<?php
/**
 * User: apple
 * Date: 21.08.12
 * Time: 14:31
 */
abstract class fvRootLockable extends fvRoot {
    const LIFETIME = 1800; // 30*60

    private $_user;

    /** @return Storage */
    function getStorage(){
        static $storages = array();

        if( empty($storages[$this->getEntity()]) )
            $storages[$this->getEntity()] = Storage::create("memcache", array("lifetime" => self::LIFETIME, "prefix" => $this->getEntity() . "_Locks"));

        return $storages[$this->getEntity()];
    }

    private function getLockKey(){
        return $this->getPk();
    }

    private function getUser() {
        if( ! $this->_user ){
            if( ! fvSite::$fvSession->getUser() instanceof User )
                throw new Exception("Lock functions not available without login in.");

            $this->_user = fvSite::$fvSession->getUser()->getPk();
        }

        return $this->_user;
    }

    public function lock(){
        if( $this->isLocked() && !$this->isLockedByMe() )
            throw new Exception("Already locked!");

        UserActivity::getManager()->addActivity('lock', $this);

        $this->getStorage()->set($this->getLockKey(), $this->getUser());
    }

    public function unlock(){
        if( !$this->isLockedByMe() )
            throw new Exception("Not locked!");

        UserActivity::getManager()->addActivity('unlock', $this);

        $this->getStorage()->set($this->getLockKey(), "");
    }

    public function isLocked(){
        $lockedBy = $this->getStorage()->get( $this->getLockKey() );

        return !empty($lockedBy);
    }

    public function isLockedByMe(){
        $lockedBy = $this->getStorage()->get( $this->getLockKey() );

        return !empty($lockedBy) && ($lockedBy == $this->getUser());
    }

    public function getLockedUser(){
        $lockedBy = $this->getStorage()->get( $this->getLockKey() );

        if( !empty($lockedBy) ){
            return "Locked by: <b>" . User::getManager()->getByPk( $lockedBy )->getFullName() . "</b>";
        }

        return false;
    }

}
