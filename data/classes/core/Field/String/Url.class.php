<?php

class Field_String_Url extends Field_String {

    private $auto = 'name';

    public function __construct( $fieldSchema, $name) {
        if(isset($fieldSchema['auto']))
            $this->auto = $fieldSchema['auto'];
        $this->key = $name;
        $this->updateSchema( $fieldSchema );
        $this->setDefaultValue();
    }


    public function getEditMethod() {
        return self::EDIT_METHOD_INPUT_AUTO;
    }

    public function isValid() {
        $pattern = "/^[a-zA-Z0-9\_\-\/]{1,255}$/";
        if( $result = preg_match( $pattern, $this->value )) {
            $this->setValidationMessage("Введите корректный УРЛ");
        }

        return $result;
    }

    public function getAuto(){
        return $this->auto;
    }

}