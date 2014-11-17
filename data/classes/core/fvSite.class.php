<?php

class fvSite {
	/**
	 * @var fvConfig
	 */
	public static $fvConfig;
	/**
	 * @var DB_common
	 * @extends PEAR
	 */
	public static $DB;
	/**
	 * @var fvSession
	 */
	public static $fvSession;
	/**
	 * @var Smarty
	 */
	public static $Template;
	/**
	 * @var mixed
	 */
	public static $currentModules;
	/**
	 * @var fvLayoult
	 */
	public static $Layoult;
	public static $fvRequest;
	public static $fvParams;

	/** @var Domain */
	public static $fvDomain;

    /** @var fvPDO */
	public static $pdo;

	public static function initilize () {

		//include core classes. Exceptions.
		//echo "1";
		if (!(fvSite::$fvConfig instanceof fvConfig)) user_error("Can't find loaded config class", E_USER_ERROR);

		//ititilize DB core

		if (!$dsn = fvSite::$fvConfig->get("database.dsn")) {
			$dsn = fvSite::$fvConfig->get("database.driver", "mysql") . "://" .
				fvSite::$fvConfig->get("database.user", "root") . ":" .
				fvSite::$fvConfig->get("database.pass", "") . "@" .
				fvSite::$fvConfig->get("database.host", "localhost") . "/" .
				fvSite::$fvConfig->get("database.name", "fv");
		}

		// BEGIN PDO Block
		fvSite::$pdo = new fvPDO(   fvSite::$fvConfig->get('pdo_database.dsn'),
                                    fvSite::$fvConfig->get('pdo_database.user'),
                                    fvSite::$fvConfig->get('pdo_database.pass'));


		//try to load schema yml
		fvSite::$fvConfig->Load(fvSite::$fvConfig->get("path.config") . "abstract.yml", true);
		fvSite::$fvConfig->Load(fvSite::$fvConfig->get("path.config") . "schema.yml", true);
		fvSite::$fvConfig->Load(fvSite::$fvConfig->get("path.config") . "schema.core.yml", true);
		if (file_exists(fvSite::$fvConfig->get("path.config") . "acl.yml"))
			fvSite::$fvConfig->Load(fvSite::$fvConfig->get("path.config") . "acl.yml", true);

		//initilize core classes
		/*
		$entityDir = fvSite::$fvConfig->get("path.entity", "../classes/entity/");
		$classDir = fvSite::$fvConfig->get("path.classes", "../classes/");
		$filterDir = fvSite::$fvConfig->get("path.filters", "../classes/filter");
		$interfaceDir = fvSite::$fvConfig->get("path.interfaces", "../classes/interface");
		 */

		self::setParams(fvParams::getInstance());

		//try to initilize template engine, if we have an application definded
		$fvSession = new fvSession();

		$fvSession->start();
		fvSite::setSession($fvSession);

		fvSite::setRequest(fvRequest::getInstance());

		if (defined("FV_APP")) {

			//Load main application config
			fvSite::$fvConfig->Load(fvSite::$fvConfig->get("path.application." . FV_APP . ".config") . "app.yml", true);
			fvSite::$fvConfig->Load(fvSite::$fvConfig->get("path.application." . FV_APP . ".config") . "template.yml", true);

			require_once(fvSite::$fvConfig->get("path.smarty.class_path") . "smarty.class.php");

			$smarty = new Smarty();
			$smarty->template_dir = fvSite::$fvConfig->get("path.smarty.template");
			$smarty->compile_dir = fvSite::$fvConfig->get("path.smarty.compile");
			fvSite::setTemplate($smarty);

			fvSite::$Template->assign("fvConfig", fvSite::$fvConfig);
			fvSite::$Template->assign("fvUser", fvSite::$fvSession->getUser());

			//Load routes for application
			fvSite::$fvConfig->Load(fvSite::$fvConfig->get("path.application." . FV_APP . ".config") . "routes.yml", true);
			//$cfg = fvSite::$fvConfig->getAllconfig();
			//var_dump($cfg['routes']);
			//die;
			

			//Load modules config
			fvSite::$fvConfig->Load(fvSite::$fvConfig->get("path.application." . FV_APP . ".config") . "modules.yml", true);
			fvSite::$currentModules = fvSite::$fvConfig->get("modules");

			//Load app classes
			if (file_exists(fvSite::$fvConfig->get("path.application." . FV_APP . ".config") . "acl.yml"))
				fvSite::$fvConfig->Load(fvSite::$fvConfig->get("path.application." . FV_APP . ".config") . "acl.yml", true);

			$urls = explode( '/', $_REQUEST['__url'] );
			if( !empty($urls[0]) )
				$language = fvManagersPool::get( "Language" )->getOneByCode( $urls[0] );

			if( ! $language instanceof Language ){
				$language = fvManagersPool::get( "Language" )->getDefault( true );
			} else {
                $_REQUEST['__url'] = preg_replace("/^{$language->code}\//", "", $_REQUEST['__url']);
            }
            $_REQUEST['__lang'] = (string)$language->code;
			define( 'lang', (string)$language->code );

			fvSite::$Template->assign("fvDictionary", fvDictionary::getInstance() );
			fvSite::$Template->assign("LANG", lang);

			fvSite::setDomain();
			fvSite::$Template->assign( "fvDomain", fvSite::$fvDomain );
		}
	}

	public static function setDomain() {
		if ( fvSite::$fvConfig->get( "site_base" ) == $_SERVER[ "HTTP_HOST" ] ) {
			self::setDefaultDomain();
		}
		try {
			$domain = preg_match( "/^(\S*?)\..*$/", $_SERVER[ "HTTP_HOST" ], $match );
			$domainName = $match[ 1 ];
			self::$fvDomain = Domain::getManager()->getOneInstance( " url like ? ", null, array( $domainName ) );
		}
		catch ( EInstanceError $e ) {
			self::setDefaultDomain();
		}
	}

	public static function setDefaultDomain() {
		self::$fvDomain = Domain::getManager()->getOneInstance( "isDefault = 1" );
	}

	public static function setConfig(fvConfig $Config) {
		fvSite::$fvConfig = $Config;
	}

	public static function setSession(fvSession $fvSession) {
		fvSite::$fvSession = $fvSession;
	}

	public static function setTemplate(Smarty $Template) {
		fvSite::$Template = $Template;
	}

	public static function setRequest (fvRequest $fvRequest) {
		self::$fvRequest = $fvRequest;
	}

	public static function setParams (fvParams $fvParams) {
		self::$fvParams = $fvParams;
	}
}
