<?php
/**
 *
 * Author: Andrey
 * Date: 22.11.13
 * Time: 14:40
 */

class Emp_VehicleTier extends fvRoot {

    protected $array = array();

    function buildArray()
    {
        $this->array = array_reduce(
            $this->seats->get(),
            function($result, $element) {
                $result[$element->row->get()][$element->column->get()] = $element;
                return $result;
            }
        );
        return $this;
    }

    function __toString(){
        return $this->getName();
    }

    function getName() {
        return "";
        /*  $names = array(
                "1" => "Первый ярус",
                "2" => "Второй ярус"
            );

            return $names[$this->name->get()];
        */
    }

    function getSeatNumber($row, $column)
    {
        if($this->array[$row][$column] instanceof Emp_VehicleSeat) {
            return $this->array[$row][$column]->number->get();
        }
        return null;
    }

    function getSeatComment($row, $column)
    {
        if($this->array[$row][$column] instanceof Emp_VehicleSeat) {
            return $this->array[$row][$column]->comment->get();
        }
        return null;
    }

    function getSeatObject(array $coords){
        $object = Emp_VehicleSeat::getManager()
            ->select()
            ->where($coords)
            ->fetchOne();
        if(!$object instanceof Emp_VehicleSeat)
            $object = new Emp_VehicleSeat($coords);
        return $object;
    }
} 