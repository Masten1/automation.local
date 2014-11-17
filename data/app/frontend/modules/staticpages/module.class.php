<?php

    class StaticPagesModule extends fvModule{

        function __construct(){
            $this->moduleName = strtolower( substr( __CLASS__, 0, -6 ) );
            parent::__construct( fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.template" ),
                                 fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.compile" ),
                                 fvSite::$Layoult );
        }

        function showIndex(){
            $link = fvRoute::getInstance()->getRequestURL();
            $page = StaticPage::getManager()->find( $link );
            $this->__assign("page", $page );
            return $this->__display( "index.tpl" );
        }
    }
