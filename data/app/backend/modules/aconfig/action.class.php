<?php

class AConfigAction extends fvAction
{

    function __construct () {
        parent::__construct(fvSite::$Layoult);
    }

    function executeIndex() {
        if (!fvRequest::getInstance()->isXmlHttpRequest()) {
            return self::$FV_OK;
        } else {
            return self::$FV_AJAX_CALL;
        }
    }

    function executeSave()
    {
        try
        {
            if ( !$iConfig = AConfig::getManager()->getByPk( $this->getRequest()->getRequestParameter('id') ) )
                throw new Exception( "Не существует такой константы!" );

            $data = $this->getRequest()->getRequestParameter('data', 'array', array() );
            $iConfig->hydrate( $data );

            var_dump( $iConfig );

            if ( $iConfig->save() )
            {
                foreach(fvSite::$fvConfig->get("path.application") as $app => $config){
                    fvCacheConfig::getInstance($app . "config/")->dropCache();
                }

                $this->setFlash( "Данные успешно сохранены", self::$FLASH_SUCCESS );
                if ($this->getRequest()->getRequestParameter('redirect') )
                    fvResponse::getInstance()->setHeader('redirect', fvSite::$fvConfig->get('dir_web_root') . $this->getRequest()->getRequestParameter('module') . "/");
            }
            else
            {

                fvResponse::getInstance()->setHeader( 'X-JSON', json_encode( $iConfig->getValidationResult() ) );
                throw new Exception( "Ошибка при сохранении данных проверьте правильность введенных данных" );
            }
        }
        catch( Exception $ex )
        {
            $this->setFlash( $ex->getMessage(), self::$FLASH_ERROR , $ex->getTraceAsString());
        }

        if (fvRequest::getInstance()->isXmlHttpRequest())
            return self::$FV_AJAX_CALL;
        else return self::$FV_OK;
    }
}

?>
