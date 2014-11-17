<?php

class Dictionary extends fvRoot{
    static function getEntity(){ return __CLASS__; }

    function save( $logging = true ){
        parent::save( $logging );

        //fvDictionary::dropCache();
    }

    function delete(){
        parent::delete();

        //fvDictionary::dropCache();
    }
}

?>
