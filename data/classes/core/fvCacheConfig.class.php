<?php

class fvCacheConfig extends fvConfig {

    static $instances = array();

    private $loadedFiles = array();
    private $loadedFromDatase = false;

    /**
     * @return self
     */
    public static function getInstance( $configDir, $configSeparator = "." ) {
        if( isset(self::$instances[$configDir]) ){
            if (self::$instances[$configDir] instanceof self)
                return self::$instances[$configDir];
        }

        $cache = fvMemCache::getInstance();
        if( !isset($_GET['_reloadconfig']) ) {
            if( ($cachedDictionary = $cache->getCache("__config_".$configDir . FV_APP)) instanceof self)
                return self::$instances[$configDir] = $cachedDictionary;
        } else {
            $cache->clearCache("__config_".$configDir . FV_APP);
        }

        return self::$instances[$configDir] = new self( $configDir, $configSeparator );
    }

    public function dropCache(){
        $cache = fvMemCache::getInstance();
        $cache->clearCache("__config_" . $this->configDir . FV_APP);
    }

    public function cache(){
        if( !FV_DEBUG_MODE )
            fvMemCache::getInstance()->setCache("__config_" . $this->configDir . FV_APP, $this);
    }

    public function Load($fileName, $fullPath = false) {
        $configFile = ($fullPath) ? $fileName : ($this->configDir . $fileName);

        if( !in_array($configFile, $this->loadedFiles) ){
            parent::Load($fileName, $fullPath);
            $this->cache();
        }
    }

    public function loadFromDatabase() {
        if( !$this->loadFromDatabase ){
            parent::loadFromDatabase();
            $this->cache();
        }
    }

}
