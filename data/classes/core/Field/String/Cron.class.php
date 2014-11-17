<?php

class Field_String_Cron extends Field_String {
    const EDIT_METHOD_CRON = "cron";
    function getEditMethod() {
        return self::EDIT_METHOD_CRON;
    }

}