<?php

/**
 * Словарь
 */
class fvDictionary {
    const NO_TRANSLATION = "<i>Нет перевода</i>";

    private $dictionary;
    static private $instance;

    /**
     * @returns fvDictionary
     */
    private function __construct() {
        $cache = fvMemCache::getInstance();

        $dict = $cache->getCache("__dictionary_" . lang);

        if( !$dict ){
            $list = Dictionary::getManager()->getAll();

            $dict = array();
            foreach ($list as $key => $e) {
                $dict[(string) $e->keyword] = $e;
            }

            $this->saveCache();
        }

        $this->dictionary = $dict;

        return $this;
    }

    /**
     * @return self
     */
    public static function getInstance() {
        if (self::$instance instanceof self)
            return self::$instance;

        $cache = fvMemCache::getInstance();

        if( ($cachedDictionary = $cache->getCache("__dictionary_" . lang)) instanceof self)
            return self::$instance = $cachedDictionary;

        $newDictionary = new self;
        $result = $cache->setCache("__dictionary_" . lang, $newDictionary);

        return self::$instance = $newDictionary;
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function get($key, $default = null) {
        if (isset($this->dictionary[$key])) {
			if (strlen($this->dictionary[$key]->translation->get()))
                return $this->dictionary[$key]->translation->get();

            return $default ? $default : $this->noTranslation($key);
        }

		$dbCheck = Dictionary::getManager()->getOneByKeyword( $key );
		if( $dbCheck instanceof Dictionary ){
			$this->dictionary[$key] = $dbCheck;
            $this->saveCache();
			return $this->dictionary[$key]->translation;
		}

		$this->createElement($key, $default);

        return $default ? $default : $this->noTranslation($key);
    }

    private function noTranslation($key) {
        return ( FV_DEBUG_MODE == true ) ? $key : self::NO_TRANSLATION;
    }

    /**
     * save new macros
     * @param $key macros key
     * @return bool
     */
    private function createElement($key, $default = null) {
        $iDictionary = new Dictionary();
        $iDictionary->keyword = $key;
        $this->dictionary[$key] = $iDictionary;
        if ($default) {
            $iDictionary->save();
            $iDictionary->setLanguage(lang);
            $iDictionary->translation->set($default);
        }
        $this->saveCache();
        return $iDictionary->save();
    }

    public function dropCache(){
        $cache = fvMemCache::getInstance();
        $langs = Language::getManager()->getAll();

        foreach( $langs as $l )
            $cache->clearCache("__dictionary_" . $l->code);
    }

    public function saveCache(){
        fvMemCache::getInstance()->setCache("__dictionary_" . lang, $this->dictionary);
    }
}

?>
