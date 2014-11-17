<?php

class CronLocker extends Singleton {

    private static
        $myPid,
        $lockPid,
        $lockName,
        $lockSuffix = '.lock';

    public static $lockDir = './locks/';

    function __destruct(){
        if( self::$myPid == self::$lockPid )
            self::unlock();
    }

    private static function getPsString( $pid ) {
        return exec( "ps {$pid} | grep {$pid}" );
    }

    private static function isRunning( $pid ) {
        return (bool)self::getPsString( $pid );
    }

    public static function lock($lockName = null) {
        self::$lockName = $lockName;

        self::$myPid = getmypid();

        $lockFile = self::getInstance()->getLockFile();

        if( file_exists( $lockFile ) ) {
            self::$lockPid = file_get_contents( $lockFile );

            if(self::$lockPid === false || self::isRunning( self::$lockPid ) ) {
                return false;
            }
        }

        file_put_contents( $lockFile, self::$myPid );
        chmod( $lockFile, 0777 );
        self::$lockPid = self::$myPid;
        return true;
    }

    public static function unlock() {
        $lockFile = self::getInstance()->getLockFile();
        if( file_exists( $lockFile ) )
            unlink( $lockFile );

        return TRUE;
    }

    public function getLockFile() {
        if (!self::$lockName) {
            global $argv;
            self::$lockName = self::$lockDir . $argv[0];
        }
        return self::$lockName . self::$lockSuffix;
    }

    static function getMyPid(){
        return self::$myPid;
    }
}