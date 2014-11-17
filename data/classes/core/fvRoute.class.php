<?php

class fvRoute {
    
    protected $_routeName;
    protected $_moduleName;
    protected $_actionName;
    protected $_routes;
    protected $_requestURL;
    
    protected function __construct() {
        $this->_routes = $this->getFilteredRoutes();
    }
    
    public static function getInstance() {
        static $instance;
        if (empty($instance)) {
            $instance = new self();
        }
        return $instance;
    }                                                
    
    public function processURL($currentURL) 
    {

        $urlparams = $this->parseUrl(@$currentURL);

        if (!$urlparams) {
            fvAction::redirect404();
        }

        $cur = "http://".$_SERVER['HTTP_HOST']."/".$currentURL;

        if(!fvSite::$fvSession->get("curURL"))
            $prev=$currentURL;
        else
            $prev = fvSite::$fvSession->get("curURL");

        fvSite::$fvSession->set("prevURL",$prev);
        fvSite::$fvSession->set("curURL",$cur);


        $this->_routeName = $urlparams['routeName'];
        $this->_requestURL = $currentURL;
        $this->_moduleName = $urlparams['module'];
        $this->_actionName = $urlparams['action'];


        $r = fvRequest::getInstance();
        foreach ($urlparams['params'] as $requestKey => $requestValue) {
            $r->putRequestParameter($requestKey, $requestValue);
        }
        $r->putRequestParameter('module', $this->_moduleName);
        $r->putRequestParameter('action', $this->_actionName);
        $r->putRequestParameter('requestURL', $currentURL);
        //var_dump(array('module' => $this->_moduleName, 'action' => $this->_actionName));die;
        return array('module' => $this->_moduleName, 'action' => $this->_actionName);
    }

    public function parseUrl($currentURL) {
        if (substr($currentURL, 0, 1) !== '/') $currentURL = "/" . $currentURL;
        $matches = array();

        foreach ($this->_routes as $routeName => $route) {
            $urlArray = explode("/", $route['url']);
            $url = '\/';
            $paramsArray = array();
            $i = 0;

            foreach ($urlArray as $urlPath) {
                if (strlen(trim($urlPath)) == 0) continue;
                if (strpos($urlPath, ":") !== false) {
                    if (isset($route['params'][substr($urlPath, 1)])) {
                        $url .= "(" . $route['params'][substr($urlPath, 1)] . ")\/";
                    } else {
                        $url .= "([^\/]*)\/?";
                    }
                    $paramsArray[substr($urlPath, 1)] = ++$i;
                } else {
                    $url .= $urlPath . "\/";
                }
            }

            if( $url != '\/' )
                $url = rtrim($url, "?") . "?";

            if (preg_match("/^".$url."/i", $currentURL, $matches)) {
                if (!($moduleName = ($route['module'])?$route['module']:$matches[$paramsArray['module']])) {
                    $moduleName = "index";
                }
                if (!($actionName = ($route['action'])?$route['action']:$matches[$paramsArray['action']])) {
                    $actionName = "index";
                }
                $params = array();
                foreach ($paramsArray as $requestKey => $matchId) {
                    $params[$requestKey] = $matches[$matchId];
                }

                return array(
                    'module'        => $moduleName,
                    'action'        => $actionName,
                    'routeName'     => $routeName,
                    'currentURL'    => $currentURL,
                    'params'        => $params,
                );
            }
        }
    }
    
    public function processRoute($route) {
        $currentRoute = $this->_routes[$route];
        $this->_routeName = $currentRoute;
        $this->_moduleName = ($route['module'])?$route['module']:'index';
        $this->_actionName = ($route['action'])?$route['action']:"index";

        return array('module' => $this->_moduleName, 'action' => $this->_actionName);
    }
    
    
    public function process($url) {
        if (substr($url, 0, 1) == "@") {
            return $this->processRoute(substr($url, 1));
        } else {
            return $this->processURL($url);
        }
    }
    
    public function getRouteName() {
        return $this->_routeName;
    }
    
    public function getModuleName() {
        return $this->_moduleName;
    }
    
    public function getActionName() {
        return $this->_actionName;
    }
    
    public function getRequestURL () {
        return $this->_requestURL;
    }

    /**
     * @return array|null
     */
    private function getFilteredRoutes() {
        $routes = fvSite::$fvConfig->get('routes');

        foreach ($routes as $routeKey => $route) {
            if (!$this->checkAcl($route)) {
                unset($routes[$routeKey]);
            }
        }
        return $routes;
    }

    private function checkAcl($route) {
        $user = fvSite::$fvSession->getUser();
        if ($route['access']['enable']) {
            if ($route['access']['mode'] ==  'deny') {
                if ($user && !$user->isRoot() && $route['access']['acl'] && $user->check_acl($route['access']['acl'])) {
                    return false;
                }
            } elseif (!$user || ($route['access']['acl'] && !$user->check_acl($route['access']['acl']))){
                return false;
            }
        }
        return true;
    }
}
