<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_CheckMail extends Script_Iterator {
    const CRON_CALLABLE = true;

    private $imap;

    function __construct() {
        try {
            $this->imap = new IMAP(
                fvSite::$fvConfig->get('mail_account.mailbox'),
                fvSite::$fvConfig->get('mail_account.login'),
                fvSite::$fvConfig->get('mail_account.password'),
                'INBOX'
            );
        } catch (Exception $e) {
            $this->log->error($e->getMessage());
        }
        parent::__construct();

    }

    function getCount() {
        return $this->imap->getMessagesCount();
    }
    
    function startIteration() {
        for($i = 1; $i <= $this->itemsCount; $i++) {
            $this->executeIteration($i);
        }
    }

    function executeIteration($i) {
        $headers = $this->imap->getHeader($i);
        //$headers = $this->imap->getHeader($i);
        //$body = $this->imap->getBody($i);
        $this->log->notice("Processing ".$headers->Subject);
        if ($this->imap->undeliveredReport($i)) {

            $this->log->notice("This is undelivery report");

            if ($messageId = $this->imap->getOriginalMessageId($i)) {

                $unleliveredLetter = Letter::getManager()->getOneBysentMessageId($messageId);

                if ($unleliveredLetter) {
                    $unleliveredLetter->status = Letter::STATUS_DELIVERY_ERROR;
                    $unleliveredLetter->save();
                    $this->log->notice("moving to Processed");
                    $this->imap->move($i, 'Processed');
                } else {
                    $this->log->notice("moving to UnknownMessageId");
                    $this->imap->move($i, 'UnknownMessageId');
                }
            } else {
                $this->log->notice("Cannot find message id");
            }

        } else {
            $this->log->notice("moving to Unknown");
            $this->imap->move($i, 'Unknown');
        }
    }
    
    function getQuery() {}
}