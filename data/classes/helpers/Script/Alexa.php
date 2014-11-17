<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_Alexa extends Script_Iterator {
    const CRON_CALLABLE = true;

    function getQuery() {
        return Site::getManager()->select();
    }

    function executeIteration($site) {
        $site->renewAlexaRank();
    }
}