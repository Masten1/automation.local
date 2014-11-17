<?php
/**
 * User: cah4a
 * Date: 22.03.12
 * Time: 10:41
 */
class fvCommet
{
    public
        $_room,
        $_storage,
        $_prefix,
        $_maxPullTime,
        $_checkTime,
        $_messageSession = null,
        $_lifetime;

    function __construct( $room ){
        $this->_room = (string)$room;
        $this->_prefix = fvSite::$fvConfig->get("commet.prefix", "commet");
        $this->_interval = (int)fvSite::$fvConfig->get("commet.interval", 500000);
        $this->_lifetime = (int)fvSite::$fvConfig->get("commet.lifetime", 1000 );
        $this->_maxPullTime = (int)fvSite::$fvConfig->get("commet.maxPullTime", 23 );

        $this->_storage = Storage::create("memcache", array("lifetime" => $this->_lifetime, "prefix" => $this->_prefix));
    }

    /**
     * Добавить сообщение в очередь
     * @param mixed $message сообщение
     */
    function push( $message, $session = null ){
        $messages = $this->getMessages();

        if( $session )
            $messages[] = array( "time" => self::time(), "message" => $message, "session" => $session );
        else
            $messages[] = array( "time" => self::time(), "message" => $message );

        return $this->setMessages( $messages );
    }

    /**
     * Вытягует крайнее сообщение в очереди по данному каналу
     * @param null $time время с которого возвращять сообщения
     * @return mixed message сообщение
     * @throws CommetException если временя утекло, а активности нуль
     */
    function pull( $time = null ){
        ini_set('output_buffering', 0);

        if( !$time )
            $time = self::time();

        session_write_close();
        fvResponse::getInstance()->sendHeaders();
        ob_start();
        ignore_user_abort(true);

        while( (($message = $this->getMessage($time)) === false) ){
            $processedTime = self::time() - $time;
            if( $processedTime > $this->_maxPullTime ){
                throw new CommetException($this->getCheckTime());
            }

            if( connection_status() != CONNECTION_NORMAL ) {
                echo "<br>" ; // To make this work on chrome
                flush();
                ob_flush(); // FLUSH (in flush u will need to add a <br> with the flushed data in order to get the response sent to user if the user is using chrome )
                die;
            }
            usleep($this->_interval);
        }

        return $message;
    }

    /**
     * @return int Последнее проверенное время
     */
    function getCheckTime(){
        return $this->_checkTime;
    }

    function getMessageSession(){
        return $this->_messageSession;
    }

    public function getMessages(){
        $this->_checkTime = self::time();
        $messages = $this->_storage->get($this->_room);
        if( !is_array($messages) ){
            $messages = array();
        }

        foreach( $messages as $key => $message ){
            if( (self::time() - $message['time']) > $this->_lifetime ){
                unset( $messages[$key] );
            }
        }

        return $messages;
    }

    protected function setMessages( $messages ){
        return $this->_storage->set($this->_room, $messages);
    }

    protected function getMessage( $minTime ){
        $messages = $this->getMessages();
        foreach( $messages as $message ){
            if( $minTime < $message['time'] ){
                if( isset($message['session']) )
                    $this->_messageSession = $message['session'];
                return $message['message'];
            }
        }

        return false;
    }

    protected static function time(){
        return microtime(true);
    }

}


class CommetException extends Exception{}