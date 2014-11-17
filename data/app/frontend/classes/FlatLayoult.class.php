<?php

class FlatLayoult extends fvLayoult {

    private $_pageInstance = null;
    
    public function __construct() {
        //$currentUrl = fvRoute::getInstance()->getModuleName();

        $p = Page::getManager()->getPagesByURL(fvRoute::getInstance()->getRequestURL());
        if (is_object($p[0])) {
            $this->_pageInstance = $p[0];
                
            $this->setTitle($this->_pageInstance->title);
            $this->setDescription($this->_pageInstance->description);
            $this->setKeywords($this->_pageInstance->keywords);
        } else {
            $this->setTitle(fvSite::$fvConfig->get('title'));
        }
        
        parent::__construct("main.tpl");
    }
    
    public function getPageContent() {
            
        return $this->getModuleResult();
    }
}
