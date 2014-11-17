<?php
/**
 * User: apple
 * Date: 15.08.12
 * Time: 18:29
 */
abstract class Script_Endless extends Script_Iterator {

    protected $sleepTime = 10;
    protected $memoryLimit = 300;
    protected $killEvery = 24; // IN HOURs
    protected $repeatAnnounce = 3600;

    function __construct(){
        ini_set('memory_limit', $this->memoryLimit . 'M');

        parent::__construct();
    }

    function execute() {
        $this->prepare();
        $lastActivity = time();
        $startTime = time();

        while( true ){
            parent::execute();

            if( $this->itemsCount ){
                $memoryUsed = memory_get_usage() / 1024 / 1024;

                $lastActivity = time();
                $this->log->memory( sprintf( "%.1fM", $memoryUsed ) );

                if( $this->droppedDueMemory( $memoryUsed ) ){
                    $this->log->script("Dropped due memory limit");
                    return;
                }

                if(gc_enabled()){
                    if( $removed = gc_collect_cycles() ){
                        $this->log->memory("Garbage collect removed {$removed} links.");
                    }
                }
            } else
                sleep( $this->sleepTime );

            if( time() - $lastActivity > $this->repeatAnnounce ){
                $this->log->script("Still waiting...");
                $lastActivity = time();
            }

            if( time() - $startTime > 60*60*$this->killEvery ){
                $this->log->script("Kill timeout");
                break;
            }
        }
    }

    protected function droppedDueMemory( $memoryUsed ){
        return ($this->memoryLimit - $memoryUsed) < $this->memoryLimit * .1;
    }

    protected function prepare() {}
}
