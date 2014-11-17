<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 08.05.12
 * Time: 15:20
 */
class IMAP
{
    private $stream;
    private $mailbox;


    function __construct($connectionString, $user, $pass, $mailbox="INBOX") {
        //var_dump($mailbox, $user, $pass);die;
           $this->mailbox =  '{'.$connectionString.'}'.$mailbox;
           $this->stream = imap_open($this->mailbox, $user, $pass);
        if (!$this->stream) {
            throw new Exception(implode("\n", imap_errors()));
        }

    }

    public function getInfo() {

    }

    public function getMailboxes($pattern='*') {
        return imap_list($this->stream, $this->mailbox, $pattern);
    }

    public function getMessagesCount($renew=false) {
        return imap_num_msg($this->stream);
    }

    public function getBody($msgNum) {
        return imap_body($this->stream, $msgNum);
    }

    public function getHeader($msgNum) {
        return imap_header($this->stream, $msgNum);
    }

    public function move($msgNum, $mailbox) {
        return imap_mail_move( $this->stream , $msgNum , $mailbox );
    }

    function __destruct() {
        if ($this->stream) {
            imap_expunge($this->stream);
            imap_close($this->stream);
        }
    }

    /**
     * @param integer $msgNum
     * @return bool
     */
    //TODO:: Нормальная настройка проверки
    function undeliveredReport($msgNum) {
        $headers = $this->getHeader($msgNum);
        $subject = $headers->Subject;
        //$body = $this->getBody($msgNum);
        return ($subject == 'Delivery Status Notification (Failure)' || $subject == 'Mail delivery failed: returning message to sender');
    }

    public function getOriginalMessageId($msgNum) {
        $headers = $this->getHeader($msgNum);
        if ($headers->in_reply_to) {
            return $headers->in_reply_to;
        }

        $body = $this->getBody($msgNum);
        if (preg_match('/Message-ID: (<.*>)/i', $body, $macthes)) {
            return $macthes['1'];
        }

        return false;
    }
}
