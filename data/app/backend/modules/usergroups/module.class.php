<?php

class UserGroupsModule extends fvModule {

    function __construct() {
        $this->moduleName = strtolower( substr( __CLASS__, 0, -6 ) );
        parent::__construct( fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.template" ), fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.compile" ), fvSite::$Layoult );
    }

    function showIndex() {
        $pager = new fvPager( UserGroup::getManager() );
        $this->__assign( 'UserGroups', $pager->paginate( null, "isDefault DESC" ) );
        return $this->__display( 'group_list.tpl' );
    }

    function showEdit() {
        $request = fvRequest::getInstance();
        $id = $request->getRequestParameter( 'id' );
        $UserGroup = UserGroup::getManager()->getByPk( $id, TRUE );

        $this->__assign( 'UserGroup', $UserGroup );
        return $this->__display( 'group_edit.tpl' );
    }

}