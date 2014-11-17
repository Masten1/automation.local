<?php

    class ToursAction extends fvAction{

        function __construct(){
            parent::__construct( fvSite::$Layoult );
        }

        function executeIndex(){
            return self::proceed();
        }

        function executeList(){
            return self::$FV_NO_LAYOULT;
        }
        function executeInsurance(){
            return self::$FV_NO_LAYOULT;
        }
        function executeHotelFinance(){
            return self::$FV_NO_LAYOULT;
        }
    }
