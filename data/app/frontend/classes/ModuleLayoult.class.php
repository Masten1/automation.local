<?php

class ModuleLayoult extends fvLayoult {

    /** @var Page */
    private $_pageInstance = null;
    private $_domInstance = null;
    public $pageInstance;
    public $moduleStr;

    function __construct(){
        $currentUrl = fvRoute::getInstance()->getModuleName();

        $this->_pageInstance = $this->pageInstance = Page::getManager()->getOneInstance("url like ? and domainId = ?", null, array( $currentUrl, fvSite::$fvDomain->getPk()));
        $this->_domInstance = new DOMDocument();


        if (!( $this->_domInstance->loadXML( $this->_pageInstance->getPageContent() ) ) ) {
            fvDispatcher::redirect(fvSite::$fvConfig->get('page_error', 0, 302));
        }

        $this->moduleStr = $currentUrl;

        $this->setTitle($this->_pageInstance->mTitle);
        $this->setDescription($this->_pageInstance->mDescription);
        $this->setKeywords($this->_pageInstance->mKeywords);

        // выбор нужной тпл в зависимости от модуля {{
            parent::__construct( $this->_pageInstance->layolt );
        //}}
    }

    function getPageContent() {

    }

    function getPageContentPart($contentPartName) {
        $xpth = new DOMXPath($this->_domInstance);
        $currentPart = $xpth->evaluate("/page/content_part[@name='$contentPartName']");

        if ($currentPart->length == 1) {
            $currentPart = $currentPart->item(0);
        } else return false;//fvDispatcher::redirect(fvSite::$fvConfig->get('page_error', 0, 302));

        return $this->_parseNode($currentPart);
    }

    private function _parseNode($node) {
        $result = '';
        $childNodes = array();
        foreach ($node->childNodes as $childNode) {
            $childNodes[] = $childNode;
        }
        usort($childNodes, array($this, '__cmp'));


        switch ($node->nodeName) {
            case 'module':
                $moduleName = $node->getAttribute('name');
                $moduleView = $node->getAttribute('view');
                $moduleParams = unserialize($node->getAttribute('parameters'));
                $module = fvDispatcher::getInstance()->getModule($moduleName, "module");
                return $module->showModule($moduleView, $moduleParams, $node->getAttribute("id"));
            case 'current_module':
                return $this->getModuleResult();
            case 'vertical_layoult':
                $data = '';
                $data .= "<table style='width: 100%' valign='top'>";
                $heights = explode(',', $node->getAttribute('size'));
                $spacer = $node->getAttribute('spacer');
                foreach ($childNodes as $key => $childNode) {
                    $data .= "<tr" . (($heights[$key] != "*")?" height='{$heights[$key]}'":'') ."><td>".$this->_parseNode($childNode)."</td></tr>";
                    if ($spacer) {
                        $data .= "<tr height='{$spacer}px'><td>&nbsp;</td></tr>";
                    }
                }
                $data .= "</table>";
                return $data;
            case 'horisontal_layoult':
                $data = '';
                $data .= "<table style='width: 100%' valign='top'><tr>";
                $widths = explode(',', $node->getAttribute('size'));
                $spacer = $node->getAttribute('spacer');
                foreach ($childNodes as $key => $childNode) {
                    $data .= "<td valign=\"top\"" . (($widths[$key] != "*")?" width='{$widths[$key]}'":'') .">".$this->_parseNode($childNode)."</td>";
                    if ($spacer && ($key < (count($childNodes) - 1))) {
                        $data .= "<td width='{$spacer}px'>&nbsp;</td>";
                    }
                }
                $data .= "</tr></table>";
                return $data;
            case 'content_part':
                foreach ($childNodes as $childNode) {
                    $result .= $this->_parseNode($childNode);
                }
                break;
        }
        return $result;
    }

    private function __cmp($a, $b) {

        if ($a->getAttribute('order') == $b->getAttribute('order')) return 0;

        return ($a->getAttribute('order') < $b->getAttribute('order')) ? -1 : 1;
    }

    function getJS(){
        $files = $this->_pageInstance->getJS();
        return array_merge( $this->_js, $files );
    }

    function getCss(){
        $files = $this->_pageInstance->getCSS();
        return array_merge( $this->_css, $files );
    }

    private $_js = Array();
    public function appendJS( $scripts ){
        if( is_array( $scripts ) ){
            foreach( $scripts as $script ){
                $this->appendJS($script);
            }
        }
        else{
            $this->_js[md5($scripts)] = $scripts;
        }

        return $this;
    }

    private $_css = Array();
    public function appendCSS( $scripts ){
        if( !is_array( $scripts ) ){
            $this->_css[] = $scripts;
        }
        else{
            $this->_css = array_merge( $this->_css, $scripts );
        }

        return $this;
    }
}

?>
