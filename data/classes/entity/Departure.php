<?php
/**
 * Created by PhpStorm.
 * User: masten
 * Date: 14.11.2014
 * Time: 16:29
 */

class Departure
{
    /**
     * @var array
     */
    private $orders;
    private $date;

    function __construct(array $orders)
    {
        $this->orders = $orders;
        $this->date = current($orders)->date->get();
    }
}