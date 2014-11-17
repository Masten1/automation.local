<?php
/**
 *
 * Author: Andrey
 * Date: 10.12.13
 * Time: 18:39
 */

class Emp_VehicleSeat extends fvRoot {

    function __toString(){
        return $this->number->get();
    }

}