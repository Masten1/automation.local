<?php

abstract class Storage {

    /**
     * @static
     * @param $storageName
     * @param $params
     * @return Storage
     */
    static function create( $storageName, $params ){
        $storageClass = "Storage_" . ucfirst($storageName);
        return new $storageClass( $params );
    }

    abstract function get( $key );

    abstract function set( $key, $value );

    abstract function open();

    abstract function close();

    abstract function destroy( $key, $value );

    abstract function garbageCollect( $lifetime );

}

class StorageException extends Exception {}