<?php
    /**
    * Кеширование данных при помощи файлов.
    * @version 1.1
    * @since 2011/09/06
    * @author Korniev Zakhar
    */
    class fvCache
    {
        /**
        * Кеширует данные, которые содержатся в $data
        * Сохраняет в файл, до которого можно будет добраться по ключу $key
        * @param string $key
        * @param mixed $data
        * @return boolean
        */
        public static function setCache( $key, $data )
        {
            try
            {
                $pFile = fopen( self::getCacheName( $key ), "w+" );
                fwrite( $pFile, serialize( $data ) );
                fclose( $pFile );
            }
            catch( Exception $e )
            {
                return false;
            }
        }
        
        /**
        * Возвращает из кеша данные па ключю $key
        * Если кеша не найдено либо его срок истёк, то возвращаем $default
        * @param string $key
        * @param mixed $default
        * @returns mixed
        */
        public static function getCache( $key, $default = false )
        {
            if( !self::checkCache( $key ) )        
                return $default;
                
            $content = file_get_contents( self::getCacheName( $key ) );
            return unserialize( $content );
        }
        
        /**
        * Удаляет кеш по ключу $key
        * @param string $key
        * @returns bool
        */
        public static function clearCache( $key )
        {
            @unlink( self::getCacheName( $key ) );
        }
        
        /**
        * Формирует имя для кеш-файла
        * @param string $key
        * @returns string - path to file
        */
        static function getCacheName( $key )
        {
            $pathToFile = fvSite::$fvConfig->get( "cache.path" );
            $fileName = md5( $key ) . ".cache";
            
            return $pathToFile . $fileName;
        }
        
        /**
        * Время жизни кеша
        * @returns int
        */
        static function getCacheTTL()
        {
            return intval( fvSite::$fvConfig->get( "cache.ttl" ) );
        }
        
        static function checkCache( $key )
        {
            $fileName = self::getCacheName( $key );
            
            // Если кеш не создан - выход
            if( !file_exists( $fileName ) )
                return false;
                
            //Если кеш истек, то выход
            if( time() - filemtime( $fileName ) > self::getCacheTTL() )   
                return false;
                
            return true;  
            
        }
    }

?>
