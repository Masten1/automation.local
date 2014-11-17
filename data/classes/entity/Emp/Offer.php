<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrey
 * Date: 11.10.13
 * Time: 10:37
 * To change this template use File | Settings | File Templates.
 */

class Emp_Offer extends fvRoot {


    function __toString(){
        return $this->direction." ".$this->getDuration();
    }

    function getDuration(){

        switch($this->duration->get() % 10) {

            case 1 : $text = "день"; break;
            case 2;
            case 3;
            case 4: $text = "дня"; break;
            default: $text = "дней";
        }

        return $this->duration->get()." ".$text;
    }

    function getServiceCategories() {
        return Emp_ServiceCategory::getManager()
            ->select()
            ->join("services")
            ->join("services.toOffers", "tooffers")
            ->where(array("tooffers.offerId" => $this->getPk()))
            ->fetchAll();
    }
}