<?php

abstract class fvAction {

    public static $FV_ERROR = -1;

    public static $FV_OK = 1;
    public static $FV_NO_LAYOULT = 2;
    public static $FV_NO_ACTION = 3;
    public static $FV_NO_LAYOULT_MODULE = 4;
    public static $FV_AJAX_CALL = 5;

    public static $FLASH_SUCCESS = "success";
    public static $FLASH_ERROR = "error";
    public static $FLASH_INFO = "info";

/*    protected $template_dir;
    protected $compile_dir;/*/
    protected $current_page;
//    protected $className;

    public $returnValue = null;

    function __construct($current_page) {
        $this->current_page = $current_page;
            
        $this->_request = fvRequest::getInstance();
    }

    function getPage() {
        return $this->current_page;
    }

    function callAction($action) {
        if (strlen((string)$action) == 0) $action = "index";
        
        $function = create_function( '$matches', 'return strtoupper($matches[1]);');
        $action = ucfirst(strtolower($action));
        while( preg_match("/-(\w)/", $action) ) {
            $action = preg_replace_callback("/-(\w)/", $function, $action );
        }
        
        $actionName = 'execute' . $action;
        if (is_callable(array($this, $actionName))) {
            $res = call_user_func(array($this, $actionName));
            if (is_null($res)) $res = fvAction::$FV_OK;
            return $res;
        }
        else return static::proceed();
    }

    function forward($url) {
        if (fvRequest::getInstance()->isXmlHttpRequest()) {
            fvResponse::getInstance()->setHeader('redirect', $url);
        } else {
            fvDispatcher::getInstance()->forward($url);
        }
    }

    static function redirect($url, $application = FV_APP) {
        if (substr($url, 0, 1) == "@" || substr($url, 0, 4) != "http") {
            $url = fvSite::$fvConfig->get("path.application." . $application . ".web_root") . substr($url, 1);
        }
        if (fvRequest::getInstance()->isXmlHttpRequest()) {
            fvResponse::getInstance()->setHeader('redirectDirect', $url);
        } else {
            fvDispatcher::getInstance()->redirect($url);
        }
    }

    static function redirect404() {
        fvDispatcher::getInstance()->redirect(fvSite::$fvConfig->get('page_404'), 0, 404);
    }

    /**
     * @return fvRequest
     */
    function getRequest() {
        return fvSite::$fvRequest;
    }

    public function setFlash($message, $type = self::FLASH_INFO, $trace = null) {
        fvResponse::getInstance()->setFlash($message, $type, $trace);
    }

    protected function __display($template_name) {
        $old_template_dir = fvSite::$Template->template_dir;
        $old_compile_dir = fvSite::$Template->compile_dir;

        fvSite::$Template->template_dir = $this->template_dir;
        fvSite::$Template->compile_dir = $this->compile_dir;

        $result = fvSite::$Template->fetch($template_name);

        fvSite::$Template->template_dir = $old_template_dir;
        fvSite::$Template->compile_dir = $old_compile_dir;

        return $result;
    }

    protected function __assign($key, $value = null) {
        if (is_null($value)) {
            fvSite::$Template->assign($key);
        }
        else {
            fvSite::$Template->assign($key, $value);
        }
    }

	public static function proceed() { 
		if (!fvRequest::getInstance()->isXmlHttpRequest()) {
			return self::$FV_OK;
		} else {
			return self::$FV_AJAX_CALL;
		}
	}

}

?>
