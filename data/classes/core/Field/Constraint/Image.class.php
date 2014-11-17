<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrey
 * Date: 26.03.13
 * Time: 15:05
 */
class Field_Constraint_Image extends Field_Constraint
{
    public $itpl;

    function __construct( array $fieldSchema, $key){
        parent::__construct( $fieldSchema, $key );
        $this->itpl = new Field_String_Image(Array(), NULL);
    }

    public function getEditMethod() {
        return self::EDIT_METHOD_CONSTRAINT_IMAGE;
    }
}
