<?php
    /**
     * User: cah4a
     * Date: 12.03.12
     * Time: 17:33
     */
    class fvHttpRequest{

        const DEFAULT_PORT = 80;
        const DEFAULT_HTTPS_PORT = 443;

        private $_https = false, $_port = false, $_host, $_uri, $_data, $_user, $_password, $_method = 'GET', $_lastRequest, $_lastResponse, $_resultContent, $_resultHeaders, $_maxResponseSize = 8388608; //8MB

        function __construct( $url = null ){
            if( $url )
                $this->parseUrl( $url );
        }

        public function request(){
            $out = "{$this->getMethod()} {$this->getUri()} HTTP/1.1\r\n";
            if( $this->getUser() && $this->getPassword() )
                $out .= 'Authorization: Basic ' . base64_encode( $this->getUser() . ':' . $this->getPassword() ) . "\r\n";

            //$out .= "Host: {$this->getHost()}:{$this->getPort()}\r\n";
            $out .= ( $this->getPort() )
                ? "Host: {$this->getHost()}:{$this->getPort()}\r\n"
                : "Host: {$this->getHost()}\r\n";

            if( $this->getMethod() == 'POST' ){
                $content = http_build_query( $this->getData() );
                $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $out .= "Content-Length: " . strlen( $content ) . "\r\n";
                $out .= "Connection: Close\r\n";
                $out .= "\r\n" . $content . "\r\n";
            }
            else{
                $out .= "Connection: Close\r\n";
                $out .= "\r\n";
            }

            if( $this->isHttps() )
                $host = 'ssl://' . $this->getHost();
            else
                $host = $this->getHost();

            if( !$sock = @fsockopen( $host, $this->getPort( true ), $errno, $errstr, 10 ) ){
                //print $errno . ' :: ' . $errstr;
                return false;
            }

            $this->setLastRequest( $out );
            fwrite( $sock, $out );
            $data = '';
            while( !feof( $sock ) ){
                $data .= fgets( $sock );
                if( strlen( $data ) > $this->_maxResponseSize ){
                    throw new Exception( "Responce for {$this->getHost()}{$this->getUri()} is more than {$this->getMaxSize()} bytes" );
                }
            }
            fclose( $sock );

            $this->setLastResponse( $data );

            $data = explode( "\r\n\r\n", $data );

            $this->setResultHeaders( array_shift( $data ) );

            $body = trim( implode( "\r\n\r\n", $data ), "\r\n" );

            if( strtolower( $this->getHeader( 'Transfer-Encoding' ) ) == 'chunked' ){
                $this->setResultContent( $this->unchunkHttpResponse( $body ) );
            }
            else
                $this->setResultContent( $body );

            return true;
        }

        private function unchunkHttpResponse( $str ){
            if( !is_string( $str ) || strlen( $str ) < 1 )
                return false;

            $eol = "\r\n";
            $add = strlen( $eol );
            $tmp = $str;
            $str = '';
            do{
                $tmp = ltrim( $tmp );
                $pos = strpos( $tmp, $eol );

                if( $pos === false )
                    return false;

                $len = hexdec( substr( $tmp, 0, $pos ) );

                if( !is_numeric( $len ) or $len < 0 )
                    return false;

                $str .= substr( $tmp, ( $pos + $add ), $len );
                $tmp = substr( $tmp, ( $len + $pos + $add ) );
                $check = trim( $tmp );
            } while( !empty( $check ) );
            unset( $tmp );

            return $str;
        }

        public function setData( array $data ){
            $this->_data = $data;
            $this->setMethod( 'POST' );
            return $this;
        }

        public function addDataParameter( $name, $value ){
            $this->_data[$name] = $value;
            return $this;
        }

        public function addDataParameters( $values ){
            foreach( $values as $key => $value ){
                $this->addDataParameter( $key, $value );
            }

            return $this;
        }

        public function getData(){
            return $this->_data;
        }

        public function setHost( $host ){
            $this->_host = $host;
            return $this;
        }

        public function getHost(){
            return $this->_host;
        }

        public function setHttps( $https ){
            $this->_https = (bool)$https;
            return $this;
        }

        public function isHttps(){
            return $this->_https;
        }

        public function setMethod( $method ){
            $this->_method = $method;
            return $this;
        }

        public function getMethod(){
            return $this->_method;
        }

        public function setPort( $port ){
            $this->_port = $port;
            return $this;
        }

        public function getPort( $returnDefault = false ){
            if( !$this->_port && $returnDefault ){
                if( $this->isHttps() ){
                    return self::DEFAULT_HTTPS_PORT;
                }
                else{
                    return self::DEFAULT_PORT;
                }
            }
            else{
                return $this->_port;
            }

        }

        public function setUri( $uri ){
            $this->_uri = $uri;
            return $this;
        }

        public function parseUrl( $url ){
            $parseUrl = parse_url( trim( $url ) );

            if( strtolower( $parseUrl['scheme'] ) == 'https' ){
                $this->setHttps( true );
            }

            $this->setHost( $parseUrl['host'] );

            if( empty( $parseUrl['path'] ) )
                $parseUrl['path'] = '/';

            if( !empty( $parseUrl['query'] ) )
                $this->setUri( $parseUrl['path'] . "?" . $parseUrl['query'] );
            else
                $this->setUri( $parseUrl['path'] );

            return $this;
        }

        public function getUri(){
            return $this->_uri;
        }

        public function setResultContent( $resultContent ){
            $this->_resultContent = $resultContent;
            return $this;
        }

        public function getResultContent(){
            return $this->_resultContent;
        }

        public function setResultHeaders( $resultHeaders ){
            if( !is_array( $resultHeaders ) ){
                $headers = array();
                foreach( explode( "\n", $resultHeaders ) as $line ){
                    $sections = explode( ":", trim( $line ) );
                    if( count( $sections ) > 1 ){
                        $key = trim( array_shift( $sections ) );
                        $value = trim( implode( ":", $sections ) );
                        $headers[$key] = $value;
                    }
                }
                $this->_resultHeaders = $headers;
            }
            else
                $this->_resultHeaders = $resultHeaders;
            return $this;
        }

        public function getResultHeaders(){
            return $this->_resultHeaders;
        }

        public function getHeader( $key ){
            return $this->_resultHeaders[$key];
        }

        public function getLocation(){
            if( !empty( $this->_resultHeaders['Location'] ) ){
                return $this->_resultHeaders['Location'];
            }

            if( !empty( $this->_resultHeaders['location'] ) ){
                return $this->_resultHeaders['location'];
            }

            return false;

        }

        public function setPassword( $password ){
            $this->_password = $password;
            return $this;
        }

        public function getPassword(){
            return $this->_password;
        }

        public function setUser( $user ){
            $this->_user = $user;
            return $this;
        }

        public function getUser(){
            return $this->_user;
        }

        public function setLastRequest( $lastRequest ){
            $this->_lastRequest = $lastRequest;
            return $this;
        }

        public function getLastRequest(){
            return $this->_lastRequest;
        }

        public function setLastResponse( $lastResponse ){
            $this->_lastResponse = $lastResponse;
            return $this;
        }

        public function getLastResponse(){
            return $this->_lastResponse;
        }

        public function getMaxSize(){
            return $this->_maxResponseSize;
        }

        public function setMaxSize( $size ){
            $this->_maxResponseSize = $size;
        }


    }
