<?php

class Field_String_ListLang extends Field_String_List {

    const LANG_NONE = 0;
    const LANG_RUSSIAN = 1;
    const LANG_ENGLISH = 2;
    const LANG_GERMAN = 3;

    public static $list = array(
        self::LANG_NONE => "Not set",
        self::LANG_ENGLISH => "English",
        self::LANG_RUSSIAN => "Russian",
        self::LANG_GERMAN  => "German");

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
        return self::LANG_NONE;
    }

    public function getDefaultValue() {
        return self::$list[self::LANG_NONE];
    }
}