<?php

    class UsersModule extends fvModule{
        protected $filter = array( );
        protected $table = array( );

        function __construct(){
            $this->moduleName = strtolower( substr( __CLASS__, 0, -6 ) );
            parent::__construct( fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.template" ),
                                 fvSite::$fvConfig->get( "modules.{$this->moduleName}.smarty.compile" ),
                                 fvSite::$Layoult );

            $this->filter = fvSite::$fvConfig->get( "modules.users.filter" );
            $this->table = fvSite::$fvConfig->get( "modules.users.table" );
        }

        function showIndex(){
            $pager = new fvPager( User::getManager() );

            $user = new User;
            $this->filterConfig = $this->filter;
            $this->tableConfig = $this->table;
            $this->ajax = $this->_request->getRequestParameter( "ajax" , "int" , 0);

            if( in_array( $this->_request->sort, $user->getFieldList() ) ) {
                $sort = $this->_request->sort;
                $order = $sort . ' ' . ( $this->_request->order ? 'ASC' : 'DESC' );
            } else
                $order = null;

            if( $this->getRequest()->search && strtolower($this->filter['type']) == 'simple' ) {
                $where = array();
                $search = $this->getRequest()->search;
                foreach( $this->filter['fields'] as $field )
                    $where[] = "$field LIKE '%{$search}%'";
                $this->Users = $pager->paginate( implode(" OR ", $where), $order );
                $this->search = $search;
            } else
                $this->Users = $pager->paginate( null, $order );

            return $this->__display( 'user_list.tpl' );
        }

        function showEdit(){
            $request = fvRequest::getInstance();
            $id = $request->getRequestParameter( 'id', "int", 0 );
            $User = User::getManager()->getByPk( $id, true );

            $this->__assign( array( 'User'         => $User,
                                    "GroupManager" => UserGroup::getManager(), ) );
            return $this->__display( 'user_edit.tpl' );

        }

        function showGetparameterslist(){
            $r = fvRequest::getInstance();
            $UserPrivileges = UserPrivileges::getManager()
                ->getByPk( array( 'user_id' => $r->getRequestParameter( 'user_id' ),
                                  'site_id' => $r->getRequestParameter( 'site_id' ) ) );

            if( $UserPrivileges === false ){
                $UserPrivileges = new UserPrivileges();
            }

            $this->__assign( "Privileges", $UserPrivileges );
            return $this->__display( 'param_list.tpl' );
        }
    }

?>
