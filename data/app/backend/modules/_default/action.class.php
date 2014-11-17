<?php

class DefaultAction extends fvAction {

    protected $entity_name;

    /**
     * Current entity exemplar.
     * @var fvRoot
     */
    protected $entity;

    /**
     * Entity manager
     * @var fvRootManager
     */
    protected $manager;

    function __construct( $module ) {
        parent::__construct( fvSite::$Layoult );

        $this->entity_name = fvSite::$fvConfig->get( "modules.{$module}.entity" );
        $this->entity = new $this->entity_name;
        $this->manager = fvManagersPool::get( $this->entity_name );
    }

    function executeIndex() {
        if ( !fvRequest::getInstance()->isXmlHttpRequest() ) {
            return self::$FV_OK;
        }
        else {
            return self::$FV_AJAX_CALL;
        }
    }

    function executeEdit() {
        if ( !fvRequest::getInstance()->isXmlHttpRequest() ) {
            return self::$FV_OK;
        }
        else {
            return self::$FV_AJAX_CALL;
        }
    }

    function executeEditWindow() {
        if ( !fvRequest::getInstance()->isXmlHttpRequest() ) {
            return self::$FV_OK;
        }
        else {
            return self::$FV_AJAX_CALL;
        }
    }

    function executeData() {
        if ( !fvRequest::getInstance()->isXmlHttpRequest() ) {
            return self::$FV_OK;
        }
        else {
            return self::$FV_AJAX_CALL;
        }
    }

    /**
     * Делаем is_active=!is_active  и сохраняем
     */
    function executeActivate() {
        try {
            $id = $this->getRequest()->getRequestParameter( "id", 'int', 0 );
            $subject = $this->manager->getByPk( $id );
            if ( !$subject instanceof $this->entity ) {
                throw new Exception( "Не существует такой записи!" );
            }

            $subject->is_active = !$subject->is_active;
            if ( $subject->save() ) {
                $this->setFlash( "Изменения внесены.", self::$FLASH_SUCCESS );
                return self::$FV_AJAX_CALL;
            }

            throw new Exception( "Не удается сохранить изменения" );
        }
        catch ( Exception $e ) {
            $this->setFlash( $e->getMessage(), self::$FLASH_ERROR );
        }
        return self::$FV_AJAX_CALL;
    }

    /**
     * Сохранение
     */
    function executeSave() {
        try {
            $id = $this->getRequest()->getRequestParameter( "id", "int", 0 );
            $subject = $this->manager->getByPk( $id, true );

            $data = $this->getRequest()->getRequestParameter( 'data', 'array', array( ) );


            $subject->hydrate( $data, true );

            if ( $subject->isValid() ) {
                $subject->save();

                $this->setFlash( "Данные успешно сохранены", self::$FLASH_SUCCESS );

                if ( $this->getRequest()->getRequestParameter( 'redirect' ) )
                    fvResponse::getInstance()->setHeader( 'redirect', fvSite::$fvConfig->get( 'dir_web_root' ) . $this->getRequest()->getRequestParameter( 'module' ) );
                else
                    fvResponse::getInstance()->setHeader( 'redirect',
                            fvSite::$fvConfig->get( 'dir_web_root' ) . $this->getRequest()->getRequestParameter( 'module' ) . "/edit/?id=" . $subject->getPk() . "&rand" . rand() );
            } else {

                fvResponse::getInstance()->setHeader( 'X-JSON', json_encode( $subject->getValidationResult() ) );
                throw new Exception( "Ошибка при сохранении данных проверьте правильность введенных данных" );
            }
        }
        catch ( Exception $ex ) {
            $this->setFlash( $ex->getMessage(), self::$FLASH_ERROR, $ex->getTraceAsString() );
        }

        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_AJAX_CALL;
        else
            return self::$FV_OK;
    }

