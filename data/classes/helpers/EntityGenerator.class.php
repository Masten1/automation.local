<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 23.05.12
 * Time: 15:55
 */
class EntityGenerator
{
    private $entityConfig;
    private $entityName;
    private $overwrite;

    private $code = '';
    function __construct($entityName, $overwrite = false) {
        $this->entityName = $entityName;
        $this->entityConfig = fvSite::$fvConfig->get("entities.{$entityName}");
        $this->overwrite = $overwrite;

        $this->executeSql("SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0");
    }

    function __destruct() {
        $this->executeSql("SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS");
    }

    public function generateClass() {
        $filename = fvSite::$fvConfig->get('path.entity').$this->entityName.'.class.php';
        if (!$this->overwrite && file_exists($filename)) {
            throw new EntityGeneratorException("File $filename already exists");
        }

        $this->code = '';
        $this->addLine('<?php');
        $this->addLine('/**');
        $this->addLine(' * Created by EntityGenerator');
        $this->addLine(" * Date: ".date('Y.m.d'));
        $this->addLine(" * Time: ".date('H:i'));

        $this->addLine();
        $this->addLine(' field definitions');
        foreach ($this->getFieldsForClass() as $fieldName => $className) {
            $this->addLine(" * @property $className \$$fieldName");
        }

        if ($this->entityConfig['foreigns']) {
            $this->addLine();
            $this->addLine(' foreign definitions');
            foreach ($this->entityConfig['foreigns'] as $fieldName => $fieldParams) {
                $this->addLine(" * @property Field_Foreign \$$fieldName");
            }
        }

        if ($this->entityConfig['references']) {
            $this->addLine();
            $this->addLine(' reference definitions');
            foreach ($this->entityConfig['references'] as $fieldName => $fieldParams) {
                $this->addLine(" * @property Field_References \$$fieldName");
            }
        }

        if ($this->entityConfig['constraints']) {
            $this->addLine();
            $this->addLine(' constraint definitions');
            foreach ($this->entityConfig['constraints'] as $fieldName => $fieldParams) {
                $this->addLine(" * @property Field_Constraint \$$fieldName");
            }
        }

        $this->addLine(" */");
        $this->addLine("class {$this->entityName} extends fvRoot ");
        $this->addLine('{');
        $this->addLine();
        $this->addLine('    function getEntity(){ return __CLASS__; }');
        $this->addLine();
        $this->addLine('}');

        file_put_contents($filename, $this->code);
        $this->code = '';
    }

    private function getEntityFields() {
        $fields = $this->entityConfig['fields'];
        /*foreach ($this->entityConfig['implements'] as $implementName) {
            $fields = array_merge($fields, $this->getImplementFields($implementName));
        }*/
        return $fields;
    }

    private function getFieldsForClass() {
        $fields = array();
        $function = create_function( '$matches', 'return "_" . strtoupper($matches[1]);' );
        foreach ($this->getEntityFields() as $fieldName => $fieldParams) {
            $type = preg_replace_callback( "/_(\w)/", $function, ucfirst( $fieldParams[ 'type' ] ) );
            $fields[$fieldName] = "Field_$type";
        }
        return $fields;
    }

    private function getImplementFields($implementName) {
        $implementation = fvSite::$fvConfig->get("abstract.{$implementName}");
        if (empty($implementation)) {
            throw new Exception("can not find abstract implementation $implementName");
        }
        $fields = $implementation['fields'];
        if (!empty($implementation['implements'])) {
            foreach ($implementation['implements'] as $subImplement) {
                $fields += $this->getImplementFields($subImplement);
            }
        }
        return $fields;
    }

    private function addLine($line='') {
        $this->code .= $line."\r\n";
    }

    public function generateTables(){
        $className = $this->entityName;
        /**
         * @var fvRoot $entity
         */
        $entity = new $className;
        if (!$entity instanceof $className) {
            throw new EntityGeneratorException("Can not find class $className");
        }
        if ($this->overwrite) {
            $this->dropTables($entity);
        }
        $this->generateEntityTable($entity);

        if ($entity->isLanguaged()) {
            $this->generateLanguageTable($entity);
        }

        foreach ($entity->getFields('Field_References') as $field) {
            if (get_class($field) == 'Field_References') {
                //var_dump($field);
                /** @var  Field_References $field */
                $refTableName = $field->getReferenceTableName();
                //var_dump('$refTableName'.$refTableName);
                if (!$this->isTableExists($refTableName)) {
                    $this->generateReferenceTable($field);
                }
            }
        }
    }

    /**
     * @param fvRoot $entity
     */
    private function dropTables($entity) {
        $this->dropTable($entity->getTableName());

        if ($entity->isLanguaged()) {
            $this->dropTable($entity->getLanguageTableName());
        }

        foreach ($entity->getFields('Field_References') as $field) {
            if (get_class($field) == 'Field_References') {
                $this->dropTable($field->getReferenceTableName());
            }
        }
    }

    /**
     * @param string $tableName
     */
    private function dropTable($tableName) {
        if ($this->isTableExists($tableName)) {
            $this->executeSql("DROP TABLE `{$tableName}`");
        }
    }


