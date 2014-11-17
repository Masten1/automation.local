<?php

class Field_String_Email extends Field_String
{
    /**
     * Метод валидации поля E-Mail
     *
     * @return bool
     */
    public function isValid()
    {
        if(!$this->get() and $this->nullable) {
            return true;
        }


        $pattern = "/^[a-z0-9_\-\.]+@[a-z_\-\.]+\.[a-z]{2,3}$/i";
        if (!preg_match($pattern, $this->value)) {
            $this->setValidationMessage("Введите правильный электронный адрес");
            return false;
        }

        return true;
    }
}