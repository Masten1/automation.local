<?php

    /**
    * Translates a camel case string into a string with underscores (e.g. firstName -&gt; first_name)
    * @param    string   $str    String in camel case format
    * @return    string            $str Translated into underscore format
    */
    function from_camel_case($str) {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }

    /**
    * Translates a string with underscores into camel case (e.g. first_name -&gt; firstName)
    * @param    string   $str                     String in underscore format
    * @param    bool     $capitalise_first_char   If true, capitalise the first char in $str
    * @return   string                              $str translated into camel caps
    */
    function to_camel_case($str, $capitalise_first_char = false) {
        if($capitalise_first_char) {
          $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    function concatinateCamelCase(){
        $response = "";

        $args = func_get_args();
        foreach( $args as $string ){
            $response .= to_camel_case( $string, true );
        }

        $response[0] = strtolower( $response[0] );
        return $response;
    }

    function file_get_contents_authorize($host, $uri, $user, $pwd, $https = false, $port = false) {

    }

    if(false === function_exists('lcfirst'))
    {
        /**
         * Make a string's first character lowercase
         *
         * @param string $str
         * @return string the resulting string.
         */
        function lcfirst( $str ) {
            $str[0] = strtolower($str[0]);
            return (string)$str;
        }
    }

    function hsv2rgb( $H, $S, $V ) {
        if($S == 0)
        {
            $R = $G = $B = $V * 255;
        }
        else
        {
            $var_H = $H * 6;
            $var_i = floor( $var_H );
            $var_1 = $V * ( 1 - $S );
            $var_2 = $V * ( 1 - $S * ( $var_H - $var_i ) );
            $var_3 = $V * ( 1 - $S * (1 - ( $var_H - $var_i ) ) );

            if ($var_i == 0) { $var_R = $V ; $var_G = $var_3 ; $var_B = $var_1 ; }
            else if ($var_i == 1) { $var_R = $var_2 ; $var_G = $V ; $var_B = $var_1 ; }
            else if ($var_i == 2) { $var_R = $var_1 ; $var_G = $V ; $var_B = $var_3 ; }
            else if ($var_i == 3) { $var_R = $var_1 ; $var_G = $var_2 ; $var_B = $V ; }
            else if ($var_i == 4) { $var_R = $var_3 ; $var_G = $var_1 ; $var_B = $V ; }
            else { $var_R = $V ; $var_G = $var_1 ; $var_B = $var_2 ; }

            $R = $var_R * 255;
            $G = $var_G * 255;
            $B = $var_B * 255;
        }

        return array($R, $G, $B);
    }