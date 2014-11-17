<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrey
 * Date: 11.10.13
 * Time: 10:37
 * To change this template use File | Settings | File Templates.
 */

class Emp_Tour extends fvRoot {
    protected $price, $paid;

    function __toString(){
        return $this->date->asAdorned()." ".$this->offer->direction->name." ".$this->offer->getDuration();
    }


    function getTotalTourists()
    {
        return Emp_Tourist::getManager()
            ->select()
            ->join("order", 'o')
            ->where("o.tourId = {$this->getPk()}")
            ->getCount();
    }

    function getAllTourists() {
        return Emp_Tourist::getManager()->select()
            ->join("order")
            ->join("order.tour", "tour")
            ->where("tour.id = {$this->getPk()}")
            ->orderBy("order.id")
            ->fetchAll();
    }

    function getFreeVehicles(){
        $ids = array();
        foreach($this->transports->get() as $busy) {
            $ids[] = $busy->vehicle->getPk();
        }

        return Emp_Vehicle::getManager()
            ->select()
            ->whereNotIn("root.id", $ids)
            ->fetchAll();
    }

    function getFreeHotels(){
        $ids = array();
        foreach($this->hotels->get() as $busy) {
            $ids[] = $busy->hotel->getPk();
        }

        return Emp_Hotel::getManager()
            ->select()
            ->whereNotIn("root.id", $ids)
            ->fetchAll();
    }

    function getAllHotels(){
        return Emp_Hotel::getManager()
            ->select()
            ->join("direction")
            ->join("direction.offers", "offers")
            ->join("offers.mytours", "mytours")
            ->where("mytours.id = {$this->getPk()}")
            ->fetchAll();
    }

    function getNoTransportTourists(){
        $pk = $this->getPk();
        return array_filter($this->getAllTourists(), function($tourist) use($pk) {
            $transport = $tourist->getTransport($pk);
            if((!$transport instanceof fvRoot) )
                return true;
            return false;
        });
    }

    function getNoHotelTourists(){
        $pk = $this->getPk();
        return array_filter($this->getAllTourists(), function($tourist) use($pk) {
            $transport = $tourist->getHotel($pk);
            if((!$transport instanceof fvRoot) )
                return true;
            return false;
        });
    }

    function getOrderHotels($orderId) {
        $order = Emp_Order::getManager()->getByPk($orderId);
        $touristPk = array();
        array_walk($order->tourists->get(), function($tourist) use (&$touristPk){
            $touristPk[] = $tourist->getPk();
        });

        return Emp_OrderHotel::getManager()
            ->select()
            ->join("tourist")
            ->join("tourhotel")
            ->join("tourhotel.tour", "tour")
            ->where(array("tour.id" => $this->getPk()))
            ->andWhereIn("tourist.id", $touristPk);
    }

    function getPrice()
    {
        $price = array_reduce(
            $this->orders->get(),
            function ($price, $order) {
                $price += $order->getPrice();
                return $price;
            }
        );
        $this->price = $price;
        return $price;
    }

    function getPayment() {
        $sum = array_reduce(
            $this->orders->get(),
            function ($sum, $payment) {
                $sum+= $payment->getPayment();
                return $sum;
            }
        );
        $this->paid = $sum;
        return $sum;
    }

    function getRest(){
        return $this->price - $this->paid;
    }

    function getInsurances()
    {
        return Emp_OrderService::getManager()
            ->select()
            ->join("order", "o")
            ->join("offservice", "offs")
            ->join("offs.service", "service")
            ->join("o.tour", "tour")
            ->andWhere(array("tour.id" => $this->getPk(), "service.type" => "insurance"))
            ->groupBy("offs.id")
            ->fetchAll();
    }
}