<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_AutoCreateDeletionLettersLawyer extends Script_Iterator {
    const CRON_CALLABLE = true;

    /**
     * @var DeletionLetterGrouper
     */
    private $_letterGrouper;

    function __construct() {
        $this->_letterGrouper = new DeletionLetterGrouper();
        parent::__construct();
    }

    function getQuery() {
        $subQuery = UrlLinkToLetter::getManager()->query('ul')
            ->setSelect('ul.UrlLinkId')
            //->useQmodifiers(false)
            ->join('letter', 'l')
            ->join('l.template', 't')
            ->where('t.type = '.DeletionTemplate::TYPE_L_KEY)
            ->groupBy(null);

        $q =  UrlLink::getManager()->query('u')
            //->useQmodifiers(false)
            ->join('domain', 'd')
            ->join('project', 'p')
            ->join('d.hoster', 'h')
            ->join('h.lawyer', 'l')
            ->where("u.status = :status", array( 'status' => UrlLink::STATE_NOT_DELETED ))
            ->andWhere('p.deleteActive = 1 and p.fictitiousDeletion != 1 AND p.deleteStatus = :ps', array('ps' => Project::STATE_OK))
            ->andWhere('p.sendLawyerLetter = 1 AND l.autoSendLetters = 1')
            ->andWhereNotIn('u.id', $subQuery)
            ->loadRelation('domain d')
            ->loadRelation('d.hoster h')
            ->loadRelation('h.lawyer');
        return $q;
    }

    /**
     * @param UrlLink $link
     */
    function executeIteration( $link ) {
        try {
            $this->addDeletionLetter( $link );
            $this->log->notice("Link #{$link->getPk()} added");
        } catch (Exception $e) {
            $this->log->error("Error link #{$link->getPk()}: " . $e->getMessage());
        }
    }

    function finally() {
        $this->_letterGrouper->sendAll();
    }

    /**
     * @param UrlLink $link
     */
    function addDeletionLetter( UrlLink $link ){
        $this->_letterGrouper->addUrlLink($link, DeletionTemplate::TYPE_L);
    }




}