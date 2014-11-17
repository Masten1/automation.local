<?php

class fvFileLog{

    private $_path,
            $_name,
            $_rawOutput = false,
            $_fileOutput = true;

    function __construct( $name ){
        global $argv;

        if( !empty($argv) ){
            foreach( $argv as $arg ){
                if( $arg == '-r' )
                    $this->_rawOutput = true;
                if( $arg == '-f' )
                    $this->_fileOutput = false;
            }
        }

        $this->_name = $name;
        $this->_path = FV_ROOT . "logs/" . $name . '.log';
    }

    function __call( $name, $arguments ){
        $name = strtoupper($name);
        $string = array_shift( $arguments );
        $string = "{$name} :: {$string}\n";

        if( empty($string) )
            throw new Exception('Param $string is required!');

        if( $this->_rawOutput ){
            print $this->_name . " :: " . $string;
        }

        if( $this->_fileOutput ){
            $log_handle = fopen($this->_path, "a");
            fwrite($log_handle, date(DATE_ATOM) . " :: $string");
            fclose($log_handle);
        }
    }

}