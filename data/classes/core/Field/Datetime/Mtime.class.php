<?php

class Field_Datetime_Mtime extends Field_Datetime_Ctime {
    
    function isChanged(){
        return true;
    }
    
}