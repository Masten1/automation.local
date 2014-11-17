<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrey
 * Date: 11.06.13
 * Time: 10:52
 */

class Field_Float_Price extends Field_Float {

    function get() {
        if(!$this->value)
            return (float)fvSite::$fvConfig->get('sm_price');
        else return parent::get();
    }


}