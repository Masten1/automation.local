<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrey
 * Date: 11.10.13
 * Time: 10:37
 * To change this template use File | Settings | File Templates.
 */

class Emp_Direction extends fvRoot {


    function __toString(){
        return (string)$this->name;
    }

    function getName(){
        return $this->name->get();
    }


}