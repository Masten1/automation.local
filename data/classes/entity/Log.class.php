<?php

class Log extends fvRoot {
    
    const OPERATION_INSERT = 'insert';
    const OPERATION_DELETE = 'delete';
    const OPERATION_UPDATE = 'update';
    const OPERATION_ERROR  = 'error';
    
    static function getEntity(){ return __CLASS__; }
    
}

?>
