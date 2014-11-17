<?php

class UserGroup extends fvRoot implements iLogger {
    
    static function getEntity(){ return __CLASS__; }
    
    public function getLogMessage($operation) {
        $message = "Группа менеджеров была ";
        switch ($operation) {
            case Log::OPERATION_INSERT: $message .= "создана ";break;
            case Log::OPERATION_UPDATE: $message .= "изменена ";break;
            case Log::OPERATION_DELETE: $message .= "удалена ";break;
            case Log::OPERATION_ERROR: $message = "Произошла ошибка при операции с записью ";break;
        }
        $user = fvSite::$fvSession->getUser();
        $message .= "в ".date("Y-m-d H:i:s").". Менеджер [".$user->getPk()."] " . $user->getLogin() . " (" . $user->getFullName() . ")";
        return $message;    
    }
    
    public function getLogName() {
        return (string)$this->name;
    }
    
    public function putToLog($operation) {
        $logMessage = new Log();
        $logMessage->operation = $operation;
        $logMessage->objectType = __CLASS__;
        $logMessage->objectName = $this->getLogName();
        $logMessage->objectId = $this->getPk();
        $logMessage->managerId = (fvSite::$fvSession->getUser())?fvSite::$fvSession->getUser()->getPk():-1;
        $logMessage->message = $this->getLogMessage($operation);
        $logMessage->editLink = fvSite::$fvConfig->get('dir_web_root')."usergroups/edit/?id=".$this->getPk();
        $logMessage->save();
    }

    public function __toString(){
        return (string)$this->name;
    }
}

?>
