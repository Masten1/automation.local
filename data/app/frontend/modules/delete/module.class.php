<?php

class DeleteModule extends fvModule{

    function __construct(){
        $this->moduleName = strtolower( substr( __CLASS__, 0, -6 ) );
        parent::__construct( fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.template" ),
                             fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.compile" ),
                             fvSite::$Layoult );
    }

    function showIndex(){
        try {
            $entity = fvManagersPool::get($this->getRequest()->entity)
                ->getByPk( $this->getRequest()->id);

            if ($entity->hasField('isActive')) {
                $entity->isActive = false;
                $entity->save();
            } else {
                $entity->delete();
            }
            return json_encode( array(
                "msg" => "Запись успешно удалена",
                "msgclass" => "alert-success",
                "action" => "remove"
            ) );
        }
        catch(Exception $e){
            return json_encode( array(
                "msg" => "Ошибка удаления записи",
                "msgclass" => "alert-danger"
            ) );
        }

    }
}
