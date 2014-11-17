<?php

class Field_String_Articul extends Field_String {

    public function __construct( $fieldSchema, $name) {
        $this->key = $name;
        $this->updateSchema( $fieldSchema );
        $this->setDefaultValue();
    }


    function get() {
        if(isset($this->value)) return $this->value;
        else return false;
    }

}