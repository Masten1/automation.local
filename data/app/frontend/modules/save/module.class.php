<?php

class SaveModule extends fvModule{

    function __construct(){
        $this->moduleName = strtolower( substr( __CLASS__, 0, -6 ) );
        parent::__construct( fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.template" ),
                             fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.compile" ),
                             fvSite::$Layoult );
    }

    function showIndex(){
        try {
            $dataSource = $this->getRequest()->getRequestParameter("data");
            $data = $this->getRequest()->getRequestParameter($dataSource);
            $name = $this->getRequest()->entity;
            $entity = new $name;

            $entity->hydrate($data);

            if(!$entity->save())
                throw new Exception();

            return json_encode( array(
                "msg" => "Запись успешно сохранена",
                "msgclass" => "alert-success",
                "action" => "append",
                "toappend" => $entity->asAdornedEdit()->render()
            ) );
        }
        catch(ValidationException $e) {
            return json_encode( array(
                "msg" => $e->getMessage(),
                "msgclass" => "alert-danger"
            ) );
        }
        catch(Exception $e){
            return json_encode( array(
                "msg" => "Ошибка cохранения",
                "msgclass" => "alert-danger"
            ) );
        }

    }
}
