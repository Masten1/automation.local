<?php

class AConfigModule extends fvModule {

    function __construct () 
    {
        $this->moduleName = strtolower(substr(__CLASS__, 0, -6));
        parent::__construct(fvSite::$fvConfig->get("modules.{$this->moduleName}.smarty.template"), 
        fvSite::$fvConfig->get("modules.{$this->moduleName}.smarty.compile"), 
        fvSite::$Layoult);
    }

    function showIndex() 
    {
        $pager = new fvPager( AConfig::getManager() );
        $this->cDictionary = $pager->paginate();
        
        return $this->__display('list.tpl');
    }
}

?>
