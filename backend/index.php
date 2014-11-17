<?php

define( "FV_MEMCACHE_ENABLED", true );
$stime = microtime(true);
define("FV_PROFILE", isset( $_GET['_profile'] ) );
define("FV_TIME", isset( $_GET['_time'] ) );

try{

    define("FV_APP", "backend");
    define( 'lang', "ru" );
    ini_set('magic_quotes_gpc', '0');

    require_once("../data/config.inc.php");

    fvDispatcher::getInstance()->process();

    if( FV_PROFILE )
        Profile::show();
}
catch (Exception $e) {
    throw($e);
}