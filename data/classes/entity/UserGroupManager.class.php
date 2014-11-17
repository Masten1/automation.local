<?php

    require_once ( fvSite::$fvConfig->get( "path.entity" ) . 'UserGroup.class.php' );

    class UserGroupManager extends fvRootManager{

        public function getControl(){
            $UserGroups = $this->getAll();

            $result = array();

            foreach( $UserGroups as $UserGroup ){
                $result[$UserGroup->getPk()] = $UserGroup->get( 'group_name' );
            }

            return $result;
        }

        public function getDefaultGroup(){
            $defaultGroup = $this->select()->where( "isDefault = 1 and isActive = 1" )->fetchOne();

            if( !$defaultGroup instanceof UserGroup )
                throw new Exception( "Default user group not set." );

            return $defaultGroup;
        }
    }
