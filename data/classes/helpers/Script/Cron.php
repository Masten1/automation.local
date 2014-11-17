<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_Cron extends Script_Iterator {
    const CRON_CALLABLE = false;

    function getQuery() {
        return CronTab::getManager()->select();
    }

    function executeIteration($cron) {
        /**
         * @var CronTab $cron
         */
        if ($cron->isCallTime()) {
            $this->log->notice("Calling {$cron->script->get()}");
            $cron->callScriptInThread();
        }
    }

    /**
     * @return string
     */
    protected function getPath(){
        $path = str_replace('//', '/',
            sprintf( "/scripts/%s/%s/",
                $this->getScriptName() ,
                date( "Y" )));

        $fullPath = str_replace('//', '/',FV_ROOT . "logs/".$path);
        if( !is_dir( $fullPath ) ){
            mkdir( $fullPath, 0777, true );
        }

        $path .= $this->getFileName();

        return $path;
    }

    /**
     * @return string
     */
    protected function getFileName(){
        return date( "Y-m-d" );
    }
}