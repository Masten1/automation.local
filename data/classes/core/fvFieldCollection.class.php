<?php

abstract class fvFieldCollection {

    /**
     *
     * @var fvField[] $_fields
     */
    protected $_fields = Array( );
    protected $_fieldNames = [];

    protected function updateFields( array $schema ) {
        $function = create_function( '$matches', 'return "_" . strtoupper($matches[1]);' );

        $new_fields = array( );

        foreach ( $schema as $name => $fieldSchema ) {
            if ( isset( $this->_fields[ $name ] ) ) {
                $this->_fields[ $name ]->updateSchema( $fieldSchema );
            }
            else {
                $type = preg_replace_callback( "/_(\w)/", $function, ucfirst( $fieldSchema[ 'type' ] ) );
                $className = 'Field_' . $type;
                if( !$type ) echo $name;
                //$this->_fields[$name] = new $className($fieldSchema, $name);
                $new_fields[ $name ] = new $className( $fieldSchema, $name );
            }
        }

        $this->_fields = array_merge_recursive( $new_fields, $this->_fields );

//        fvDebug::debug( $this->_fields );
    }

    public function __get( $name ) {
        if ( !isset( $this->_fields[ $name ] ) ) {
            throw new EFieldError( "Trying to get field '{$name}' wich does not implement in schema." );
        }

        return $this->_fields[ $name ];
    }

    public function __set( $name, $value ) {
        if ( !isset( $this->_fields[ $name ] ) ) {
            $this->_fields[ $name ] = new Field_Heap(array(), $name);
            //throw new EFieldError( "Trying to set field '{$name}' wich does not implement in schema." );
        }

        return $this->_fields[ $name ]->set( $value );
    }

    function getFieldList() {
        return array_keys( $this->_fields );
    }

    /**
     * @param null $type
     * @param null $parameter
     * @return array|fvField[]
     */
    public function getFields( $type = null, $parameter = null ) {
        $fieldCollection = $this->_fields;

        if ( $type ) {
            $result = Array( );

            foreach ( $fieldCollection as $keyName => $field ) {
                if ( is_a( $field, $type ) )
                    $result[ $keyName ] = $field;
            }

            $fieldCollection = $result;
        }
        // Отобрать по параметру
        if ( !is_null( $parameter ) ) {
            $result = Array( );

            foreach ( $fieldCollection as $keyName => $field ) {
                if ( $field->checkProperty( $parameter ) )
                    $result[ $keyName ] = $field;
            }
            $fieldCollection = $result;
        }


        return $fieldCollection;
    }

    function isValid() {
        foreach ( $this->getFields() as $field ) {
            if ( !$field->isValid() )
                return false;
        }

        return true;
    }

    /**
     * Fill fields by array (fieldName => fieldValue)
     * @param string $map
     */
    function hydrate( array $map ) {
        try {
            if ( !is_array( $map ) )
                throw new EModelError( "Can't create object from non array" );
            foreach ( $map as $field => $value ) {
                if ($this->hasFieldName($field)){
                    if (!$this->hasField($field)) {
                        $this->_fields[$field] = new Field_Heap(array(), $field);
                    }
                    $this->_fields[$field]->set($value);
                }
            }
        }
        catch ( EFieldError $e ) {
            throw new EModelError( "Field {$field} throw error: " . $e->getMessage() );
        }
    }

    function setFieldNames(array $schema)
    {
        foreach ((array)$schema['fields'] as $name => $field) {
            $this->_fieldNames[] = $name;
        }
        foreach ((array)$schema['foreigns'] as $field) {
            $this->_fieldNames[] = $field['key'];
        }
    }

    function hasFieldName($field)
    {
        return in_array($field, $this->_fieldNames);
    }

    function hasField( $fieldName ) {
        return isset( $this->_fields[ $fieldName ] );
    }

    function toHash() {
        $result = array( );
        foreach ( $this->_fields as $name => $field ) {
            $result[ $name ] = ( string ) $field;
        }

        return $result;
    }

    function __clone() {
        foreach ( $this->_fields as &$field ) {
            $field = clone $field;
        }
    }

    function setChanged( $value ) {
        foreach ( $this->_fields as $field )
            $field->setChanged( $value );
    }

    // todo write this fucken getValidationResult method!

    public function getValidationResult() {
        $valid = array( );

        foreach ( $this->_fields as $fieldName => $field ) {
            if ( $field->isLanguaged() ) {
                $langs = Language::getManager()->getAll( " isActive = 1 " );
                foreach ( $langs as $lang ) {
                    $field->setLanguage( $lang->code->get() );
                    if ( !$field->isValid() ) {
                        $valid[ $lang->code->get() . $fieldName ] = $field->getValidationMessage(  );
                        $valid[ "l" . $lang->code->get() . $fieldName ] = $field->getValidationMessage( );
                    }
                }
            }
            else
                if ( !$field->isValid() ) {
                    $valid[ $fieldName ] = $field->getValidationMessage(  );
                    $valid[ "l" . $fieldName ] = $field->getValidationMessage(  );
                }
        }

        return $valid;
    }

    public function getShortValidation(){
        $valid = array();
        foreach ($this->_fields as $fieldName => $field){
            if(!$field->isValid()){
                $valid[$fieldName] = $field->getValidationMessage();
            }
        }
        return $valid;
    }

}
