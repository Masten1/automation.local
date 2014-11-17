<?php

    class DictionaryAction extends fvAction 
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
                if ( !$iDictionary = Dictionary::getManager()->getByPk( $this->getRequest()->getRequestParameter('id') ) ) 
                    $iDictionary = new Dictionary();
                
                $data = $this->getRequest()->getRequestParameter('data', 'array', array() );
                
                $iDictionary->hydrate( $data, true );
                
                if( $iDictionary->isValid() ) {
                    $iDictionary->save();
                    
                    $this->setFlash( "Данные успешно сохранены", self::$FLASH_SUCCESS );
                    if ($this->getRequest()->getRequestParameter('redirect') ) 
                        fvResponse::getInstance()->setHeader('redirect', fvSite::$fvConfig->get('dir_web_root') . $this->getRequest()->getRequestParameter('module') . "/");
                } 
                else
                {
                    fvResponse::getInstance()->setHeader( 'X-JSON', json_encode( $iDictionary->getValidationResult() ) );
                    throw new Exception( "Ошибка при сохранении данных проверте правильность введенных данных" );
                } 
            }
            catch( Exception $ex )
            {
                $this->setFlash( $ex->getMessage(), self::$FLASH_ERROR, $ex->getTraceAsString());        
            }
        
            if (fvRequest::getInstance()->isXmlHttpRequest())
                return self::$FV_AJAX_CALL;
            else return self::$FV_OK;
        }

        function executeDelete() {
            $request = fvRequest::getInstance();
            if (!$iDictionary = Dictionary::getManager()->getByPk($request->getRequestParameter('id'))) {
                $this->setFlash("Ошибка при удалении.", self::$FLASH_ERROR);
            } else {
                $iDictionary->delete();
                $this->setFlash("Данные успешно удалены", self::$FLASH_SUCCESS);
            }

            fvResponse::getInstance()->setHeader('redirect', fvSite::$fvConfig->get('dir_web_root') . $request->getRequestParameter('module') . "/");
            if (fvRequest::getInstance()->isXmlHttpRequest())
                return self::$FV_NO_LAYOULT;
            else return self::$FV_OK;
        }    
    }

?>
