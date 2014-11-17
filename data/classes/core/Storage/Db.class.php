<?php

class Storage_Db extends Storage {

    private $table;
	
	private $db;

    function __construct( $params ){
        $this->table = $params["table"];
        $this->lifetime = $params["lifetime"];
        $this->db = fvSite::$pdo;
    }

    function get( $key ){
        $res = $this->db->getOne("SELECT value FROM " . $this->table . " WHERE sess_id = ?", $key);

        if( $res )
            return $res;

        $sth = $this->db->prepare("INSERT INTO ".$this->table." (sess_id, last_updated, value) VALUES(?, UNIX_TIMESTAMP(NOW()), '')");
        $sth->execute(array($key));

        return '';
    }

    function set( $key, $value ){
        $sth = $this->db->prepare("UPDATE " . $this->table . " SET value = ?, last_updated=UNIX_TIMESTAMP(NOW()) WHERE sess_id = ?");

        $this->db->execute($sth, array($value, $key));
        $this->db->freePrepared($sth);

        return true;
    }

    function destroy( $key, $value ){
        $sth = $this->db->prepare("DELETE FROM " . $this->table . " WHERE sess_id = ?");
        $sth->execute(array($key));

        return true;
    }

    function garbageCollect( $lifetime ){
        $sth = $this->db->prepare("DELETE FROM " . $this->table . " WHERE UNIX_TIMESTAMP(NOW())-last_updated > ?");
        $sth->execute(array($lifetime));

        return true;
    }

    function open(){
        //$this->garbageCollect( $this->lifetime );
        return true;
    }

    function close(){
        return true;
    }

}