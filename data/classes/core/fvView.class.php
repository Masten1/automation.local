<?php

/**
* Абстрактный класс просмотра одной языковой версии сущности
* @abstract
* @author Korniev Zakhar
*/

abstract class fvView
{
    public function __construct( fvRoot $entity )
    {
        $this->extend( $entity );    
    }    
    
    private function extend( fvRoot $entity )
    {
        $tableName = $entity->getTableName() . "_ru";
        $sql = "select * from 
                {$tableName} as entity
                where entity.id = {$entity->getPk()}";   
        $result = fvSite::$pdo->getAssoc( $sql );
        $entity->hydrate( $result );
    }
}

?>
