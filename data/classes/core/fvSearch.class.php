<?php

class fvSearch {

    private $entities = array();

    function __construct( array $entities ){
        $this->entities = $entities;
    }

    function search( $needle ){
        $resutl = array();
        foreach( $this->entities as $entity => $search_fiedls ){
            $manager = fvManagersPool::get($entity);
            $fields = array();

            foreach( $search_fiedls as $field ){
                $fields[] = "$field LIKE '%{$needle}%'";
            }

            $resutl[$entity] = $manager->getAll(implode(" OR ", $fields));
        }
        return $resutl;
    }

}