<?php

class TouristsModule extends fvModule{

    function __construct(){
        $this->moduleName = strtolower(substr(__CLASS__, 0, -6));
        parent::__construct(fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.template"),
                            fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.compile"),
                            fvSite::$Layoult);
    }

    function showIndex(){
        $table = new Component_Table( Emp_Person::getManager());
        $this->table = $table
            ->setTitle("Туристы")
            ->setModule($this->moduleName)
            ->addRelation("phones")
            ->addOrder("id desc")
            ->preload();
        return $this->__display( "index.tpl" );
    }

    function showDelete(){
        try {
            Emp_Person::getManager()
                ->getByPk( $this->getRequest()->id )
                ->delete();
            return json_encode(
                array(
                    "msg" => "Запись успешно удалена",
                    "msgtype" => "alert-success",
                    "action" => "remove"
                )
            );
        }
        catch(Exception $e){
            return json_encode(
                array(
                    "msg" => "Ошибка удаления записи",
                    "msgtype" => "alert-error"
                )
            );
        }
    }

    function showEdit() {
        $entity = Emp_Person::getManager()->getByPk($this->getRequest()->id, true);

        $form = new Component_Edit( $entity );
        $form->setAction("/tourists/save/{$this->getRequest()->id}");

        if($entity->isNew())
            $form->setTitle("Создание записи туриста");
        else
            $form->setTitle("Редактирование записи туриста");

        $this->form = $form;
        return $this->__display("edit.tpl");
    }

    function showSave() {
        try {
            $entity = Emp_Person::getManager()->getByPk($this->getRequest()->id, true);
            $redirect = $this->getRequest()->redirect;
            $entity->hydrate( $this->getRequest()->getRequestParameter("data", "array", array()) );
            if(!$entity->isValid()){
                throw new Exception( json_encode( $entity->getShortValidation() ) );
            }
            $entity->save();


            $return = array(
                    "msg" => "Запись сохранена успешно",
                    "msgclass" => "alert-success"
                );
            $return["direction"] = ($redirect == 1) ? "/tourists" : "/tourists/edit/{$entity->getPk()}";

            return json_encode($return);

        }
        catch (Exception $e) {
            return json_encode( array(
                    "msg" => $e->getMessage(),
                    "msgclass" => "alert-error",
                    "direction" => "/tourists/edit/{$entity->getPk()}"
                )
            );
        }


    }

}
