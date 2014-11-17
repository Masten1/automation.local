<?php 
class EImageNoSourceException extends EImageException{
    public function __construct() {
        parent::__construct(null, Field_String_File::NO_SOURCE);
    }
}