    /**
     * @param fvRoot $entity
     * @return string
     * @throws EntityGeneratorException
     */
    private function generateEntityTable($entity) {
        $tableName = $entity->getTableName();
        $this->checkTable($tableName);

        $this->code = '';
        $this->addLine("CREATE TABLE `{$tableName}` (");
        $this->addLine("`{$entity->getPkName()}` int(11) unsigned NOT NULL AUTO_INCREMENT,");

        foreach ($entity->getFields() as $fieldName=>$fieldDescription ) {
            if (!$fieldDescription->isLanguaged() && !($fieldDescription instanceof Field_Constraint) && !($fieldDescription instanceof Field_References) ) {
                $this->addLine("`$fieldName` ".$fieldDescription->getSqlPart().",");
            }
        }
        $this->addLine("PRIMARY KEY (`id`)");
        $this->addLine(") ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        return $this->executeSql();
    }

    /**
     * @param fvRoot $entity
     * @return string
     * @throws EntityGeneratorException
     */
    private function generateLanguageTable($entity) {
        $tableName = $entity->getLanguageTableName();

        $this->checkTable($tableName);

        $this->code = '';
        $this->addLine("CREATE TABLE `{$tableName}` (");
        $this->addLine("`id` int(11) unsigned NOT NULL,");
        $this->addLine("`languageId` int(11) unsigned NOT NULL,");
        foreach ($entity->getFields() as $fieldName=>$fieldDescription ) {
            if ($fieldDescription->isLanguaged()) {
                $this->addLine("`$fieldName` ".$fieldDescription->getSqlPart().",");
            }
        }
        $this->addLine("PRIMARY KEY (`id`,`languageId`)");
        $this->addLine(") ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        return $this->executeSql();
    }



    /**
     * @param Field_References $fieldReferences
     * @return string
     * @throws EntityGeneratorException
     */
    private function generateReferenceTable($fieldReferences) {
        $tableName = $fieldReferences->getReferenceTableName();

        $this->checkTable($tableName);

        $this->code = '';
        $this->addLine("CREATE TABLE `{$tableName}` (");
        $this->addLine("`{$fieldReferences->getCurrentEntityKey()}` int(11) unsigned NOT NULL,");
        $this->addLine("`{$fieldReferences->getForeignEntityKey()}` int(11) unsigned NOT NULL,");
        $this->addLine("PRIMARY KEY (`{$fieldReferences->getCurrentEntityKey()}`,`{$fieldReferences->getForeignEntityKey()}`)");
        $this->addLine(") ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        return $this->executeSql();
    }


    private function executeSql($sql=false) {
        if (!$sql) {
            $sql = $this->code;
            $this->code = '';
        }
        //var_dump($sql);
        fvSite::$pdo->query($sql);
        return $sql;
    }

    private function isTableExists($tableName) {
        $tableList = fvSite::$pdo->getAssoc("SHOW TABLES LIKE '$tableName'");
        return !empty($tableList);
    }

    private function checkTable($tableName) {
        if ($this->isTableExists($tableName)) {
            throw new EntityGeneratorException("Table $tableName already exists");
        }
        return true;
    }

    public function generateForeignKeys(){
        $className = $this->entityName;
        /**
         * @var fvRoot $entity
         */
        $entity = new $className;
        if (!$entity instanceof $className) {
            throw new EntityGeneratorException("Can not find class $className");
        }
        /*
        if ($this->overwrite) {
            $this->clearAllForeignKeys($entity);
        }*/

        $this->generateEntityForeignKeys($entity);

        if ($entity->isLanguaged()) {
            $this->generateLanguageForeignKeys($entity);
        }

        foreach ($entity->getFields('Field_References') as $field) {
            if (get_class($field) == 'Field_References') {
                $refTableName = $field->getReferenceTableName();
                    $this->generateReferenceForeignKeys($field, $entity);
            }
        }
    }

    /**
     * @param fvRoot $entity
     * @deprecated
     */
    private function clearAllForeignKeys($entity) {
        $this->clearForeignKeys($entity->getTableName());

        if ($entity->isLanguaged()) {
            $this->clearForeignKeys($entity->getLanguageTableName());
        }

        foreach ($entity->getFields('Field_References') as $field) {
            if (get_class($field) == 'Field_References') {
                $this->clearForeignKeys($field->getReferenceTableName());
            }
        }
    }
    /**
     * @param string $tableName
     * @deprecated
     */
   private function clearForeignKeys($tableName) {
   foreach ($this->getForeignKeys($tableName) as $key) {
            $sql = "ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$key['CONSTRAINT_NAME']}`" ;
            $this->executeSql($sql);
        }
    }

    /**
     * @param fvRoot $entity
     */
    private function generateEntityForeignKeys($entity) {
        $tableName = $entity->getTableName();
        foreach ($entity->getFields('Field_Constraint') as $field) {
            if (get_class($field) == 'Field_Constraint') {
                $this->createForeignKey(
                    $field->getForeignEntityTableName(),
                    $field->getForeignEntityKey(),
                    $tableName,
                    $entity->getPkName(),
                    true
                );
            }
        }

        foreach ($entity->getFields('Field_Foreign') as $field) {
            $this->createForeignKey(
                $tableName,
                $field->getKey(),
                $field->getForeignEntityTableName(),
                $entity->getPkName(),
                true
            );
        }
    }

    /**
     * @param fvRoot $entity
     */
    private function generateLanguageForeignKeys($entity) {
        $tableName = $entity->getLanguageTableName();

        $this->createForeignKey(
            $tableName,
            'id',
            $entity->getTableName(),
            $entity->getPkName(),
            true
        );

        $lang = new Language();
        $this->createForeignKey(
            $tableName,
            'languageId',
            $lang->getTableName(),
            $lang->getPkName(),
            true
        );
    }
    /**
     * @param Field_References $fieldReferences
     * @param fvRoot $entity
     * @throws EntityGeneratorException
     */
    private function generateReferenceForeignKeys($fieldReferences, $entity) {
        $tableName = $fieldReferences->getReferenceTableName();

        $this->createForeignKey(
            $tableName,
            $fieldReferences->getCurrentEntityKey(),
            $entity->getTableName(),
            $entity->getPkName(),
            true
        );


        $this->createForeignKey(
            $tableName,
            $fieldReferences->getForeignEntityKey(),
            $fieldReferences->getForeignEntityTableName(),
            $fieldReferences->getForeignEntityPkName(),
            true
        );
    }


    private function createForeignKey($table, $id, $foreignTable, $foreignId, $addIndex=true, $onDelete="CASCADE", $onUpdate="CASCADE") {
        if (!$this->isTableExists($table)) {
            throw new Exception("Can not find table $table");
        }
        if (!$this->isTableExists($foreignTable)) {
            throw new Exception("Can not find table $foreignTable");
        }

        if ($key = $this->getForeignKey($table, $id, $foreignTable, $foreignId)) {
            if ($this->overwrite) {
                $this->deleteForeignKey($table, $key);
            } else {
                throw new EntityGeneratorException("Foreing key $key already exists");
            }
        }

        $keyName = "FK_{$table}_{$foreignTable}";
        $counter = 1;
        while ($this->isForeignKeyExist($keyName)){
            $counter++;
            $keyName = "FK_{$table}_{$foreignTable}{$counter}";
        }

        $sql = "  ALTER TABLE `{$table}`
                  ADD CONSTRAINT `{$keyName}`
                  FOREIGN KEY (`{$id}` )
                  REFERENCES `{$foreignTable}` (`{$foreignId}` )
                  ON DELETE {$onDelete}
                  ON UPDATE {$onUpdate}";

        if ($addIndex) {
            //$sql .= "\r\n, ADD INDEX `{$keyName}` (`{$id}` ASC)";
        }

        $sql .= ";";
        try {
            $this->executeSql($sql);
        } catch (Exception $e) {
            throw new EntityGeneratorException("Cannot create key. Query: $sql Error{$e->getMessage()}");
        }
    }

    private function getForeignKey($table, $id, $foreignTable, $foreignId) {
        $sql = "select *
                from
                    information_schema.key_column_usage
                where
                    TABLE_NAME = '{$table}'
                    AND
                    COLUMN_NAME = '{$id}'
                    AND
                    REFERENCED_TABLE_NAME = '{$foreignTable}'
                    AND
                    REFERENCED_COLUMN_NAME = '{$foreignId}'";

        $keyList = fvSite::$pdo->getAssoc($sql);
        if (!empty($keyList)) {
            $key = reset($keyList);
            return $key['CONSTRAINT_NAME'];
        } else {
            return false;
        }
    }

    private function isForeignKeyExist($keyName) {
        $sql = "select *
                from
                    information_schema.key_column_usage
                where
                    CONSTRAINT_NAME = '{$keyName}'";

        $keyList = fvSite::$pdo->getAssoc($sql);
        return !empty($keyList);
    }

    private function deleteForeignKey($tableName, $keyName) {
            $sql = "ALTER TABLE `{$tableName}` DROP FOREIGN KEY `$keyName`" ;
            $this->executeSql($sql);
    }

    /**
     * @param string $tableName
     * @return array
     * @throws EntityGeneratorException
     */
    private function getForeignKeys($tableName) {
        if (!$this->isTableExists($tableName)) {
            throw new Exception("Can not find table $tableName");
        }
        $sql = "select *
                from
                    information_schema.key_column_usage
                where
                    TABLE_NAME = '{$tableName}'
                    AND CONSTRAINT_NAME != 'PRIMARY'";
        $keys =fvSite::$pdo->getAssoc($sql);
        $res = array();
        foreach ($keys as $key) {
            $res[] = $key['CONSTRAINT_NAME'];
        }
        return $res;
    }


    public function generateAll() {
        $this->generateClass();
        $this->generateTables();
        $this->generateForeignKeys();
    }
}

class EntityGeneratorException extends Exception{}
