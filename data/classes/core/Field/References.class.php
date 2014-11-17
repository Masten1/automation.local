<?php

class Field_References extends Field_Array {

    protected $foreignEntity;

    protected $toUnset = array();
    protected $toSet = array();

    protected $currentEntity;
    protected $cache = array( );
    protected $refTableName;
    protected $currentEntityKey;
    protected $foreignEntityKey;
    protected $pk;
    protected $foreignKeys;
    protected $autocomplete = false;
    protected $fromJSON = true;
    protected $method = self::EDIT_METHOD_ENTITIES_LIST;

    function __construct( array $fieldSchema, $key ) {
        parent::__construct( $fieldSchema, $key );

        $this->foreignEntity = $fieldSchema[ 'entity' ];
        /*
          $entity = new $fieldSchema['entity'];
          $this->foreignEntityPkName = $entity->getPkName();
         */
        $this->currentEntity = $fieldSchema[ 'currentEntity' ];

        $ent = array( $this->foreignEntity, $this->currentEntity );
        sort( $ent );

        if ( $fieldSchema[ 'table' ] )
            $this->refTableName = $fieldSchema[ 'table' ];
        else
            $this->refTableName = fvSite::$fvConfig->get( 'database.db_prefix' ) . to_camel_case( $ent[ 0 ] ) . 'To' . to_camel_case( $ent[ 1 ] );

        if ( $fieldSchema[ 'local_key' ] )
            $this->currentEntityKey = $fieldSchema[ 'local_key' ];
        else
            $this->currentEntityKey = mb_strtolower( $this->currentEntity, "utf-8" ) . 'Id';

        if ( $fieldSchema[ 'foreign_key' ] )
            $this->foreignEntityKey = $fieldSchema[ 'foreign_key' ];
        else
            $this->foreignEntityKey = mb_strtolower( $this->foreignEntity, "utf-8" ) . 'Id';

        if ( isset( $fieldSchema[ 'autocomplete' ] ) && $fieldSchema[ 'autocomplete' ] != false ) {
            $this->method = self::EDIT_METHOD_ENTITIES_LIST_AUTOCOMPLETE;

            if ( isset( $fieldSchema[ 'fromJSON' ] ) && !$fieldSchema[ 'fromJSON' ] ) {
                $this->fromJSON = false;
            }
        }
        $this->foreignKeys = array( );

        if ( $fieldSchema['method'] )
            $this->method = $fieldSchema['method'];
    }

    function getForeignEntityPkName() {
        $entity = new $this->foreignEntity;
        return $entity->getPkName();
    }

    function isValid() {
        return true;
    }

    function getReferenceTableName() {
        return $this->refTableName;
    }

    /** @return fvRoot[] */
    function get() {
        $args = func_get_args();
        $cacheKey = md5( implode( ",", $args ) );

        if ( !isset( $this->cache[ $cacheKey ] ) ) {
            if( empty($this->pk) )
                return array();

            $where = "{$this->getForeignEntityPkName()} IN (SELECT {$this->foreignEntityKey} FROM {$this->refTableName} WHERE {$this->currentEntityKey} = {$this->pk})";
            $args[ 0 ] = ( $args[ 0 ] ? "({$where}) AND ({$args[ 0 ]})" : $where);

            $this->loadCache($cacheKey, call_user_func_array( array( fvManagersPool::get( $this->foreignEntity ), 'getAll' ), $args ));
        }

        return $this->cache[ $cacheKey ];
    }

    function setRootPk( $value ) {
        $this->pk = $value;
        $this->cache = array();
    }

    function set( $value ){
        if( !is_array( $value ) )
            $value = (array)@unserialize($value);

        if( is_null($value) )
            $value = Array();

        if( array_search(0, $value) !== false )
            unset( $value[ array_search(0, $value) ] );

        $this->toSet = array();
        $this->toUnset = array();

        if( count($value) == 0 ){
            foreach( $this->get() as $entity ){
                $this->toUnset[] = $entity->getPk();
            }
        } elseif( $value[0] instanceof fvRoot ) {
            $entity = $this->getForeignEntity();
            $ids = array();

            /** @var $obj fvRoot */
            foreach( $value as $obj ){
                if( ! $obj instanceof fvRoot ){
                    throw new Exception("Combined array not supported to set reference field.");
                }

                if( ! $value[0] instanceof $entity )
                    throw new Exception("Can't set reference field with entity '".get_class($value[0])."' class instead of instance '{$entity}' class.");

                $ids[] = $obj->getPk();
            }

            $value = $ids;
        }

        foreach( $value as $key ){
            $key = (int)$key;
            foreach( $this->get() as $entity ){
                if( $entity->getPk() == $key ){
                    continue 2;
                }
            }
            $this->toSet[] = $key;
        }

        foreach( $this->get() as $entity ){
            foreach( $value as $key ){
                if( $entity->getPk() == $key ){
                    continue 2;
                }

            }
            $this->toUnset[] = $entity->getPk();
        }
    }

    function add( $item ){
        if( $item instanceof fvRoot ){
            /** @var $item fvRoot */
            $entityName = $this->getForeignEntityName();
            if( ! $item instanceof $entityName )
                throw new Exception("Item to remove from Field_Reference must be Primary Key or instance of {$this->getForeignEntityName()} class");

            $item = $item->getPk();
        }

        if( !is_numeric($item) ){
            throw new Exception("Item to remove from Field_Reference must be Primary Key or instance of {$this->getForeignEntityName()} class");
        }

        $item = (int)$item;

        $toUnsetKey = array_search( $item, $this->toUnset );
        if( $toUnsetKey !== false ){
            unset( $this->toUnset[$toUnsetKey] );
        }

        if($this->is($item))
        {
            return;
        }

        if( !in_array($item, $this->toSet) ){
            $this->toSet[] = $item;
        }
    }

