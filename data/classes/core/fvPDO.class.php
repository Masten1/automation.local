<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 18.04.12
 * Time: 20:02
 */
class fvPDO extends Pdo
{
    protected $hasActiveTransaction = false;

    function __construct($dsn, $user, $pass) {
        parent::__construct($dsn, $user, $pass);
        /*** set the error reporting attribute ***/
        parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        parent::query('set names utf8')->execute();
    }

    function beginTransaction () {
        $this->hasActiveTransaction = parent::beginTransaction ();
        return $this->hasActiveTransaction;
    }

    function commit () {
        parent::commit ();
        $this->hasActiveTransaction = false;
    }

    function rollback () {
        parent::rollback ();
        $this->hasActiveTransaction = false;
    }

    public function isTransactionOpen(){
        return $this->hasActiveTransaction;
    }

    public function update($tableName, $updateValues, $where, $whereParams) {
        //$updateValues = self::prepareSetParams(array_merge($updateValues));
        $updateString = '';
        foreach ($updateValues as $field=>$value) {
            $updateString .= "`$field` = :$field, ";
        }
        $updateString = substr($updateString, 0, -2);

        if( is_array($where) )
            $where = implode( " AND ", $where );

        $sql = "UPDATE `{$tableName}` SET $updateString WHERE " . $where;

        $this->queryPrepared($sql, array_merge($updateValues, $whereParams));
    }

    public function insert($tableName, $values) {
        $sql = "INSERT INTO `{$tableName}` (".implode(self::sanitizeFieldNames(array_keys($values)), ',').") VALUES (:".implode(array_keys($values), ',:').")";
        $this->queryPrepared($sql, $values);
    }

    public function delete($tableName, $where, $whereParams) {
        $sql = "DELETE FROM `{$tableName}` WHERE " . implode( " AND ", $where );
        $this->queryPrepared($sql, $whereParams);
    }

    public function getAssoc($sql, $fetchType = PDO::FETCH_ASSOC) {
        return parent::query($sql)->fetchAll($fetchType);
    }

    public function getOne($sql) {
        return parent::query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function queryPrepared($sql, $params) {
        parent::prepare($sql)->execute( self::prepareSetParams($params));
    }

    public static function prepareSetParams($params) {
        $result = array();
        foreach( $params as $field => $value ){
            $key = ":" . preg_replace( "/[^\d\w]/", "", $field );
            $result[$key] = $value;
        }
        return $result;
    }

    public static function sanitizeFieldNames($names) {
        if (is_array($names)) {
            foreach( $names as &$name ) {
                $name = "`$name`";
            }
            return $names;
        } else {
            return "`$names`";
        }
    }
}
