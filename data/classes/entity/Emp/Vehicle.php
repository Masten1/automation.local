<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andrey
 * Date: 11.10.13
 * Time: 10:37
 * To change this template use File | Settings | File Templates.
 */

class Emp_Vehicle extends fvRoot {


    function __toString(){
        return $this->model->get()." ".$this->number->get();
    }

    function asAdorned(){
        return new Component_View( $this );
    }

    function getTotalSeats(){
        $count = 0;
        array_walk($this->seats->get(), function($elem) use (&$count){
            $count += $elem->quantity->get();
        });
        return $count;
    }

    function getFirstTierRows(){
        return (!$this->isNew()) ?
            $this->tiers
                ->select()
                ->andWhere("name = 1")
                ->fetchOne()
                ->rows->get() : 0;
    }

    function getSecondTierRows(){
        return ($this->secondTier->get()) ?
            $this->tiers
                ->select()
                ->andWhere("name = 2")
                ->fetchOne()
                ->rows->get() : 0;

    }

    function getFirstTierId(){
        return (!$this->isNew()) ?
            $this->tiers
                ->select()
                ->andWhere("name = 1")
                ->fetchOne()
                ->getPk() : 0;
    }

    function getSecondTierId(){
        return ($this->secondTier->get()) ?
            $this->tiers
                ->select()
                ->andWhere("name = 2")
                ->fetchOne()
                ->getPk() : 0;
    }

    function widget($type = "default") {
        return new Component_View($this, $type);
    }

    function getSeat( $number ) {
        return Emp_VehicleSeat::getManager()
            ->select()
            ->join("tier")
            ->join("tier.vehicle", "vehicle")
            ->where(array("root.number" => $number, "vehicle.id" => $this->getPk()))
            ->fetchOne();
    }

    function asString() {
        return "{$this->number->get()} {$this->model->get()}";
    }
}