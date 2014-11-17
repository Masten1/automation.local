<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_ProviderFillStatus extends Script_Iterator {
    function getQuery() {
        return Provider::getManager()->select();
    }

    function executeIteration($provider) {
        $status = $provider->setFillStatus(true);
        $this->log->notice("Set status $status to $provider");
    }
}