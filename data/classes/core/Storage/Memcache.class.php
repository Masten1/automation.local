<?php

class Storage_Memcache extends Storage {

    protected $lifetime;
    protected $prefix;
    protected $memcache;

    function __construct( $params ){
        $this->lifetime = $params["lifetime"];
        $this->prefix = isset($params["prefix"]) ? $params["prefix"] : 'emp_session_';
        $this->memcache = fvMemCache::getInstance();
    }

    function get( $key ){
        return $this->memcache->getCache( $this->prefix . $key, "" );
    }

    function set( $key, $value ){
        if( ! $this->memcache->setCache( $this->prefix . $key, $value ) ){
            throw new StorageException("could'n put data to memcache storage");
        }

        return true;
    }

    function destroy( $key, $value ){
        $this->memcache->clearCache( $this->prefix . $key, $this->lifetime );
        return true;
    }

    function garbageCollect( $lifetime ){
        return true;
    }

    function open(){
        return true;
    }

    function close(){
        return true;
    }

}