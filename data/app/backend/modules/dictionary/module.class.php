<?php

class DictionaryModule extends fvModule {

    function __construct ()
    {
        $this->moduleName = strtolower(substr(__CLASS__, 0, -6));
        parent::__construct(fvSite::$fvConfig->get("modules.{$this->moduleName}.smarty.template"),
        fvSite::$fvConfig->get("modules.{$this->moduleName}.smarty.compile"),
        fvSite::$Layoult);
    }

    function showIndex()
    {
        $pager = new fvPager( Dictionary::getManager() );
        
        $search = $this->_request->search;
        if( !empty( $search ) )
            $this->cDictionary = $pager->paginate("translation LIKE '%{$search}%' OR keyword LIKE '%{$search}%'");
        else
            $this->cDictionary = $pager->paginate();
        
        $this->search = $search;
        
        $this->cLanguages = Language::getManager()->getAll();
        
        return $this->__display('list.tpl');
    }
}

?>
