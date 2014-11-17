<?php
/**
 * Class: Emp_TouristPhone
 * @author: Andrey
 * Date: 11.10.13
 * Time: 10:47 
 */

class Emp_PersonPhone extends fvRoot {

    function __toString(){
        return (string)$this->name;
    }

    function asAdornedEdit(){
        return new Component_View($this);
    }

    function save() {
        if($this->isNew()) {
            $phone = Emp_PersonPhone::getManager()
                ->select()
                ->where(array("name" => $this->name->get()))
                ->fetchOne();
            if($phone instanceof fvRoot)
                throw new ValidationException(
                    json_encode(
                        array("phone" => "Такой номер телефона уже существует в базе. Пользователь {$phone->person->getName()}")
                    )
                );
        }

        return parent::save();

    }

}