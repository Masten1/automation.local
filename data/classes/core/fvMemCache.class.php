<?php
    /**
    * Класс для работы с MemCache
    * @version 1.0
    * @author Korniev Zakhar
    * @since 2011/09/06
    */
    class fvMemCache
    {
        /**
        * Ссылка на коннект с мемкешем
        * @var Memcache
        */
        private $memCacheHandler;

        const host = 'localhost';
        const port = '11211';


        private function __construct()
        {
            if( $this->checkMemCache() )
            {
                $this->memCacheHandler = new Memcache();
                $this->memCacheHandler->connect( self::host, self::port );
            }
        }

        /**
        * Получить экземпляр
        * @return fvMemCache
        */
        public static function getInstance()
        {
            static $instance;
            if( ! $instance instanceof self )
                $instance = new self();

            return $instance;
        }

        /**
        * Установить кеш
        * @param string $key
        * @param mixed $data
        * @return bool
        */
        public function setCache( $key, $data )
        {
            if ( !self::checkMemCache() )
                return false;

            $ttl = 3600;
            if( fvSite::$fvConfig ){
                $ttl = fvSite::$fvConfig->get( "memcache.ttl" );
            }

            if( ! $this->memCacheHandler->add( $this->getCacheName( $key ), $data, MEMCACHE_COMPRESSED, $ttl ) ){
                return $this->memCacheHandler->replace( $this->getCacheName( $key ), $data, MEMCACHE_COMPRESSED, $ttl );
            }

            return true;
        }

        /**
        * Получить значение по ключу
        * @param string $key
        * @param bool $default
        * @return array
        */
        public function getCache( $key, $default = false )
        {
            if ( !self::checkMemCache() )
                return false;

            $response = $this->memCacheHandler->get( $this->getCacheName( $key ) );
            return ( $response ) ? $response : $default;
        }

        /**
        * Очистить кеш по имени
        * @param string $key
        * @return bool
        */
        public function clearCache( $key )
        {
            if ( !self::checkMemCache() )
                return false;

            return $this->memCacheHandler->delete( $this->getCacheName( $key ), 0 );
        }

        /**
        * Проверить доступен ли мемКеш
        * @return bool
        */
        public function checkMemCache()
        {
            if ( defined( "FV_MEMCACHE_ENABLED" ) && FV_MEMCACHE_ENABLED && class_exists( "Memcache" ) )
                return true;

            return false;
        }

        /**
        * Сформировать имя мемКеш ключа
        * @param string $key
        */
        private function getCacheName( $key )
        {
            return md5( $key );
        }
    }
?>
