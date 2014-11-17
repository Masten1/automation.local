<?php
    /**
     * @author Iceman
     * @since 06.12.12 13:43
     */
    class fvFilter_LoggedUser implements iFilter{

        public function execute(){
            $user = fvSite::$fvSession->getUser();
            if( $user instanceof User ){
                $newUser = User::getManager()->getByPk( $user->getPk() );
                if( $newUser instanceof User ){
                    fvSite::$fvSession->setUser( $newUser );
                }
            }
            return true;
        }
    }
