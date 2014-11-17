<?php

    class fvLanguageFactory
    {
        /**
         *
         * @returns new $className
         * @param mixed $language
         */
        public static function view( fvRoot $class, $lang_code = null )
        {
            $lang = Language::getManager()->getOneByCode($lang_code ? $lang_code : fvSite::$fvConfig->getCurrentLang());

            if( !$lang_code && !$lang ){
                $lang = Language::getManager()->getDefault();
            } elseif( !$lang ) {
                throw new LanguageException("Language '{$lang_code}' not found!");
            }
            $className = $class->getEntity()."Localed";
            return new $className( $class, $lang->getPk() );
        }
    }


    class LanguageException extends Exception {}
?>
