<?php
/**
 * 
 * Author: Andrey
 * Date: 30.05.14
 * Time: 17:24 
 */

class Component_Log extends Component_Extended
{

    /**
     * @var Emp_Tourist
     */
    public $tourist;

    function __construct(fvRoot $tourist)
    {
        $this->tourist = $tourist;
    }

    function getComponentName()
    {
        return "log";
    }
}