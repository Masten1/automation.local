<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cah4a
 * Date: 13.04.12
 * Time: 22:07
 * To change this template use File | Settings | File Templates.
 */
class Locker {

    /** @var \Storage */
    protected
        $_storage,
        $_user;

    function __construct(){
        $this->_storage = Storage::create("memcache", array("lifetime" => 30*60, "prefix" => "lockers"));
        $this->_user = fvSite::$fvSession->getUser()->getPk();
    }

    function lock( $projectId, $siteId ){
        if( $this->isLocked($projectId, $siteId) && !$this->isLockedByMe($projectId, $siteId) )
            throw new Exception("Already locked!");

        $this->_storage->set("$projectId-$siteId", $this->_user);
    }

    function unlock( $projectId, $siteId ){
        if( !$this->isLockedByMe($projectId, $siteId) )
            throw new Exception("Not locked!");

        $this->_storage->set("$projectId-$siteId", "");
    }

    function isLocked( $projectId, $siteId ){
        $lockedBy = $this->_storage->get("$projectId-$siteId");

        return !empty($lockedBy);
    }

    function isLockedByMe( $projectId, $siteId ){
        $lockedBy = $this->_storage->get("$projectId-$siteId");

        return !empty($lockedBy) && ($lockedBy == $this->_user);
    }
}
