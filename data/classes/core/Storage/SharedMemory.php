<?php
    /**
     * @author Iceman
     * @since 08.11.12 18:37
     */
    class Storage_SharedMemory extends Storage{

        private $key;
        private $flags;
        private $mode;
        private $size;

        public function __construct(){
            $this->key = fvSite::$fvConfig->get( "storage.sharedMemory.key", 0xff3 );
            $this->flags = fvSite::$fvConfig->get( "storage.sharedMemory.key", "c" );
            $this->mode = fvSite::$fvConfig->get( "storage.sharedMemory.key", 0666 );
            $this->size = fvSite::$fvConfig->get( "storage.sharedMemory.key", 1024 );
        }

        function get( $key ){
            $memoryHandler = shmop_open( $this->key, $this->flags, $this->mode, $this->size );
            $data = unserialize( shmop_read( $memoryHandler, 0, $this->size ) );
            shmop_close( $memoryHandler );

            if( is_array( $data ) )
                return $data[$key];

            return null;
        }

        function set( $key, $value ){
            $memoryHandler = shmop_open( $this->key, $this->flags, $this->mode, $this->size );
            $data = shmop_read( $memoryHandler, 0, $this->size );

            if( !is_array( $data ) ){
                $data = Array();
            }

            $data[$key] = $value;

            shmop_write( $memoryHandler, serialize($data), 0 );
            shmop_close( $memoryHandler );

            return true;
        }

        function open(){
            return true;
        }

        function close(){
            return true;
        }

        function destroy( $key, $value ){
            $memoryHandler = shmop_open( $this->key, $this->flags, $this->mode, $this->size );
            $response = shmop_delete( $memoryHandler );
            shmop_close( $memoryHandler );

            return $response;
        }

        function garbageCollect( $lifetime ){
            return true;
        }
    }
