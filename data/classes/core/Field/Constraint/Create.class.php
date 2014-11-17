<?php
    /**
     * User: cah4a, J. R. Openheimer
     * Date: 22.03.12
     * Time: 13:49
     */
    class Field_Constraint_Create extends Field_Constraint{
        protected $toCreate = Array();

        function getEditMethod(){
            return self::EDIT_METHOD_ENTITIES_LIST_READONLY;
        }

        /**
         * @override
         */
        function set( $values ){
            $foreignEntity = new $this->foreignEntity;
            if( ! $foreignEntity instanceof iForceCreatable )
                return parent::set( $values );

            if( !is_array( $values ) ){
                $values = Array( $values );
            }

            foreach( $values as $key => $value ){
                $entity = new $this->foreignEntity;
                $entity->forceCreate( $value );
                $this->toCreate[] = $entity;
            }

            return $this;
        }

        public function get(){
            $parentResult = parent::get();

            return array_merge( $parentResult, $this->toCreate );
        }

        public function save(){
            foreach( $this->toCreate as $entity ){
                $entity->{$this->foreignEntityKey} = $this->pk;
                $entity->save();
            }

            return parent::save();
        }
    }
