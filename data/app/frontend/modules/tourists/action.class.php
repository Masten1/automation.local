<?php

    class TouristsAction extends fvAction{

        function __construct(){
            parent::__construct( fvSite::$Layoult );
        }

        function executeIndex(){
            return self::proceed();
        }

        function executeDelete(){
            return self::proceed();
        }

        function executeEdit(){
            return self::proceed();
        }

        function executeSave(){
            return self::proceed();
        }
    }
