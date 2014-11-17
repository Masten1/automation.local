<?php

class Emp_Shipping extends fvRoot {
    protected $price, $paid;

    function getDate(){
        return $this->order->date;
    }

    function getTotalTourists()
    {
        return Emp_Tourist::getManager()
            ->select()
            ->join("order", 'o')
            ->where("o.shippingId = {$this->getPk()}")
            ->getCount();
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

}