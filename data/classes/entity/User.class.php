<?php

/**
 * @method static UserManager getManager()
 */
class User extends fvUser implements iLogger{

    static function getEntity(){
        return __CLASS__;
    }

    function check_acl( $acl_name, $action = 'index' ){
        if( $this->isRoot() )
            return true;

        if( !is_array( $acl_name ) )
            $acl_name = array( $acl_name );

        if( is_array( $acl_name[$action] ) )
            $acl_check = $acl_name[$action];
        else
            $acl_check = $acl_name;

        return ( count( array_intersect( $this->group->permissions->get(), $acl_check ) ) > 0 );
    }

    function add_acl( $acl_name, $action = "index" ){
        if( !is_array( $acl_name ) )
            $acl_name = array( $acl_name );

        if( is_array( $acl_name[$action] ) )
            $acl_check = $acl_name[$action];
        else
            $acl_check = $acl_name;

        $this->permissions = array_replace( $this->group->permissions->get(), $acl_check );
    }

    function remove_acl( $acl_name, $action = "index" ){
        if( !is_array( $acl_name ) )
            $acl_name = array( $acl_name );

        if( is_array( $acl_name[$action] ) )
            $acl_check = $acl_name[$action];
        else
            $acl_check = $acl_name;

        $this->permissions = array_diff( $this->permissions->get(), $acl_check );
    }

    function isRoot(){
        return $this->isRoot->get();
    }

    function __toString(){
        return $this->getFullName();
    }

    function getLogin(){
        return $this->login->get();
    }

    function getFullName(){
        $name = $this->name->get();

        if( empty( $name ) )
            return "Anonymous";

        return $name;
    }

    public function getLogMessage( $operation ){
        $message = "Пользователь был ";
        switch( $operation ){
            case Log::OPERATION_INSERT:
                $message .= "создан ";
                break;
            case Log::OPERATION_UPDATE:
                $message .= "изменен ";
                break;
            case Log::OPERATION_DELETE:
                $message .= "удален ";
                break;
            case Log::OPERATION_ERROR:
                $message = "Произошла ошибка при операции с записью ";
                break;
        }

        $message .= "в " . date( "Y-m-d H:i:s" );

        $user = fvSite::$fvSession->getUser();
        if( $user instanceof User )
            $message .= ". Менеджер [" . $user->getPk() . "] " . $user->getLogin() . " (" . $user->getFullName() . ")";

        return $message;
    }

    public function getLogName(){
        return (string)$this->name;
    }

    function getUrl(){
        return fvSite::$fvConfig->langRoot() . "users/{$this->getPk()}/edit";
    }

    public function putToLog( $operation ){
        $logMessage = new Log();
        $logMessage->operation = $operation;
        $logMessage->objectType = __CLASS__;
        $logMessage->objectName = $this->getLogName();
        $logMessage->objectId = $this->getPk();
        $logMessage->managerId = ( fvSite::$fvSession->getUser() ) ? fvSite::$fvSession->getUser()->getPk() : -1;
        $logMessage->message = $this->getLogMessage( $operation );
        $logMessage->editLink = fvSite::$fvConfig->get( 'dir_web_root' ) . "usergroups/edit/?id=" . $this->getPk();
        $logMessage->save();
    }


    function isDeletable(){
        if( $this->getPk() == fvSite::$fvSession->getUser()->getPk() )
            return false;

        return true;
    }
}
