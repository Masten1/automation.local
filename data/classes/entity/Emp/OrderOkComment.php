<?php
/**
 * 
 * Author: Andrey
 * Date: 28.10.13
 * Time: 11:48 
 */

class Emp_OrderOkComment extends fvRoot {

    function __toString(){
        return (string)$this->text;
    }

    function asAdorned() {
        return new Component_View_Comment($this);
    }

} 