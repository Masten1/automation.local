<?php
/**
 * Class: Emp_TouristPhone
 * @author: Andrey
 * Date: 11.10.13
 * Time: 10:47 
 */

class Emp_OrderComment extends fvRoot {

    function __toString(){
        return (string)$this->text;
    }

    function asAdorned() {
        return new Component_View_Comment( $this );
    }

    function save() {
        if($this->isNew()){
            $this->managerId = fvSite::$fvSession->getUser()->getPk();
        }
        return parent::save();
    }
}