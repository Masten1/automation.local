<?php
    /**
     * @author Iceman
     * @since 08.11.12 16:30
     */
    class Storage_File extends Storage{
        public function __construct( $params ){
            $this->pathToFile = $params["file"];
        }

        function get( $key ){
            $values = unserialize( trim( file_get_contents( $this->pathToFile ) ) );
            if ( !is_array( $values ) ){
                return null;
            }

            return $values[$key];
        }

        function set( $key, $value ){
            $handler = fopen( $this->pathToFile, "r+" );

            if( flock( $handler, LOCK_EX ) ){
                $contents = fread( $handler, filesize( $this->pathToFile ) );
                $values = unserialize( $contents );

                if( !is_array( $values ) )
                    $values = Array();

                ftruncate( $handler, 0 );

                $values[$key] = $value;

                fseek( $handler, 0 );
                fwrite( $handler, serialize( $values ) );
                flock( $handler, LOCK_UN );
            }
            else{
                fclose( $handler );
                throw StorageException( "Cannot lock file" );
            }

            fclose( $handler );
            return true;
        }

        function open(){
            return true;
        }

        function close(){
            return true;
        }

        function destroy( $key, $value ){
            $handler = fopen( $this->pathToFile, "r+" );
            ftruncate( $handler, 0 );
            fclose( $handler );

            return true;
        }

        function garbageCollect( $lifetime ){
            return true;
        }
    }
