<?php

session_start();

/* Session manager class */
class fvSession {

        /** Session lifetime  */
        private $sessionName;

        private $userKeyName;

        /** Constructor */
        function __construct() {
            $storageName = fvSite::$fvConfig->get("session.storage", "db");
            $storageParams = fvSite::$fvConfig->get("session.params");

            $storageClass = 'Storage_' . ucfirst($storageName);

            $this->storage = Storage::create( $storageName, $storageParams);
            $this->sessionName = fvSite::$fvConfig->get("session.sess_name", "fv_session");

            session_set_save_handler(
                array(& $this, 'sess_open'),
                array(& $this, 'sess_close'),
                array(& $this, 'sess_read'),
                array(& $this, 'sess_write'),
                array(& $this, 'sess_destroy'),
                array(& $this, 'sess_gc')
            );

            $this->userKeyName = fvSite::$fvConfig->get("session.userkeyname", "login/loggedUser");
        }

        /** Start session engine */
        function start() {
            session_name($this->sessionName);
            session_start();
        }

        /** Stop session engine */
        function stop() {
            session_destroy();
        }

        /** Set session value */
        function set($key, $value) {
            global $_SESSION;
            $_SESSION[$key] = $value;
        }

        /** Get session value */
        function get($key) {
            global $_SESSION;
            return $_SESSION[$key];
        }

        /** Unset session value */
        function remove($key) {
            global $_SESSION;
            unset($_SESSION[$key]);
        }

        /** Clear all session variables  */
        function clear() {
            global $_SESSION;
            foreach ($_SESSION as $key => $value)
                    unset($_SESSION[$key]);
        }

        /** Find out whether a global variable is registered in a session  */
        function is_set($key) {
                global $_SESSION;
                return isset($_SESSION[$key]);
        }

        /**
         * @return User
         */
        function getUser() {
            return $this->get($this->userKeyName);
        }

        function setUser($user) {
            return $this->set($this->userKeyName, $user);
        }

        function getReadonlyUser() {
            return $this->get( "login/readonlyUser" );
        }

        function setReadonlyUser($user) {
            return $this->set( "login/readonlyUser", $user);
        }

        function finish() {
                session_write_close();
        }

        /* --- session handling methods --- */

        /** Session open method */
        function sess_open() {
            $this->storage->open();
            return true;
        }

        /* Session close method **/
        function sess_close() {
            $this->storage->close();
            return true;
        }

        /** Session read method */
        function sess_read($key) {
            return $this->storage->get( $key );
        }

        /** Write session data */
        function sess_write( $key, $value ) {
            return $this->storage->set( $key, $value );
        }

        /** Destroy session */
        function sess_destroy($key) {
            return $this->storage->destroy( $key );
        }

        /** Session garbage collection */
        function sess_gc( $lifetime ) {
            return $this->storage->garbageCollect( $lifetime );
        }

        // to be continued...

        function multiset( $key1, $key2, $value) {
            global $_SESSION;
            $_SESSION[$key1][$key2] = $value;
        }
}
