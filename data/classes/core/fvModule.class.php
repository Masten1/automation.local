<?php

    abstract class fvModule extends fvDebug{

        protected $template_dir;
        protected $compile_dir;
        /**
         * @var ModuleLayoult
         */
        protected $current_page;
        protected $className;
        protected $moduleName;
        protected $TTL;
        protected $_request;
        public static $FV_NO_MODULE = "";

        function __construct( $template, $compile, $current_page ){
            $this->template_dir = $template;
            $this->compile_dir = $compile;
            $this->current_page = $current_page;
            $this->TTL = 1800;

            $this->_request = fvRequest::getInstance();
        }

        function __set( $name, $value ){
            $this->__assign( $name, $value );
        }

        protected function getPage(){
            return $this->current_page;
        }

        protected function __display( $template_name ){
            $this->__assign( "module", $this->moduleName );
            $template_name = $this->moduleName . "." . $template_name;

            $old_template_dir = fvSite::$Template->template_dir;
            $old_compile_dir = fvSite::$Template->compile_dir;

            fvSite::$Template->template_dir = $this->template_dir;
            fvSite::$Template->compile_dir = $this->compile_dir;

            $result = fvSite::$Template->fetch( $template_name );

            fvSite::$Template->template_dir = $old_template_dir;
            fvSite::$Template->compile_dir = $old_compile_dir;

            return $result;
        }

        protected function __assign( $key, $value = null ){
            if( is_null( $value ) ){
                fvSite::$Template->assign( $key );
            }
            else{
                fvSite::$Template->assign( $key, $value );
            }
        }

        static $total = 0;

        function showModule( $module, $params = array(), $id = null ){
            $start = microtime( true );
            if( strlen( ( string )$module ) == 0 )
                $module = "index";
            $this->getParams()->setParameter( "moduleID", $id );

            $function = create_function( '$matches', 'return strtoupper($matches[1]);' );
            $module = ucfirst( strtolower( $module ) );
            while( preg_match( "/-(\w)/", $module ) ){
                $module = preg_replace_callback( "/-(\w)/", $function, $module );
            }
            $moduleName = "show" . $module;
            if( is_callable( array( $this, $moduleName ) ) ){
                try{
                    $res = call_user_func( array( $this, $moduleName ), $params );
                }
                catch( Exception $e ){
                    $res = StringFunctions::parseException( $e );
                }
            }
            else
                return fvModule::$FV_NO_MODULE;

            $time = round( microtime( true ) - $start, 5 );
            self::$total += $time;
            if( FV_TIME )
                return "($time) $res";
            else
                return $res;
        }

        /**
         * @return fvRequest
         */
        function getRequest(){
            return fvSite::$fvRequest;
        }

        function getParams(){
            return fvSite::$fvParams;
        }

        public function setMeta( $title = "", $keywords = "", $description = "" ){

            $this->current_page->setMeta( $title, $keywords, $description );
        }

    }

?>