    function is( $item ){
        if( $item instanceof fvRoot ){
            /** @var $item fvRoot */
            $entityName = $this->getForeignEntityName();
            if( ! $item instanceof $entityName )
                throw new Exception("Item to remove from Field_Reference must be Primary Key or instance of {$this->getForeignEntityName()} class");

            $item = $item->getPk();
        }

        if( in_array($item, $this->toSet) )
            return true;

        if( in_array($item, $this->toUnset) )
            return false;

        foreach( $this->get() as $obj ){
            if( $obj->getPk() == $item )
                return true;
        }

        return false;
    }

    function remove( $item ){
        if( $item instanceof fvRoot ){
            /** $item fvRoot */
            $entityName = $this->getForeignEntityName();
            if( ! $item instanceof $entityName )
                throw new Exception("Item to remove from Field_Reference must be Primary Key or instance of {$this->getForeignEntityName()} class");

            $item = $item->getPk();
        }

        if( !is_numeric($item) ){
            throw new Exception("Item to remove from Field_Reference must be Primary Key or instance of {$this->getForeignEntityName()} class");
        }

        $toSetKey = array_search( $item, $this->toSet );
        if( $toSetKey !== false ){
            unset( $this->toSet[$toSetKey] );
        }

        if( ! $this->is( $item ) ){
            return;
        }

        $toUnsetKey = array_search( $item, $this->toUnset );
        if( $toUnsetKey === false ){
            $this->toUnset[] = $item;
        }
    }

    function loadCache( $cacheKey, $entities ){
        $this->cache[ $cacheKey ] = $entities;
    }

    function getEditMethod() {
        return $this->method;
    }

    function __clone() {
        $this->cache = array( );
    }

    function getForeigns() {
        return fvManagersPool::get( $this->foreignEntity )->getAll();
    }

    function isAssigned( $key ) {

        foreach ($this->get() as $item) {
            $this->foreignKeys[] = $item->getPk();
        }

        return in_array( $key, $this->foreignKeys );
    }

    function save() {
        if( count($this->toUnset) > 0 ){
            $query = "delete from {$this->refTableName} WHERE {$this->currentEntityKey} = {$this->getPK()} AND {$this->foreignEntityKey} IN (" . implode(",", $this->toUnset) . ")";
            fvSite::$pdo->query( $query );
        }

        if( count($this->toSet) > 0 ){
            $values = "";
            foreach( $this->toSet as $key ){
                if( $key )
                    $values .= "({$this->pk},{$key}),";
            }
            if( $values ){
                $query = "insert into {$this->refTableName} ({$this->currentEntityKey}, {$this->foreignEntityKey} ) values " . rtrim($values, ",");
                fvSite::$pdo->query( $query );
            }
        }

        if( count($this->toSet) > 0 || count($this->toUnset) )
            $this->cache = array();

        $this->toUnset = array();
        $this->toSet = array();
    }

    /* привет, тут копипаста! */

    function count() {
        $args = func_get_args();
        $cacheKey = md5( "count" . implode( ",", $args ) );

        if ( !isset( $this->cache[ $cacheKey ] ) ) {
            $foreignEntity = new $this->foreignEntity;
            $where = "root.{$this->getForeignEntityPkName()} IN (SELECT {$this->foreignEntityKey} FROM {$this->refTableName} WHERE {$this->currentEntityKey} = {$this->getPK()})";
            $args[ 0 ] = $args[ 0 ] ? "({$where}) AND ({$args[ 0 ]})" : $where;

            $this->cache[ $cacheKey ] = call_user_func_array( array( fvManagersPool::get( $this->foreignEntity ), 'getCount' ), $args );
        }

        return $this->cache[ $cacheKey ];
    }

    function getPK() {
        return $this->pk;
    }

    function isFromJSON() {
        return $this->fromJSON;
    }

    /** @return fvQuery */
    function select(){
        if( empty($this->pk) )
            throw new Exception("Can't select from object doesn't saved.");

        $where = "root.{$this->getForeignEntityPkName()} IN (SELECT {$this->foreignEntityKey} FROM {$this->refTableName} WHERE {$this->currentEntityKey} = {$this->pk})";

        $manager = fvManagersPool::get( $this->foreignEntity );
        return $manager->select()->where($where);
    }

    public function getCurrentEntityKey() {
        return $this->currentEntityKey;
    }

    public function getForeignEntity() {
        return $this->foreignEntity;
    }

    public function getForeignEntityKey() {
        return $this->foreignEntityKey;
    }

    function getForeignEntityName(){
        return $this->foreignEntity;
    }

    public function getToSet() {
        return $this->toSet;
    }

    public function getToUnSet() {
        return $this->toUnset;
    }

    public function isEmpty() {
        $externalCount = count($this->toSet) + $this->count() - count($this->toUnset);
        return $externalCount == 0;
    }

    function getForeignEntityTableName() {
        $entity = new $this->foreignEntity;
        return $entity->getTableName();
    }

    function asAdorned(){
        $response = "";
        foreach( $this->get() as $entity ){
            if( method_exists( $entity, "asAdorned" ) )
                $response .= $entity->asAdorned();
            else
                $response .= (string)$entity . "<br>";
        }
        return $response;
    }
}