<?php

class Field_Datetime_Ctime extends Field_Datetime {
    
    function asMysql(){
        return date('Y-m-d H:i:s');
    }
    
    function isChanged(){
        return is_null( $this->get() );
    }

    function asAdorned(){
        if(!$this->get())
            return '<nobr>' . date('d.m.y') . ' <small>' . date('H:i') . '</small></nobr>';
        return '<nobr>' . date('d.m.y', $this->asTimestamp()) . ' <small>' . date('H:i', $this->asTimestamp()) . '</small></nobr>';
    }
}