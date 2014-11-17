<?php

class VehicleAction extends fvAction {

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

    /**
     * Сохранение
     */
    function executeSave() {
        try {
            $id = $this->getRequest()->getRequestParameter( "id", "int", 0 );
            $subject = $this->manager->getByPk( $id, true );

            $data = $this->getRequest()->getRequestParameter( 'data', 'array', array( ) );

            $tiers = $this->getRequest()->getRequestParameter('tier', 'array', array() );

            $subject->hydrate( $data );

            if ( $subject->isValid() ) {
                $subject->save();

                foreach($tiers as $pk => $data) {
                    $reset = reset($data);
                    if ($reset['rows']) {
                        $vehicleTier = Emp_VehicleTier::getManager()->getByPk( key($data), true );
                        $vehicleTier->name = $pk;
                        $vehicleTier->rows = $reset['rows'];
                        $vehicleTier->vehicleId = $subject->getPk();
                        $vehicleTier->save();
                    }
                }


                if(!$subject->secondTier->get()) {
                    $tier = reset($subject->tiers->get( "name = 2" ));
                    if($tier instanceof fvRoot) {
                        $tier->delete();
                    }
                }


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

    function executeSeat(){
        $data = $this->getRequest()->getRequestParameter("data");
        $number = $this->getRequest()->getRequestParameter("number", "int", "0");
        $comment = $this->getRequest()->getRequestParameter("comment");

        $tier = Emp_VehicleTier::getManager()->getByPk($data['tierId']);
        $seat = $tier->getSeatObject($data);

        if($number) {
            $seat->number = $number;
            $seat->comment = $comment;
            $seat->save();
        }
        else {
            $seat->delete();
        }

        return self::proceed();

    }
}