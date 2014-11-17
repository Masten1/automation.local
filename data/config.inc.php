<?php
    define("FV_ROOT", realpath(dirname(__FILE__)) . "/");

    foreach( glob(FV_ROOT . "includes/*.inc.php") as $inc )
        require_once $inc;

    $fvConfig = fvCacheConfig::getInstance(FV_ROOT . "config/");
    $fvConfig->Load("app.yml");
    
	if( file_exists(FV_ROOT . "config/local.app.yml") )
	    $fvConfig->Load("local.app.yml");

    $fvConfig->Load("routes.yml");

	if (file_exists($fvConfig->get("path.config") . "local.app.yml"))
		$fvConfig->Load($fvConfig->get("path.config") . "local.app.yml", true);

    fvSite::setConfig( $fvConfig );
    fvSite::initilize();

    $fvConfig->loadFromDatabase();
