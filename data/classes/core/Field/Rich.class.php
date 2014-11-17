<?php

class Field_Rich extends Field_Textarea {
    
    function getEditMethod() {
        return self::EDIT_METHOD_RICH;
    }
}