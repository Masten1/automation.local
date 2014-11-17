<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_FinderStatus extends Script {
    const CRON_CALLABLE = true;

    function execute() {
        $status = Search_Finder::getStatus();
        $data = array(
            'crawledPages'  => $status->crawled_pages,
            'spidersCount'  => $status->spiders_count,
            'freeSpace'  => $status->free_space,
            'cycleLength'  => $status->cycle_length,
            'cycleCompleted'  => $status->cycle_completed,
            'finishedSpiders'  => $status->finished_spiders,
            'requestsPerMinute'  => $status->requests_per_minute,
            /*'crawledPages'  => 20,
            'spidersCount'  => 2,
            'freeSpace'  => 3,
            'cycleLength'  => 4,
            'cycleCompleted'  => 5,
            'requestsPerMinute'  => 6,*/
        );

        $log  = new FinderStatus();
        $log->hydrate($data);
        $log->save();
    }

    /**
     * @return string
     */
    protected function getFileName(){
        return date( "Y-m" );
    }
}