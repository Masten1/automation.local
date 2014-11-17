<?php
class DepartureModule extends fvModule
{
    function __construct()
    {
        $this->moduleName = strtolower( substr( __CLASS__, 0, -6 ) );
        parent::__construct( fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.template" ),
            fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.compile" ),
            fvSite::$Layoult );
    }

    function showIndex()
    {
        $table = new Component_Table_Departure();
        $this->table = $table
            ->setTitle("Отправка")
            ->setAllowNew(false)
            ->setModule($this->moduleName)
            ->preload();

        return $this->__display( "index.tpl" );
    }
}