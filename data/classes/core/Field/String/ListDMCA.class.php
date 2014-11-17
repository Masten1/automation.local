<?php

class Field_String_ListDMCA extends Field_String_List {

    const TYPE_DMCA = 1;
    const TYPE_EU = 2;
    const TYPE_KZ = 3;
    const TYPE_RU = 4;
    const TYPE_UA = 5;
    const TYPE_DE = 6;

    public static $list = array(
        self::TYPE_DMCA => 'DMCA',
        self::TYPE_EU => 'EU',
        self::TYPE_DE => 'DE',
        self::TYPE_KZ => 'KZ',
        self::TYPE_RU => 'RU',
        self::TYPE_UA => 'UA',
    );

    function getEditMethod() {
        return self::EDIT_METHOD_LIST;
     }
    
    function getList( fvRoot $entity ){
      return self::$list;
    }

    function __toString(){
        return self::$list[$this->get()];
    }

    public static function getDefaultKey() {
        return self::TYPE_DMCA;
    }
}