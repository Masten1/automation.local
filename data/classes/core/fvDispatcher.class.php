<?php

class fvDispatcher {
    protected $_request;
    protected $_responce;
    protected $_params;
    protected $_route;
    protected $_redirectCount;

    protected $_statusText;

    const MAX_REDIRECT = 100;

    protected function __construct() {
        $this->_request = fvRequest::getInstance();
        $this->_route = fvRoute::getInstance();
        $this->_responce = fvResponse::getInstance();
    }

    public static function getInstance() {
        static $instance;
        if (empty($instance)) $instance = new self();
        return $instance;
    }

    function getModule( $module, $type ) {
        if (!class_exists($class = fvSite::$fvConfig->get("modules.{$module}.{$type}_class"))) {
            if (file_exists(fvSite::$fvConfig->get("modules.{$module}.path") . "{$type}.class.php")) {
                require_once(fvSite::$fvConfig->get("modules.{$module}.path") . "{$type}.class.php");
            }
            elseif (file_exists(fvSite::$fvConfig->get("modules.staticpages.path") . "{$type}.class.php")) {
            	require_once(fvSite::$fvConfig->get("modules.staticpages.path") . "{$type}.class.php");
            	$class = fvSite::$fvConfig->get("modules.staticpages.{$type}_class");
            } else
                throw new Exception("Module {$module} doesn't exist.");
        }

        return new $class($module);
    }

    function forward($url) {
        if (++$this->_redirectCount > self::MAX_REDIRECT){
            throw new EDispatcherExeception("Max redirect count reached");
            }
         $this->_route->process($url); 

        if ( fvFilterChain::getInstance()->execute() !== false) {
            $this->_responce->sendHeaders();
            $this->_responce->sendResponceBody();
        }
       --$this->_redirectCount;
    }

    function process() {
        $this->forward( $this->_request->getRequestParameter("__url") );
    }

    function redirect($url, $delay = 0, $status = 302) {
        $this->_responce = fvResponse::getInstance();
        $this->_responce->clearHeaders();
        $this->_responce->setStatus($status);
        $this->_responce->setHeader("Location", $url);
        $this->_responce->setResponceBody('<html><head><meta http-equiv="refresh" content="%d;url=%s"/></head></html>', $delay, htmlentities($url, ENT_QUOTES, fvSite::$fvConfig->get('charset')));

        $this->_responce->sendHeaders();
        $this->_responce->sendResponceBody();
        die();
    }

    function show503() {
        $this->_responce = fvResponse::getInstance();
        $this->_responce->clearHeaders();
        $this->_responce->setStatus(503);
        $this->_responce->setResponceBody('Temporary unavailable');

        $this->_responce->sendHeaders();
        $this->_responce->sendResponceBody();
        die();
    }
}