    function executeSaveAjax() {
        $win_entity_name = $this->getRequest()->getRequestParameter('win_entity');
        $manager = fvManagersPool::get($win_entity_name);
        try {
            $id = $this->getRequest()->getRequestParameter( "id", "int", 0 );
            $subject = $manager->getByPk( $id, true );

            $data = $this->getRequest()->getRequestParameter( 'data', 'array', array( ) );


            $subject->hydrate( $data, true );

            if ( $subject->isValid() ) {
                $subject->save();

                $this->setFlash( "Данные успешно сохранены", self::$FLASH_SUCCESS );

                if ( $this->getRequest()->getRequestParameter( 'redirect' ) )
                    fvResponse::getInstance()->setHeader( 'redirect', fvSite::$fvConfig->get( 'dir_web_root' ) . $this->getRequest()->getRequestParameter( 'module' ) );
                else
                    fvResponse::getInstance()->setHeader( 'redirect',
                        fvSite::$fvConfig->get( 'dir_web_root' ) . $this->getRequest()->getRequestParameter( 'module' ) . "/edit/?id=" . $subject->getPk() . "&rand" . rand() );
            } else {

                fvResponse::getInstance()->setHeader( 'X-JSON', json_encode( $subject->getValidationResult() ) );
                throw new Exception( "Ошибка при сохранении данных проверте правильность введенных данных" );
            }
        }
        catch ( Exception $ex ) {
            $this->setFlash( $ex->getMessage(), self::$FLASH_ERROR, $ex->getTraceAsString() );
        }

        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_AJAX_CALL;
        else
            return self::$FV_OK;
    }

    /**
     * Удаление сущности
     */
    function executeDelete() {
        if ( !$subject = $this->manager->getByPk( $this->getRequest()->getRequestParameter( 'id', 'int', 0 ) ) ) {
            $this->setFlash( "Ошибка при удалении.", self::$FLASH_ERROR );
        }
        else {
            $subject->delete();
            $this->setFlash( "Данные успешно удалены", self::$FLASH_SUCCESS );
        }

        $request = fvRequest::getInstance();
        fvResponse::getInstance()->setHeader( 'redirect', fvSite::$fvConfig->get( 'dir_web_root' ) . $request->getRequestParameter( 'module' ) . "/");

        if ( fvRequest::getInstance()->isXmlHttpRequest() )
            return self::$FV_NO_LAYOULT;
        else
            return self::$FV_OK;
    }

    function executeGetForeign() {
        if ( fvRequest::getInstance()->isXmlHttpRequest() ) {
            if ( !$referencesField = $this->getRequest()->getRequestParameter( 'references', 'string', false ) ) {
                $this->setFlash( "Такого поля не существует.", self::$FLASH_ERROR );
            }
            else {
                $references = $this->manager->getEntity()->getFields( 'Field_References' );
                $reference_exists = false;
                foreach ( $references as $reference ) {
                    if ( $reference->getKey() == $referencesField ) {
                        $reference_exists = true;
                        break;
                    }
                }

                if ( !$reference_exists ) {
                    $this->setFlash( "Такого поля не существует.", self::$FLASH_ERROR );
                }
                else {
                    $this->_request->reference = $reference;
                    return self::$FV_AJAX_CALL;
                }
            }
        }
        else {
            $this->redirect404();
            return self::$FV_OK;
        }
    }

    function executeResize(){
        $path = $this->getRequest()->getRequestParameter( "path" );
        $params = $this->getRequest()->getRequestParameter( "params" );

        $path = str_replace( "//", "/", fvSite::$fvConfig->get( "tech_web_root" ) . $path );

        $data = array(
            "resize_type" => fvMediaLib::THUMB_EXACT,
            "width" => $params["w"],
            "height" => $params["h"],
            "offsetX" => $params["x"],
            "offsetY" => $params["y"]
        );

//        var_dump( $path, $path, $data );
        fvMediaLib::createThumbnail( $path, $path, $data );

        return self::$FV_AJAX_CALL;
    }

    function executeAddConstraint() {
        return self::$FV_AJAX_CALL;
    }

    function executeRmvConstraint() {
        $cId = $this->getRequest()->getRequestParameter('cid');
        $name = $this->getRequest()->getRequestParameter('name');

        fvManagersPool::get($name)->getByPk($cId)->delete();

        return self::$FV_AJAX_CALL;
    }
}