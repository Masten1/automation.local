<?php

class Emp_OrderService extends fvRoot
{

    function __toString()
    {
        return (string)$this->offservice->service;
    }

    function getPrice()
    {
        return $this->price->get() ? $this->price->get() : $this->offservice->getPrice();
    }

    function asAdorned()
    {
        return new Component_View($this, "default");
    }
}