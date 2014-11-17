<?php
/**
 * Created by PhpStorm.
 * User: twida_000
 * Date: 11/3/2014
 * Time: 11:32 AM
 */

class Emp_Tourist extends fvRoot
{

    private $_services;

    function __toString()
    {
        return $this->person->getName();
    }

    function getName()
    {
        return $this->person->getName();
    }

    function getServices( $additional = false )
    {
        if(!$this->_services)
        {
            /**
             * @var fvQuery $query
             */
            $query = $this->services->select();
            if($additional) {
                $query
                    ->join("offservice")
                    ->andWhere("isMain = 0");
            }
            $this->_services =  $query->fetchAll();
        }

        return $this->_services;
    }

    function render($type = 'asAdorned')
    {
        return new Component_View($this, $type);
    }

} 