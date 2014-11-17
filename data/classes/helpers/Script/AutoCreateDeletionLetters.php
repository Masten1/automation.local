<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_AutoCreateDeletionLetters extends Script_Iterator {
    const CRON_CALLABLE = true;

    private $letterKey;
    private $letterType;

    /**
     * @var int[]
     */
    private $processedUrlLinks = array();

    /**
     * @var DeletionLetterGrouper
     */
    private $_letterGrouper;

    function __construct() {
        $this->_letterGrouper = new DeletionLetterGrouper();
        parent::__construct();
    }

    function execute() {
        $this->itemsCount = $this->getCount();
        if ($this->itemsCount) {
            $this->log->notice("Found {$this->itemsCount} elements");
            $types =  DeletionTemplate::getTypes();
            unset($types[DeletionTemplate::TYPE_L_KEY]);
            foreach( $types as $letterKey => $letter ){
                $this->setLetterKey( $letterKey );
                $this->startIteration();
            }
        }
        $this->finally();
    }

    function getQuery() {
        $q =  UrlLink::getManager()->query('u')
            ->join('domain', 'd')
            ->join('d.site', 's')
            ->join('project', 'p')
            ->loadRelation('domain d')
            ->loadRelation('d.site')
            ->loadRelation('d.hoster')
            ->loadRelation('d.provider')
            ->where("u.status = :status", array( 'status' => UrlLink::STATE_WORK ))
//            ->andWhere('(s.autosendLetters & :key) AND (s.donotsendLetters & :key = 0)', array( 'key' => $this->getLetterKey() ))
//            ->andWhereNotIn( "u.id", $this->processedUrlLinks )
            ->andWhere('p.deleteActive = 1 and p.fictitiousDeletion != 1 and p.deleteStatus = :ps', array('ps' => Project::STATE_OK));
        return $q;
    }

    /**
     * @param UrlLink $link
     */
    function executeIteration( $link ) {
        try{
            if (in_array($link->getPk(), $this->getProcessedUrlLinks())) {
                return;
            }
            elseif(!$link->domain->site->isAutoSendLetter( $this->getLetterType() ) || !$link->domain->site->canSendLetter( $this->getLetterType() ) ){
                // Тут происходит дополнительная проверка от сущнрости на то что мы таки можем создать ебанное письмо по такому ключу такому сайту.
                return;
            } elseif( $link->isInCoolDown() ) {
                $this->addProcessedUrlLink($link->getPk());
            } elseif( !$link->isLetterSent( $this->getLetterType() ) ){
                try {
                    $this->log->notice("Link #{$link->getPk()}: {$this->getLetterType()}");
                    $this->addDeletionLetter( $link );
                } catch (Exception $e) {
                    $this->log->error("Error link #{$link->getPk()}: {$this->getLetterType()}: " . $e->getMessage());
                }
                $this->addProcessedUrlLink($link->getPk());
            }
        } catch( Exception $e ){
            $this->log->error("Error: " . $e->getMessage());
        }
    }

    function finally() {
        $this->_letterGrouper->sendAll();
        new Script_DeletionLetterStatus($this->getProcessedUrlLinks());
        $this->processedUrlLinks = array();
    }

    /**
     * @param string $letterType
     * @return Script_AutoCreateDeletionLetters
     */
    public function setLetterType($letterType)
    {
        $this->letterType = $letterType;
        return $this;
    }

    /**
     * @return string
     */
    public function getLetterType()
    {
        return $this->letterType;
    }

    /**
     * @param int $letterKey
     * @return Script_AutoCreateDeletionLetters
     */
    public function setLetterKey($letterKey)
    {
        $this->letterKey = $letterKey;
        $this->setLetterType(DeletionTemplate::getTypeByKey($letterKey));
        return $this;
    }

    /**
     * @return int
     */
    public function getLetterKey()
    {
        return $this->letterKey;
    }

    /**
     * @param int $processedUrlLinks
     */
    public function addProcessedUrlLink($processedUrlLinks)
    {
        $this->processedUrlLinks[] = $processedUrlLinks;
    }

    /**
     * @return int[]
     */
    public function getProcessedUrlLinks()
    {
        return $this->processedUrlLinks;
    }

    /**
     * @param UrlLink $link
     */
    function addDeletionLetter( UrlLink $link ){
        $this->_letterGrouper->addUrlLink($link, $this->getLetterType());
    }




}