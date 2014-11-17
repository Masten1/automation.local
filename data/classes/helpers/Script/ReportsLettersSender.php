<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_ReportsLettersSender extends Script_Iterator {
    const CRON_CALLABLE = true;

    function getQuery() {
        return RightHolderLetter::getManager()
            ->select()
            ->where('status = :status AND letterType = :letterType ', array('status'=>Letter::STATUS_PENDING, 'letterType'=>LetterTemplate::LETTER_TYPE_RIGHTHOLDER));
    }

    function executeIteration($letter) {
        $letter->sendLetter();
    }
}