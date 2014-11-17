<?php

    class StaticPagesAction extends fvAction{

        function __construct(){
            parent::__construct( fvSite::$Layoult );
        }

        function executeIndex(){
            return self::proceed();
        }
    }
