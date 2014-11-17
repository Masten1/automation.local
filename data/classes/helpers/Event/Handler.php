<?php
    /**
     * @author Iceman
     * @since 29.11.12 16:20
     */
    abstract class Event_Handler{
        /**
         * @var Event_Handler
         */
        protected $next = null;

        /**
         * @param \Event_Handler $next
         */
        public function setNext( Event_Handler $next ){
            $this->next = $next;
            return $next;
        }

        public function tryHandle( Event $event ){
            $this->handle( $event );

            if( $this->next !== null )
                $this->next->tryHandle( $event );
        }

        abstract function handle( Event $event );
    }
