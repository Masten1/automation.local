<?php

    class UserAction extends fvAction{

        function __construct(){
            parent::__construct( fvSite::$Layoult );
        }

        function executeIndex(){
            return self::proceed();
        }

        function executeLogout() {
            fvSite::$fvSession->setUser(false);
        }
    }
