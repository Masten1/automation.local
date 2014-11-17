<?php
    header( 'P3P: CP="NOI ADM DEV COM NAV OUR STP"' );
    ini_set("memory_limit", "512M");
    $time = microtime( true );
    //echo microtime(true) - $_SERVER[ "REQUEST_TIME" ] ggf;
    /** HELLO TRAC * */
    define( "FV_MEMCACHE_ENABLED", true );
    define( "FV_DEBUG_MODE", true );
    define( "FV_PROFILE", isset( $_GET['_profile'] ) );
    define( "FV_TIME", isset( $_GET['_time'] ) );

    if( FV_DEBUG_MODE )
    ini_set( "display_errors", 1 );

    try{
        define( "FV_APP", "frontend" );
        require_once( "data/config.inc.php" );
        fvResponse::getInstance()->setPragma( true );
        fvDispatcher::getInstance()->process();

        if( FV_TIME )
            print '<br/> Total time: ' . ( microtime( true ) - $time );

        if( FV_PROFILE )
            Profile::show();
    }
    catch( Exception $e ){
        errorHandler( $e );
    }