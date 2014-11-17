<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrey
 * Date: 11.10.13
 * Time: 10:37
 * To change this template use File | Settings | File Templates.
 */

class Emp_ServiceToOffer extends fvRoot {


    function __toString(){
        return (string)$this->priceFirst." - ".$this->offer->direction->name->get()." ".$this->offer->getDuration();
    }

    function render($type) {
        return new Component_View($this, $type);
    }

    function getPrice() {
        return $this->priceFirst->get();
    }

    function getTouristsByInsurance(Emp_Tour $tour) {
        return Emp_Tourist::getManager()
            ->select()
            ->join("orders")
            ->join("orders.tour", "tour")
            ->join("orders.services", "services")
            ->andWhere(array("services.id" => $this->getPk(), "tour.id" => $tour->getPk()))
            ->fetchAll();
    }

    function delete()
    {
        $this->isActive->set(0);
        $this->save();
    }

}