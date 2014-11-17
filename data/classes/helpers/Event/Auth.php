<?php
    /**
     * @author Iceman
     * @since 29.11.12 16:01
     */
    class Event_Auth extends Event{
        /**
         * @var User
         */
        protected $user;

        public function __construct( User $user ){
            $this->user = $user;
        }

        /**
         * @return \User
         */
        public function getUser(){
            return $this->user;
        }
    }
