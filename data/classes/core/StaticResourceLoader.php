<?php

class StaticResourceLoader {

    private $url;

    function __construct( $url ){
        $url = "/" . ltrim( $url, "/" );
        $file = realpath("." . $url );
        $time = filemtime($file);
        $this->url = $url . ( $time ? "?" . $time : "");
    }

    function __toString(){
        return $this->url;
    }
}
