<?php
    /**
     * @author Iceman
     * @since 11.12.12 14:45
     */
    class Script_Notify extends Script{
        function Execute(){

            $users = User::getManager()
                ->select()
                ->where( " !isnull( rationId )" )
                ->loadRelation( "socialUsers" )
                ->fetchAll();

            $tokenUrl = "https://graph.facebook.com/oauth/access_token?" .
                "client_id=" . fvSite::$fvConfig->get( "auth.facebook.appId" ) .
                "&client_secret=" . fvSite::$fvConfig->get( "auth.facebook.secret" ) .
                "&grant_type=client_credentials";

            $request = new fvHttpRequest( $tokenUrl );
            $request->request();
            $response =  $request->getResultContent();

            $isError = json_decode( $response );
            if( $isError->error ){
                $this->log->fatalError( $isError->error->message );
                return false;
            }

            $this->log->token( $response );

            foreach( $users as $user ){
                if( count( $user->socialUsers->get() ) ){
                    foreach( $user->socialUsers->get() as $socialUser ){
                        if( $socialUser->netId == Component_Social::FB ){
                            $appRequest ="https://graph.facebook.com/" . $socialUser->netUserId->get() .
                                "/apprequests?message=" . fvDictionary::getInstance()->weeklyNotify .
                                "&" . $response . "&method=post";

                            echo $appRequest;

                            $request = new fvHttpRequest( $appRequest );
                            $request->request();
                            $result = $request->getResultContent();

                            $this->log->message( $result );
                        }
                    }
                }

            }
        }

        function getScriptName(){
            return "WeeklyNotifier";
        }
    }
