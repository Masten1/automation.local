<?php
class Menu extends fvRoot{

    /**
     * @static
     * @return string Entity Name
     */
    static function getEntity(){
        return __CLASS__;
    }

    public function isActive() {
        $request = explode('/', $_SERVER['REQUEST_URI']);

        return $this->link == $request[1];
    }
}
