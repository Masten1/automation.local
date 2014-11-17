<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_AutoStartStop extends Script_Iterator {
    const CRON_CALLABLE = true;

    const TYPE_SEARCH_START = 1;
    const TYPE_SEARCH_STOP = 2;
    const TYPE_DELETE_START = 3;
    const TYPE_DELETE_STOP = 4;

    private $operationType;

    function __construct($operationType) {
        $this->operationType = $operationType;
        parent::__construct();
    }

    function getQuery() {
        return Project::getManager()->select()->where($this->getQueryString());
    }

    /** @var $project Project */
    function executeIteration($project) {
        switch ($this->operationType) {
            case self::TYPE_SEARCH_START :
                $project->searchRun();
                break;
            case self::TYPE_SEARCH_STOP :
                $project->searchStop();
                break;
            case self::TYPE_DELETE_START :
                $project->deleteRun();
                break;
            case self::TYPE_DELETE_STOP :
                $project->deleteStop();
                break;
            default:
                throw new Exception('Icorrect Operation Type');
        }

        $project->save();

        Notification::getManager()->create(
            Notification::SYSTEM_SEARCH,
            Notification::TYPE_WARNING,
            $project,
            $this->getNotificationMessage()
        );
    }

    private function getQueryString() {
        switch ($this->operationType) {
            case self::TYPE_SEARCH_START :
                return 'searchStatus = ' . Project::STATE_PENDING . ' and autoStartDate < NOW()';
            case self::TYPE_SEARCH_STOP :
                return 'searchStatus = ' . Project::STATE_OK . ' and autoStopDate < NOW() ';
            case self::TYPE_DELETE_START :
                return 'deleteStatus = ' . Project::STATE_PENDING . ' and autoStartDate < NOW()';
            case self::TYPE_DELETE_STOP :
                return 'deleteStatus = ' . Project::STATE_OK . ' and autoStopDate < NOW() ';
            default: 
                throw new Exception('Icorrect Operation Type');
        }
    }

    private function getNotificationMessage() {
        switch ($this->operationType) {
            case self::TYPE_SEARCH_START :
                return fvDictionary::getInstance()->get("autoStartSearchProject", "Search started");
            case self::TYPE_SEARCH_STOP :
                return fvDictionary::getInstance()->get("autoStopSearchProject", "Search stopped");
            case self::TYPE_DELETE_START :
                return fvDictionary::getInstance()->get("autoStartDeletionProject", "Deletion started");
            case self::TYPE_DELETE_STOP :
                return fvDictionary::getInstance()->get("autoStopDeletionProject", "Deletion stopped");
            default:
                throw new Exception('Icorrect Operation Type');
        }
    }
}