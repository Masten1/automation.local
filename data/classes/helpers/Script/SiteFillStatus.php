<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_SiteFillStatus extends Script_Iterator {
    function getQuery() {
        return Site::getManager()->select();
    }

    function executeIteration($site) {
        $site->setFillStatus(true);
    }
}