<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrey
 * Date: 11.10.13
 * Time: 10:37
 * To change this template use File | Settings | File Templates.
 */

class Emp_Person extends fvRoot {

    protected $servicesCache;
    protected $skipass;
    private $currentOrder;

    private $_services;

    function __toString(){
        return (string)$this->name;
    }

    function render($name){
        return new Component_View($this, $name);
    }

    function getName()
    {
        return $this->name->get();
    }

    function getPhone()
    {
        if(count($this->phones->get()))
            return end($this->phones->get())->name->get();
        return null;
    }

    function getPhoneString()
    {
        $phone = $this->getPhone();
        return ( $phone === null ) ? "" : $phone;
    }


    function getTransport($tourId)
    {
        if(!$this->_tourTransport) {
            $this->_tourTransport = Emp_TourTransport::getManager()
                ->select()
                ->join("passengers")
                ->where(array("passengers.touristId" => $this->getPk(), "root.tourId" => $tourId))
                ->fetchOne();
        }
        return $this->_tourTransport;
    }

    function getTransportPk($tourId)
    {
        $trans = $this->getTransport($tourId);
        return $trans instanceof Emp_TourTransport ? $trans->getPk() : null;
    }

    function getHotel($tourId)
    {
        if (!$this->_tourHotel) {
            $this->_tourHotel = Emp_TourHotel::getManager()
                ->select()
                ->join("tourists")
                ->where(array("tourists.touristId" => $this->getPk(), "root.tourId" => $tourId))
                ->fetchOne();
        }
        return $this->_tourHotel;
    }

    function getHotelPk($tourId)
    {
        $trans = Emp_TourHotel::getManager()
            ->select()
            ->join("tourists")
            ->where(array("tourists.touristId" => $this->getPk(), "root.tourId" => $tourId))
            ->fetchOne();

        return $trans instanceof Emp_TourHotel ? $trans->getPk() : null;
    }

    function isValid()
    {
        if($this->isNew()){
            $tourist = Emp_Person::getManager()
                ->select()
                ->where(array("email" => $this->email->get()))
                ->fetchOne();

            if($tourist instanceof Emp_Person) {
                $this->email->setValidationMessage("Пользователь с таким адресом уже существует");
                return false;
            }

        }
        return parent::isValid();
    }

    function asAdorned()
    {
        return new Component_View( $this );
    }

    function isGroupLeader(Emp_Tour $tour)
    {
        $order = $this->getOrder($tour);
        if ($order instanceof Emp_Order) {
            return $order->getMainTourist()->getPk() === $this->getPk();
        }
        return false;
    }

    function getOrder(Emp_Tour $tour)
    {
        if (!$this->currentOrder[$tour->getPk()] instanceof Emp_Order) {
            $this->currentOrder[$tour->getPk()] = Emp_Order::getManager()
                ->select()
                ->join("tourists")
                ->where(array("tourId" => $tour->getPk(), "tourists.id" => $this->getPk()))
                ->fetchOne();
        }
        return $this->currentOrder[$tour->getPk()];
    }

    function getGroupLeader(Emp_Tour $tour)
    {
        $order = $this->getOrder($tour);
        if($order instanceof Emp_Order)
            return $order->getMainTourist()->getName();
        return "";
    }

    function getOrderId(Emp_Tour $tour)
    {
        $order = $this->getOrder($tour);
        if($order instanceof Emp_Order)
            return $order->getPk();
        return null;
    }

    function getAddServices(Emp_Tour $tour)
    {
        $order = $this->getOrder($tour);
        if($order instanceof Emp_Order)
            return $order->addservices->get();
        return null;
    }

    function getCost(Emp_Order $order)
    {
        $cost = 0;
        $services = Emp_OrderService::getManager()
            ->select()
            ->where(array("touristId" => $this->getPk(), "orderId"=>$order->getPk()))
            ->fetchAll();
        if($this->isGroupLeader($order->tour)) {
            $services = array_merge($services, $order->addservices->get());
        }

        array_walk($services, function($elem) use (&$cost){
            $cost+= $elem->getPrice();
        });
        return $cost;
    }

    function getTransportComment(Emp_Tour $tour)
    {
        $oTransport = $this->getOrderTransport($tour);
        $order = $this->getOrder($tour);
        if($oTransport instanceof Emp_OrderTransport) {
            return $oTransport->getComment($order);
        }
        elseif($order instanceof Emp_Order) {
            return $order->transportWish->get();
        }
        return "";
    }

    function getHotelComment(Emp_Tour $tour)
    {
        $oTransport = $this->getOrderHotel($tour);
        $order = $this->getOrder($tour);
        if ($oTransport instanceof Emp_OrderHotel && $order instanceof Emp_Order) {
            return $oTransport->getComment($order);
        } elseif($order instanceof Emp_Order) {
            return $order->hotelWish->get();
        }
        return "";
    }

    function getHotelRoom(Emp_Tour $tour) {
        $oHotel = $this->getOrderHotel($tour);
        return $oHotel->room;
    }

    function getHotelRoomPrice(Emp_Tour $tour) {
        return $this->getHotelRoom($tour)->price->get();
    }

    function getGeneralComment(Emp_Tour $tour)
    {

        $order = $this->getOrder($tour);
        if($order instanceof Emp_Order)
        {
            return $order->generalWish->get();
        }
        return "";
    }


    function getInsurance(Emp_Order $order)
    {
        if($this->servicesCache) return $this->servicesCache;
        return $this->servicesCache = $this->services
            ->select()
            ->join("offservice", "of")
            ->join("of.service", "srv")
            ->andWhere("orderId = {$order->getPk()} AND srv.type='insurance'")
            ->fetchOne();
    }

    function getSkipass(Emp_Order $order)
    {
        if($this->skipass) return $this->skipass;
        return $this->skipass = $this->services
            ->select()
            ->join("offservice", "of")
            ->join("of.service", "srv")
            ->andWhere("orderId = {$order->getPk()} AND srv.type='skipass'")
            ->fetchOne();
    }

}