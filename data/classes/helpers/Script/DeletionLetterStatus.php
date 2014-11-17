<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_DeletionLetterStatus extends Script_Iterator {
    const CRON_CALLABLE = false;

    private $urlLinks = array();
    private $notDeletedLinks = 0;

    function __construct($urlLinks = array()) {
        $this->urlLinks = $urlLinks;
        parent::__construct();
    }

    function getQuery() {
        return UrlLink::getManager()->query('u')
            //->join('domain', 'd')
            //->join('s.site', 's')
            ->where("u.status = :status", array( 'status' => UrlLink::STATE_WORK ))
            ->andWhereNotIn( "u.id", $this->urlLinks )
            //->loadRelation('domain d')
            //->loadRelation('s.site')
            ;
    }

    function executeIteration($link) {
        /**
         * @var UrlLink $link
         */
        $this->log->notice("Id #{$link->getPk()}: All sent: {$link->isAllLettersSent()} InCoolDown:{$link->isInCoolDown()}" );
        if( $link->isAllLettersSent() && !$link->isInCoolDown() ){
            $link->status = UrlLink::STATE_NOT_DELETED;
            $link->save();
            $this->notDeletedLinks++;
        }
    }

    function finally() {
        $this->log->notice("Moved {$this->notDeletedLinks} links.");
    }
}