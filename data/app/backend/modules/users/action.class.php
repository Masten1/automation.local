<?php

class UsersAction extends fvAction {

    function __construct() {
        parent::__construct( fvSite::$Layoult );
    }

    function executeIndex() {
        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_NO_LAYOULT;
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
        $request = fvRequest::getInstance();

        $User = User::getManager()->getByPk( $request->getRequestParameter( 'id' ), true );

        $isNew = $User->isNew();

        $m = $request->getRequestParameter( 'm' );

        $m[ 'isRoot' ] = ( $m[ 'isRoot' ] ) ? 1 : 0;
        $m[ 'isActive' ] = ( $m[ 'isActive' ] ) ? 1 : 0;
        $m[ 'inherit' ] = ( $m[ 'inherit' ] ) ? 1 : 0;

        if ( !$User->isNew() && ( strlen( $m[ 'password' ] ) == 0 ) ) {
            unset( $m[ 'password1' ] );
            unset( $m[ 'password' ] );
        } elseif ( $m[ 'password1' ] != $m[ 'password' ] ) {
            fvResponse::getInstance()->setHeader( 'X-JSON', json_encode( Array( Array( "Пароли не совпадают" ) ) ) );
            $this->setFlash( "Ошибка при сохранении данных проверте правильность введенных данных", self::$FLASH_ERROR );
        }
        else
            unset( $m[ 'password1' ] );

        $User->hydrate( $m );

        if ( $User->isValid() ) {
            $User->save(true);

            fvResponse::getInstance()->setHeader( 'Id', $User->getPk() );
            $this->setFlash( "Данные успешно сохранены", self::$FLASH_SUCCESS );
        }
        else {
            fvResponse::getInstance()->setHeader( 'X-JSON', json_encode( $User->getValidationResult() ) );
            $this->setFlash( "Ошибка при сохранении данных проверте правильность введенных данных", self::$FLASH_ERROR );
        }
        if ( $request->getRequestParameter( 'redirect' ) || $isNew ) {
            fvResponse::getInstance()->setHeader( 'redirect', fvSite::$fvConfig->get( 'dir_web_root' ) . $request->getRequestParameter( 'module' ) . "/" );
        }


        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_AJAX_CALL;
        else
            return self::$FV_OK;
    }

    function executeDelete() {
        $request = fvRequest::getInstance();
        if ( !$User = User::getManager()->getByPk( $request->getRequestParameter( 'id' ) ) ) {
            $this->setFlash( "Ошибка при удалении.", self::$FLASH_ERROR );
        }
        else {
            $User->delete();
            $this->setFlash( "Данные успешно удалены", self::$FLASH_SUCCESS );
        }

        fvResponse::getInstance()->setHeader( 'redirect', fvSite::$fvConfig->get( 'dir_web_root' ) . $request->getRequestParameter( 'module' ) . "/" );
        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_NO_LAYOULT;
        else
            return self::$FV_OK;
    }

    function executeGetparameterslist() {
        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_AJAX_CALL;
        else
            $this->redirect404();
    }

}

?>
