<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_HosterFillStatus extends Script_Iterator {
    function getQuery() {
        return Hoster::getManager()->select();
    }

    function executeIteration($hoster) {
        $status = $hoster->setFillStatus(true);
        $this->log->notice("Set status $status to $hoster");
    }
}