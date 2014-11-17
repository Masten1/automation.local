<?php
/**
 * User: Cah4a
 * Date: 16.08.12
 * Time: 18:27
 */
class fvRelationLoader {

    /** @var fvRelationLoader[] $dependentRelationLoaders */
    private $dependentRelationLoaders = array();

    /** @var Relations[] $relations */
    private $relations = array();

    /**
     * Загружает добавленные через addRelation связи в массив fvRoot'ов
     * @param $entities
     */
    function load( $entities ){
        foreach( $this->relations as $relation ){
            $this->getRelatedObjects( $entities, $relation );
        }
    }

    /**
     * Загружает добавленные через addIndirectRelation связи в массив fvRoot'ов
     * @param string $alias
     * @param array $relations
     */
    private function loadDependents( $alias, array $relations ){
        if( !isset( $this->dependentRelationLoaders[$alias] ) )
            return;

        if( empty($relations) )
            return;

        $this->dependentRelationLoaders[$alias]->load( $relations );
    }


    /**
     * Добавить прямую связь к загрузке
     * @param $fieldName
     * @param null $alias
     * @param null $condition
     * @throws Exception
     */
    function addRelation( $fieldName, $alias = null, $condition = null, $conditionParams = null ){
        if( $alias && $this->hasRelation($alias) )
            throw new Exception( "Duplicate alias!" );

        $this->relations[] = new Relation( $fieldName, $alias, $condition, $conditionParams );
    }

    /**
     * Добавить не прямую связь к загрузке (Загрузка сущностей в загружаемых сущностях)
     * @param $fromAlias
     * @param $fieldName
     * @param null $alias
     * @param null $condition
     */
    function addIndirectRelation( $fromAlias, $fieldName, $alias = null, $condition = null, $conditionParams = null ){
        $this
            ->getRelationLoaderByAlias( $fromAlias, $fieldName )
            ->addRelation( $fieldName, $alias, $condition, $conditionParams );
    }

    /**
     * Возвращает существует ли прямая связь по заданному $alias
     * @param $alias
     * @return bool
     */
    function hasRelation( $alias ){
        foreach( $this->relations as $relation ){
            /** @var $relation Relation */
            if( $relation->getAlias() == $alias )
                return true;
        }

        return false;
    }

    /**
     * @param fvRoot[] $entities
     */
    private function getRelatedObjects( array $entities, Relation $relation ){
        if( empty($entities) )
            return array();

        /** @var $entity fvRoot */
        $entity = $this->getFirstEntityFromArray( $entities );

        if( ! $entity instanceof fvRoot )
            throw new Exception( "Can't load Related Objects to not fvRoot array");

        $field = $entity->getField( $relation->getFieldName() );

        if( $field instanceof Field_Constraint ){
            /** @var $field Field_Constraint */
            $manager = fvManagersPool::get( $field->getForeignEntity() );
            $key = $field->getForeignEntityKey();

            $ids = $this->getPks( $entities );
            if (!($alias = $relation->getAlias())) {
                $alias = 'root';
            }

            $query = $manager->query( $alias )->whereIn($key, $ids)->aggregateBy($key);

            $relation->useWhere($query);

            $relations = $query->execute();
            $this->loadDependents( $relation->getAlias(), $relations );
            $this->fillRelatedObjectsToConstraint( $entities, $relations, $relation );
            return true;
        }

        if( $field instanceof Field_References ){
            /** @var $field Field_References */
            $manager = fvManagersPool::get( $field->getForeignEntity() );
            $currentKey = $field->getCurrentEntityKey();
            $foreignKey = $field->getForeignEntityKey();
            $refTableName = $field->getReferenceTableName();

            $ids = $this->getPks( $entities );

            if( !empty($ids) ){
                $ids = implode( ",", $ids );
                $relations = fvSite::$pdo->query("SELECT {$currentKey}, {$foreignKey} FROM {$refTableName} WHERE {$currentKey} IN ({$ids})")->fetchAll( PDO::FETCH_GROUP | PDO::FETCH_COLUMN );

                $foreignKeys = array();
                foreach( $relations as $foreignArray ){
                    foreach( $foreignArray as $foreignKey ){
                        $foreignKeys[$foreignKey] = null;
                    }
                }
                $foreignObjs = $manager->select()
                    ->whereIn( 'root.'.$manager->getRootObj()->getPkName(), array_keys($foreignKeys) )
                    ->aggregateBy($manager->getRootObj()->getPkName() )
                    ->execute();

                foreach( $relations as &$foreignArray ){
                    foreach( $foreignArray as &$foreign ){
                        $foreign = $foreignObjs[$foreign];
                    }
                }

                $this->loadDependents( $relation->getAlias(), $relations );
                $this->fillRelatedObjectsToConstraint( $entities, $relations, $relation );
            }

            return true;
        }

        if( $field instanceof Field_Foreign ){
            $ids = $this->getValues( $entities, $relation->getFieldName() );

            /** @var $field Field_Foreign */
            $manager = fvManagersPool::get( $field->getForeignEntityName() );
            $query = $manager->select()
                ->whereIn('root.'.$manager->getRootObj()->getPkName(), $ids )
                ->aggregateBy($manager->getRootObj()->getPkName());

            $relations = $query->execute();
            $this->loadDependents( $relation->getAlias(), $relations );
            $this->fillRelatedObjectsToForeign( $entity, $relations, $relation );
            return true;
        }

        throw new Exception( "Can't load related objects by unknown field type " . get_class($field) );
    }

