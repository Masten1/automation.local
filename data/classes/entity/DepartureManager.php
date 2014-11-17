<?php
/**
 * Created by PhpStorm.
 * User: masten
 * Date: 14.11.2014
 * Time: 16:23
 */

class DepartureManager
{
    function __construct()
    {
        $orders = array_reduce(
            Emp_Order::getManager()->getAll(),
            function ($result, $order) {
                $result[$order->date->get()][] = $order;
                return $result;
            }
        );

        $this->departures = array_map(
            function ($departure) {
                return new Departure($departure);
            }, $orders
        );
    }

} 