<?php
    /**
     * @author Iceman
     * @since 14.11.12 14:13
     */
    class fvFilter_Frequency implements iFilter{
        const MIN_LAG = 1;

        /**
         * @var Storage_DB
         */
        private $storage;

        public function __construct(){
            $accessTableName = fvSite::$fvConfig->get( "database.db_prefix" ) . "Access";
            $this->storage = Storage::create( "db",
                                              Array( "table" => $accessTableName, "lifetime" => 60, "key" => "ip" ) );
        }

        public function execute(){
            if( !$this->isRequestFilterable() ){
                return true;
            }

            $ip = $_SERVER["REMOTE_ADDR"];

            $stored = $this->storage->get( $ip );
            $lastRequest = ( is_array( $stored ) ) ? current( $stored ) : 0;

            $time = microtime( true );

            if( $lastRequest != 0 ){
                if( $time - $lastRequest < self::MIN_LAG ){
                    return $this->tooFast();
                }
            }

            $this->storage->set( $ip, $time );
            return true;
        }

        private function tooFast(){
            fvResponse::getInstance()->setStatus( 403 );
            echo "Too Fast";
            return false;
        }

        private function isRequestFilterable(){
            $requestUri = fvRequest::getInstance()->__url;
            $filterableUris = fvSite::$fvConfig->get( "frequencyFilter" );

            foreach( $filterableUris as $uriPattern ){
                $pattern = "/" . addcslashes( $uriPattern, "/" ) . "/";
                if( preg_match( $pattern, $requestUri ) )
                    return true;
            }

            return false;

        }
    }

    class FrequencyException extends Exception{
        public function __construct(){
            parent::__construct( "Too fast." );
        }
    }
