<?php
/**
 * Сущность субдоменов
 * @author Корнев Захар
 * @since 2011/10/24
 */
class Domain extends fvRoot{
    static function getEntity(){ return __CLASS__; }

    public function __toString(){
        return (string)$this->url . "." . fvSite::$fvConfig->get( "site_base" );
    }
}

?>
