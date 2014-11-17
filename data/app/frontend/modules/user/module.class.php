<?php

class UserModule extends fvModule{

    function __construct(){
        $this->moduleName = strtolower( substr( __CLASS__, 0, -6 ) );
        parent::__construct( fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.template" ),
                             fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.compile" ),
                             fvSite::$Layoult );
    }

    function showIndex(){
        return $this->__display( "index.tpl" );
    }

    function showLogin()
    {
        try {
            $className = fvSite::$fvConfig->get('access.user_class');

            $UserManager = fvManagersPool::get( $className );
            $request = fvRequest::getInstance();

            $login = $request->getRequestParameter("login");
            $password = $request->getRequestParameter('password');

            $LoggedUser = $UserManager->Login($login, $password);

            if ($LoggedUser === false) {
                throw new Exception("Пользователь с таким логином/паролем не найден. Проверьте правильность введённых данных");
            }

            fvSite::$fvSession->setUser($LoggedUser);
            return json_encode( array("type" => "success") );
        } catch (Exception $e) {
            return json_encode( array ("type" => "alert-error", "message" => $e->getMessage()));
        }

    }
}
