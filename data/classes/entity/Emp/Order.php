<?php
/**
 *
 * Author: Andrey
 * Date: 15.10.13
 * Time: 20:57
 * @property Field_References tourists
 */

class Emp_Order extends fvRoot {

    private $_price, $paid, $services;

    protected $_persons = null;

    function __toString()
    {
        return (string)$this->getPk()." {$this->getMainTourist()} ";
    }

    function getCtime()
    {
        return $this->ctime->get() ? $this->ctime->asAdorned() : date("d.m.y H:i");
    }

    function getPrice($useCache = true)
    {
        if($this->price->get()) {
            return $this->price->get();
        }

        if ($this->_price && $useCache) {
            if($this->date->asTimestamp() < strtotime(date("d.m.Y") && !$this->price->get())) {
                $this->price->set($this->_price);
                $this->save();
            }
            return $this->_price;
        }

        $price = array_reduce(
            array_merge($this->getServices($useCache), $this->addservices->get($useCache)),
            function ($price, $service) {
                return (int)$price + $service->getPrice();
            }, 0
        );

        $this->_price = $price;
        if($this->date->asTimestamp() < strtotime(date("d.m.Y")) && !$this->price->get()) {
            $this->price->set($price);
            $this->save();
        }

        return $price;
    }

    function getTourists(){
        if ( $this->_persons === null ) {
            $this->_persons = $this->persons
                ->select()
                ->join("phones")
                ->loadRelation("phones")
                ->fetchAll();
        }
        return $this->_persons;
    }

    function getPayment()
    {
        if ($this->paid) {
            return $this->paid;
        }
        $sum = array_reduce(
            $this->payments->get(),
            function ($sum, $payment) {
                return $sum + $payment->sum->get();
            }, 0
        );
        $this->paid = $sum;
        return $sum;
    }

    function getRest()
    {
        return $this->getPrice() - $this->getPayment();
    }

    function getMainTourist()
    {
        if ($this->main->getPk()) {
            return $this->main;
        }
        return $this->persons->select()->fetchOne();
    }

    function getTotalTourists()
    {
        return count($this->persons->get());
    }

    function getTransportTourists(Emp_TourTransport $trans)
    {
        return Emp_Person::getManager()
            ->select()
            ->join('tourists')
            ->where(array("tourists.orderId" => $this->getPk(), "tourists.transportId" => $trans->getPk()))
            ->fetchAll();
    }

    function getServices($cache = true)
    {
        if (!$this->services) {
            $this->services = Emp_OrderService::getManager()
                ->select()
                ->join('tourist')
                ->where(['tourist.orderId' => $this->getPk()])
                ->fetchAll();
        } elseif ($cache) {
            return $this->services;
        }

        return Emp_OrderService::getManager()
            ->select()
            ->join('tourist')
            ->where(['tourist.orderId' => $this->getPk()])
            ->fetchAll();
    }
}