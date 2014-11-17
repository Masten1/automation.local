<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_DeletionLettersSender extends Script_Iterator {
    const CRON_CALLABLE = true;

    function getQuery() {
        return DeletionLetter::getManager()
            ->select()
            ->loadRelation('links links')
            ->loadRelation('links.domain')
            ->where('status = :status AND letterType = :letterType ', array('status'=>Letter::STATUS_PENDING, 'letterType'=>LetterTemplate::LETTER_TYPE_DELETION));
    }

    /**
     * @param DeletionLetter $letter
     */
    function executeIteration($letter) {
        $letter->sendLetter();
    }
}