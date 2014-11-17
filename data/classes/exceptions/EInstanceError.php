<?php
/**
 * throws on neccessary entity instance 
 * @author Корнев Захар
 */
class EInstanceError extends Exception{
    function __construct($message = "Instance is not exists", $code = 0){
        parent::__construct($message, $code);
    }
}