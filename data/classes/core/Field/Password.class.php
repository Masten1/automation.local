<?php

class Field_Password extends Field_String {
    
    function getEditMethod() {
        return self::EDIT_METHOD_PASSWORD;
    }

    function set($value){
        if( empty($value) )
            return false;

        return parent::set( sha1($value) );
    }
}