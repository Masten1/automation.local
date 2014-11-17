<?php

class ClassAutoloader {
     public function __construct() {
         spl_autoload_register(array($this, 'loader'));
         
     }
     private function loader($className) {
        if( preg_match("/^i/", $className) ){
            $dir = FV_ROOT . "classes/interface/";
            $path = str_replace("_", "/",$className) . ".interface.php";
            
            if( file_exists($dir . $path) ) {
                require_once($dir . $path);
                return true;
            }
        } else {
            $dirs = array(
                FV_ROOT . "classes/library/",
                FV_ROOT . "classes/",
                FV_ROOT . "classes/entity/",
                FV_ROOT . "classes/core/",
                FV_ROOT . "classes/helpers/",
                FV_ROOT . "classes/exceptions/",
                FV_ROOT . "app/".FV_APP."/classes/",
            );
            $path = str_replace("_", "/", $className);
            foreach($dirs as $dir) {
                if( file_exists($dir . $path . ".php") ) {
                    require_once($dir . $path . ".php");
                    return true;
                }
                if( file_exists($dir . $path . ".class.php") ) {
                    require_once($dir . $path . ".class.php");
                    return true;
                }
            }
        }
        
        return false;
     }
 }

$autoloader = new ClassAutoloader();