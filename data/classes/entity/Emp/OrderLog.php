<?php
/**
 * Class: Emp_OrderLog
 * @author: Andrey
 * Date: 11.10.13
 * Time: 10:47 
 */

class Emp_OrderLog extends fvRoot {

    function __toString(){
        return (string)$this->comment;
    }
}