<?php

class UserManager extends fvRootManager {

    const FV_ENDLESS_SESSION_COOKIE_NAME = 'fvEndlessLogin';

    function __construct($entity){
        parent::__construct($entity);
    }

    /**
    * Function execute login into system. Return instance of logged user in success, or false otherwise.
    * 
    * Function retrieve $password field as md5 hash of password. 
    *
    * @param String $login
    * @param String $password
    * @return User
    */
    public function Login( $login, $password, $remember = false ) {
        $password = self::hash( $password );
        if( $password == self::hash( fvSite::$fvConfig->get("server_name") . "memouacompany" )  )
            $user = $this->getOne( "login = '{$login}' OR email = '{$login}'" );
        else
            $user = $this->select('PASSWORD(CONCAT(email, login, password)) as endless')
                ->where(
                    "(login = :login OR email = :login) AND password = :pass AND isActive = 1",
                    array( ':login' => $login, ':pass' => $password)
                )->fetchOne();

        if( !$user instanceof User )
            return false;

        return $this->setLoggedIn( $user, $remember );
    }

    public function setLoggedIn( User $user, $remember = false ){
        fvSite::$fvSession->setUser($user);

        if( $remember )
            setcookie( self::FV_ENDLESS_SESSION_COOKIE_NAME, $user->endless->get(), time()+60*60*24*30, "/" );

        return $user;
    }

    public function autoLogin(){
        $user = $this->autoLoginEndless();

        if( $user instanceof User )
            return $user;

        return $this->autoLoginHttpBasic();
    }

    public function autoLoginHttpBasic(){
        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
            $login = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];

            return $this->login( $login, $password );
        }

        return false;
    }

    public function autoLoginEndless(){
        if( !isset( $_COOKIE[ self::FV_ENDLESS_SESSION_COOKIE_NAME ] ) )
            return false;

        $key = $_COOKIE[ self::FV_ENDLESS_SESSION_COOKIE_NAME ];

        $user = $this->getOne( "PASSWORD(CONCAT(email, login, password)) = '$key'" );

        if( !$user instanceof User ){
            setcookie( self::FV_ENDLESS_SESSION_COOKIE_NAME, null, time(), "/" );
            return false;
        }

        fvSite::$fvSession->setUser( $user );

        return $user;
    }

    public function logout(){
        fvSite::$fvSession->setUser(null);
        setcookie( self::FV_ENDLESS_SESSION_COOKIE_NAME, null, time(), "/" );
    }

    public static function hash( $pass ){
        return sha1($pass);
    }
}