    private function getPks( array $entites ){
        $ids = array();

        foreach( $entites as $entity ){
            if( is_array($entity) )
                $ids = array_merge( $ids, $this->getPks( $entity ) );

            if( $entity instanceof fvRoot ){
                /** @var $entity fvRoot */
                $ids[] = $entity->getPk();
            }
        }

        return $ids;
    }

    private function getValues( array $entites, $fieldName ){
        $ids = array();

        foreach( $entites as $entity ){
            if( is_array($entity) )
                $ids = array_merge( $ids, $this->getValues( $entity, $fieldName ) );

            if( $entity instanceof fvRoot ){
                /** @var $entity fvRoot */
                $ids[] = $entity->getField($fieldName)->get();
            }
        }

        return $ids;
    }

    private function getFirstEntityFromArray( $entites ){
        if( is_array($entites) ){
            return $this->getFirstEntityFromArray( current($entites) );
        }

        if( $entites instanceof fvRoot )
            return $entites;

        return null;
    }

    private function fillRelatedObjectsToConstraint( array $entities, array $relations, Relation $relation ){
        if( empty($entities) )
            return;

        if( is_array(current($entities)) ){
            foreach( $entities as $entitiesArray ){
                $this->fillRelatedObjectsToConstraint( $entitiesArray, $relations, $relation );
            }
            return;
        }

        /** @var $entity fvRoot */
        foreach( $entities as $entity ){
            /** @var $constraint Field_Constraint */
            $constraint = $entity->getField($relation->getFieldName());
            if (!empty($relations[$entity->getPk()])){
                $currentRelation = $relations[$entity->getPk()];
            } else {
                $currentRelation = array();
            }

            $constraint->loadCache( md5(''), $currentRelation ); // @todo: Дописать мазанный CACHEKEY
        }
    }

    private function fillRelatedObjectsToForeign( fvRoot $entity, array $relations, Relation $relation ){
        $field = $entity->getField($relation->getFieldName());
        $field->preloadCache( $field->getForeignEntityName(), $relations );
    }

    /**
     * @param $alias
     * @return fvRelationLoader
     */
    private function getRelationLoaderByAlias( $alias, $throwException = true ){

        if( $this->hasRelation( $alias ) ){
            if( !isset($this->dependentRelationLoaders[$alias]) )
                $this->dependentRelationLoaders[$alias] = new fvRelationLoader;

            return $this->dependentRelationLoaders[$alias];
        }

        foreach( $this->dependentRelationLoaders as $relationLoader ){
            $loader = $relationLoader->getRelationLoaderByAlias( $alias, false );
            if( $loader instanceof fvRelationLoader ){
                return $loader;
            }
        }

        if( $throwException )
            throw new Exception("Unknown relation alias {$alias}");

        return false;
    }

}


class Relation {
    private $condition, $fieldName, $alias, $conditionParams;

    function __construct( $fieldName, $alias = null, $condition = null, $conditionParams = null ){
        $this->setCondition($condition);
        $this->setConditionParams($conditionParams);
        $this->setFieldName($fieldName);
        $this->setAlias($alias);
    }

    public function useWhere( fvQuery $query ){
        $condition = $this->getCondition();

        if( is_null($condition) )
            return;

        if( is_string($condition) ){
            $query->andWhere( $condition, $this->getConditionParams() );
            return;
        }

        if( is_callable($condition) ){
            /** @var $condition Closure */
            $condition( $query );
            return;
        }

        throw new Exception( "Unknown where condition for relation with type " . gettype($condition) );
    }

    protected function setAlias( $alias ) {
        $this->alias = $alias;
        return $this;
    }

    public function getAlias() {
        return $this->alias;
    }

    protected function setCondition( $condition ) {
        $this->condition = $condition;
        return $this;
    }

    public function getCondition() {
        return $this->condition;
    }

    protected function setFieldName( $fieldName ) {
        $this->fieldName = $fieldName;
        return $this;
    }

    public function getFieldName() {
        return $this->fieldName;
    }

    protected function setConditionParams( $conditionParams ) {
        $this->conditionParams = $conditionParams;
        return $this;
    }

    public function getConditionParams() {
        return $this->conditionParams;
    }
}