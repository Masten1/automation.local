<?php

abstract class fvLayoult {

    protected $_fields;

    public function __construct($templateName) {
        $this->templateName = $templateName;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function getFooterFloor() {
        return $this->footerfloor;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setTitle( $title ) {
        $this->title = $title;
    }

    public function setKeywords( $keywords ) {
        $this->keywords = $keywords;
    }

    public function setFooterFloor( $floor ) {
        $this->footerfloor = $floor;
    }

    public function setDescription( $description ) {
        $this->description = $description;
    }

    public function setTemplate( $templateName ) {
        $this->templateName = $templateName;
    }

    public function showPage() {
        if ( !is_null( $this->templateName ) ) {
            fvSite::$Template->assign( array(
                'currentPage' => $this,
            ) );
            return fvSite::$Template->fetch( $this->getTemplateName() );
        } else
            return $this->getPageContent();
    }

    public function getTemplateName() {
        $template = fvSite::$fvConfig->get( "template.{$this->templateName}" );

        if( is_null($template) )
            throw new Exception("Config 'template.{$this->templateName}' section not defined!");

        if( empty( $template[ "source" ]) )
            throw new Exception("In config 'template.{$this->templateName}' section not defined 'source' path!");

        return $template[ "source" ];
    }

    public function setModuleResult( $result ) {
        $this->moduleResult = $result;
    }

    public function getModuleResult() {
        return $this->moduleResult;
    }

    public function getLoggedUser() {
        return fvSite::$fvSession->getUser();
    }

    abstract public function getPageContent();

    public function getCss() {
        $data = $this->css;
        $arr = explode( "|", $data );
        $output = "<!-- start page css -->";
        foreach ( $arr as $key => $val ) {
            if ( $val )
                $output .= '<link rel="stylesheet" type="text/css" href="' . $val . '" />';
        }

        return $output . "<!-- end page css -->";
    }

    public function setCss( $css ) {
        $this->css = $css;
    }

    public function getJS() {
        $data = $this->js;
        $arr = explode( "|", $data );
        $output = "<!-- start page js -->";
        foreach ( $arr as $key => $val ) {
            if ( $val )
                $output .= '<script type="text/javascript" src="' . $val . '"></script>';
        }

        return $output . "<!-- end page js -->";
    }

    public function setJS( $js ) {
        $this->js = $js;
    }

    public function setMeta( $title="", $keywords="", $description="" ) {
        $this->title = $title;
        $this->keywords = $keywords;
        $this->description = $description;
    }

    public function __get($name) {
        return $this->_fields[$name];
    }

    public function __set($name, $value) {
        $this->_fields[$name] = $value;
    }

}

?>
