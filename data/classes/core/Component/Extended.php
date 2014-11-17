<?php
    /**
     * Расширяет компонент, чтобы можно было цеплять js & css
     * @author Iceman
     * @since 14.11.12 17:32
     */
    abstract class Component_Extended extends fvComponent{
        /**
         * @var array
         */
        protected $scripts = Array();

        /**
         * @var array
         */
        protected $sheets = Array();

        /**
         * @return array
         */
        public function getCSS(){
            return $this->sheets;
        }

        /**
         * @return array
         */
        public function getJS(){
            return $this->scripts;
        }

        /**
         * @param string $sheetsUrl
         * @return Component_Extended
         */
        public function addCSS( $sheetsUrl = "" ){
            if( !empty( $sheetsUrl ) ){
                $this->sheets[] = $sheetsUrl;
            }
            return $this;
        }

        /**
         * @param string $scriptUrl
         * @return Component_Extended
         */
        public function addJS( $scriptUrl = "" ){
            if( !empty( $scriptUrl ) ){
                $this->scripts[] = $scriptUrl;
            }
            return $this;
        }
    }
