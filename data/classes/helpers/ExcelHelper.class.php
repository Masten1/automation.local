<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 07.06.12
 * Time: 13:02
 */
class ExcelHelper extends PHPExcel
{
    function __construct() {
        parent::__construct();


        if( fvMemCache::getInstance()->checkMemCache() ){
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
            $cacheSettings = array(
                'memcacheServer'  => fvMemCache::host,
                'memcachePort'    => fvMemCache::port,
                'cacheTime'       => 300
            );
            if (!PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings)) {
                throw new Exception('ExcelHelper memcache caching error');
            }
        } else {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
            $cacheSettings = array( 'dir'  => '/usr/local/tmp'
            );
            if (!PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings)) {
                throw new Exception('ExcelHelper file caching error');
            }

        }
    }
}
