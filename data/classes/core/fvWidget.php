<?php
    /**
     * @author Iceman
     * @since 29.11.12 11:34
     */

    class fvWidget extends Component_Extended{
        /**
         * @var fvRoot
         */
        private $_entity;

        public function __construct( fvRoot $entity, $type = "base" ){
            $this->_entity = $entity;
            $this->setType( $type );
        }

        /**
         * @return \fvRoot
         */
        public function getEntity(){
            return $this->_entity;
        }

        function getComponentName(){
            return "widget/" . $this->_entity->getEntity();
        }

        public function __get( $needle ){
            if( $this->_entity->$needle ){
                return $this->_entity->$needle;
            }

            return null;
        }

        public function setType( $type = "base" ){
            $this->setTemplateName( $type );
            return $this;
        }
    }