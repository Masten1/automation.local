<?php
/**
 * Class: Emp_TouristPhone
 * @author: Andrey
 * Date: 11.10.13
 * Time: 10:47 
 */

class Emp_TourComment extends fvRoot {

    function __toString(){
        return (string)$this->text;
    }

    function asAdorned() {
        return new Component_View_Comment( $this );
    }

    function render($type) {
        return new Component_View_Comment($this, $type);
    }
}