<?php
/**
 * 
 * Author: Andrey
 * Date: 04.11.13
 * Time: 16:19 
 */

class Emp_TourTransport extends fvRoot {

    protected $total;
    protected $busy;

    function __toString(){
        return (string)$this->vehicle->model->get();
    }

    function asAdorned() {
        return new Component_View($this);
    }

    function render($type) {
        return new Component_View($this, $type);
    }

    function getTourists() {
        return Emp_Tourist::getManager()
            ->select()
            ->join("transports")
            ->leftJoin("phones")
            ->loadRelation("phones")
            ->where("transports.ttId = ?", $this->getPk())
            ->fetchAll();
    }

    function getBusy() {
        $count = Emp_Tourist::getManager()
            ->select()
            ->join("transports")
            ->where("transports.ttId = ?", $this->getPk())
            ->getCount();
        return $this->busy = $count;
    }

    function getFree(){
        return $this->vehicle->getTotalSeats() - $this->busy;
    }

    function getRoute() {
        $hotels = $this->getRouteHotel();
        $ret = "";
        $pk = $this->getPk();
        array_walk($hotels, function ($hotel) use (&$ret, $pk){
            $ret.="<div>{$hotel->hotel->name->get()} - {$hotel->getCountByTransport($pk)} чел. </div>";
        });
        return $ret;
    }

    function getOrders(){
        return Emp_Order::getManager()
            ->select()
            ->join("tour")
            ->join("tourists")
            ->join("tourists.transports", "trans")
            ->where(array("trans.ttId" => $this->getPk(), "tour.id" => $this->tourId->get()))
            ->fetchAll();
    }

    function getRouteHotel() {
        return Emp_TourHotel::getManager()
            ->select()
            ->join("tourists")
            ->join("tourists.tourist", "tourist")
            ->join("tourist.transports", "trans")
            ->join("trans.transport", "transport")
            ->where(array("transport.id" => $this->getPk(), "root.tourId" => $this->tourId->get()) )
            ->fetchAll();
    }

} 