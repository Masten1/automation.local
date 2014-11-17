<?php
/**
 * 
 * Author: Andrey
 * Date: 22.10.13
 * Time: 15:29 
 */

class Emp_AdditionalService extends fvRoot {

    function __toString(){
        return (string)$this->price." - ".$this->comment;
    }

    function asAdorned(){
        return new Component_View($this);
    }

    function getPrice() {
        return $this->price->get();
    }
}