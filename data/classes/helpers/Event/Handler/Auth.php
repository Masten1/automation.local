<?php
    /**
     * @author Iceman
     * @since 29.11.12 16:37
     */
    class Event_Handler_Auth extends Event_Handler{
        function handle( Event $event ){
            if( $event instanceof Event_Auth ){
                $this->logAuth( $event );
            }
        }

        private function logAuth( Event_Auth $event ){
            $authLog = new AuthLog();
            $authLog->user = $event->getUser();
            $authLog->save();

            $authLog->getReport();
        }
    }
