<?php

class UserGroupsAction extends fvAction {

    function __construct() {
        parent::__construct( fvSite::$Layoult );
    }

    function executeIndex() {
        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_AJAX_CALL;
        else
            return self::$FV_OK;
    }

    function executeEdit() {
        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_AJAX_CALL;
        else
            return self::$FV_OK;
    }

    function executeSave() {
        try {
            $request = fvRequest::getInstance();

            $id = $request->getRequestParameter( 'id', "int", "0" );
            $UserGroup = UserGroup::getManager()->getByPk( $id, true );
            $mg = $request->getRequestParameter( 'mg' );

            if ( $UserGroup->isDefault->get() && empty( $mg[ "isDefault" ] ) ) {
                throw new Exception( "Группу по умолчанию нельзья отменить. Возможно только сменить." );
            }

            $UserGroup->hydrate( $mg );

            if ( $UserGroup->isValid() ) {
                $UserGroup->save();

                if ( $UserGroup->isDefault->get() ) {
                    UserGroup::getManager()
                        ->update()
                        ->where( "id <> :id", array('id' => $UserGroup->getPk()))
                        ->set(array( 'isDefault' => 0 ))
                        ->execute();
                }

                $this->setFlash( "Данные успешно сохранены", self::$FLASH_SUCCESS );
                fvResponse::getInstance()->setHeader( 'Id', $UserGroup->getPk() );
            } else {
                fvResponse::getInstance()->setHeader( 'X-JSON', json_encode( $UserGroup->getValidationResult() ) );
                $this->setFlash( "Ошибка при сохранении данных проверте правильность введенных данных", self::$FLASH_ERROR );
            }

            if ( $request->getRequestParameter( 'redirect' ) )
                fvResponse::getInstance()->setHeader( 'redirect', fvSite::$fvConfig->get( 'dir_web_root' ) . $request->getRequestParameter( 'module' ) . "/" );
        }
        catch ( Exception $e ) {
            $this->setFlash( $e->getMessage(), self::$FLASH_ERROR, $e->getMessage() . "<br/>" . $e->getTraceAsString() );
        }

        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_AJAX_CALL;
        else
            return self::$FV_OK;
    }

    function executeDelete() {
        $request = fvRequest::getInstance();
        if ( !$UserGroup = UserGroup::getManager()->getByPk( $request->getRequestParameter( 'id' ) ) ) {
            $this->setFlash( "Ошибка при удалении.", self::$FLASH_ERROR );
        }
        else {
            $UserGroup->delete();
            $this->setFlash( "Данные успешно удалены", self::$FLASH_SUCCESS );
        }

        fvResponse::getInstance()->setHeader( 'redirect',
                fvSite::$fvConfig->get( 'dir_web_root' ) . fvRequest::getInstance()->getRequestParameter( 'module' ) . "/?page=" . $this->getRequest()->getRequestParameter( 'page', 'int', 0 ) );
        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_NO_LAYOULT;
        else
            return self::$FV_OK;
    }

    function executeGetparams() {
        if ( !fvRequest::getInstance()->isXmlHttpRequest() )
            return false;

        $Group = UserGroup::getManager()->getByPk( fvRequest::getInstance()->getRequestParameter( "group_id" ) );

        if ( !($Group instanceof UserGroup) )
            return false;

        fvResponse::getInstance()->setHeader( 'X-JSON', json_encode( $Group->permissions ) );
        return self::$FV_AJAX_CALL;
    }

}

