<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_CheckProcessor extends Script_Endless {
    const CRON_CALLABLE = true;

    const CHECK_TIMEOUT = 3;
    const RECHECK_TIMEOUT = 10;

    private $pkName;

    private $links = array();

    function __construct() {
        $this->perIteration = 50;
        $this->pkName = UrlLink::getManager()->getRootObj()->getPkName();
        parent::__construct();
    }

    function getQuery() {
        static $query;

        if( empty($query) ){
            $query = UrlLink::getManager()
                ->select()
                ->join( 'domain', 'd' )
                ->join( 'd.site', 's' )
                ->where( '((dtime IS NULL AND ( lastCheck IS NULL OR lastCheck < NOW() - INTERVAL :checkTimeout HOUR )) OR (dtime IS NOT NULL AND ( lastCheck IS NULL OR lastCheck < NOW() - INTERVAL :recheckTimeout HOUR ))) AND root.status != :urlLinkStatus',
                         array( 'checkTimeout'   => self::CHECK_TIMEOUT,
                                'recheckTimeout' => self::RECHECK_TIMEOUT,
                                'urlLinkStatus' => UrlLink::STATE_IGNORE
                         ) )
                ->andWhere( 's.autoCheckStatus = :autoCheckStatus', array( 'autoCheckStatus' => Site::AUTOCHEK_OK ) )
                ->join('project', 'p')
                ->andWhere( 'p.deleteActive = 1 AND p.deleteStatus = :deleteStatus', array( 'deleteStatus' => Project::STATE_OK))
                ->loadRelation( 'domain' )
                ->aggregateBy( $this->pkName );
        }

        return $query;
    }

    /**
     * @param UrlLink $link
     */
    function executeIteration($link) {
        $this->log->notice("Checking {$link->link->get()}");
        $isDeleted = $link->checkLinkIsDeleted();
        if ($isDeleted) {
            $this->log->notice("{$link->link->get()} is deleted");
        }

        $this->links[$link->getPk()] = $link;
    }

    function finally() {
        $field = new Field_Datetime( array(), "noop" );
        $field->set( time() );

        UrlLink::getManager()
            ->update()
            ->whereIn( $this->pkName, array_keys( $this->links ) )
            ->set( array( 'lastCheck' => $field->get() ) )
            ->execute();
    }
}