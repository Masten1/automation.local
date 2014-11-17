<?php
    /**
     * @author Iceman
     * @since 03.01.13 19:33
     */
    class Decorator_Idea{
        private $_idea;

        public function __construct( Idea $idea ){
            $this->_idea = $idea;
        }

        function getUrl(){
            $urlPattern = "/backend/ideas/edit/?id=%s";
            $url = sprintf( $urlPattern, $this->_idea->getPk() );
            return $url;
        }
    }
