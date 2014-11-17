<?php

class LanguageManager extends fvRootManager {

    private $languages;
    private $default;

    function __construct($entity){
        parent::__construct($entity);

        $this->languages = $this->getAll();
        
        $this->default = $this->getOneByIsDefault(TRUE);
        if( !$this->default ){
            $this->default = $this->getOneByCode(fvSite::$fvConfig->getCurrentLang());
        }

        if( empty($this->languages) ){
            throw new Exception("No languages found!");
        }
    }

    function getAll(){
        $args = func_get_args();
        if( empty($args) ) {
            if( empty($this->languages) ){
                $this->languages = fvMemCache::getInstance()->getCache('_languages');

                if( empty($this->languages) ){
                    $this->reloadCache();
                }
            }

            return $this->languages;
        }
        return call_user_func_array(array('parent', 'getAll'), $args);
    }

    function getByPk($pk, $createNonExist = false) {
        foreach( $this->languages as $lang ) {
            if( $lang->getPk() == $pk )
                return $lang;
        }

        return parent::getByPk($pk, $createNonExist);
    }

    function getDefault(){
        return $this->default;
    }

    protected function getAllByFieldName($fieldName, $value, $condition, $limit = null, $case_sensitive = true){
        $result = array();
                
        foreach( $this->languages as $language ) {
            if( $language->$fieldName->get() == $value ){
                $result[] = $language;
            }
        }

        return $result;
    }

    function reloadCache(){
        $this->languages = parent::getAll();
        fvMemCache::getInstance()->setCache('_languages', $this->languages);
    }
}