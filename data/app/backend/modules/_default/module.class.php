<?php

    class DefaultModule extends fvModule{

        protected $entity_name;

        /**
         * Current entity exemplar.
         * @var fvRoot
         */
        protected $entity;

        /**
         * Entity manager
         * @var fvRootManager
         */
        protected $manager;
        protected $filter = array();
        protected $table = array();
        protected $statistics = array();
        protected $actions = Array();

        function __construct( $module ){

            $this->moduleName = strtolower( substr( __CLASS__, 0, -6 ) );

            $this->path = $module;

            $this->previous = $this->getRequest()->getRequestParameter( "previous" ) ? $this->getRequest()->getRequestParameter('previous') : $module;

            parent::__construct( fvSite::$fvConfig->get( "modules.{$module}.smarty.template" ),
                                 fvSite::$fvConfig->get( "modules.{$module}.smarty.compile" ),
                                 fvSite::$Layoult );

            $this->entity_name = fvSite::$fvConfig->get( "modules.{$module}.entity" );
            $this->entity = new $this->entity_name;
            $this->manager = fvManagersPool::get( $this->entity_name );

            $this->filter = fvSite::$fvConfig->get( "modules.{$module}.filter" );
            $this->table = fvSite::$fvConfig->get( "modules.{$module}.table" );
            $this->statistics = fvSite::$fvConfig->get( "modules.{$module}.statistics" );
            $this->actions = fvSite::$fvConfig->get( "modules.{$module}.actions" );
        }

        function showIndex(){
            $pager = new fvPager( $this->manager );
            $this->class = $this->entity;
            $this->filterConfig = $this->filter;
            $this->tableConfig = $this->table;
            $this->ajax = $this->_request->getRequestParameter( "ajax", "int", 0 );

            $query = new fvQuery( $this->entity );

            if( in_array( $this->getRequest()->sort, $this->entity->getFieldList() ) ){
                $query->orderBy( "root." . $this->getRequest()->sort, (bool)$this->getRequest()->order );
            }

            if( $this->getRequest()->search && strtolower( $this->filter['type'] ) == 'simple' ){
                $pattern = "%{$this->getRequest()->search}%";
                foreach( $this->filter['fields'] as $field ){
                    $query
                        ->addOr()
                        ->andWhere( Array( $field => $pattern ), null, fvQuery::OPERATION_LIKE );
                }

                foreach( (array)$this->table["use"] as $referenceType => $references ){
                    foreach( (array)$references as $referenceName ){
                        switch( $referenceType ){
                            case "foreigns":
                                if( $this->entity->hasForeign( $referenceName ) ){
                                    $query->leftJoin( "{$referenceName} {$referenceName}" );
                                }
                                break;
                            case "constraints":
                            case "references":
                                if( $this->entity->hasField( $referenceName ) ){
                                    $query->leftJoin( "{$referenceName} {$referenceName}" );
                                }
                                break;
                        }
                    }
                }

                $this->collection = $pager->paginateQuery( $query );
                $this->search = $this->getRequest()->search;
            }
            else
                $this->collection = $pager->paginateQuery( $query );

            $this->create = $this->actions["create"];
            $this->edit = $this->actions["edit"];
            $this->delete = $this->actions["delete"];

            return $this->__display( 'list.tpl' );
        }

        function showEdit(){
            $id = $this->getRequest()->getRequestParameter( "id", "int", 0 );
            $subject = $this->manager->getByPk( $id, true );
            $this->subject = $subject;
            if( $subject->isLanguaged() ){
                $this->lLangs = Language::getManager()->getAll();
            }
            return $this->__display( 'edit.tpl' );
        }

        function showGetForeign(){
            $reference = $this->_request->reference;
            $out = array();
            $foreigns = $reference->getForeigns();
            foreach( $foreigns as $foreign ){
                $temp['label'] = (string)$foreign;
                $temp['value'] = $foreign->getPk();
                $temp['checked'] = $reference->isAssigned( $foreign->getPk() );
                $out[] = $temp;
            }
            return json_encode( $out );
        }

        function showAddConstraint() {
            $request = $this->getRequest();
            $entityName = $request->getRequestParameter('name');
            $data = $request->getRequestParameter('constraint');
            $entity = fvManagersPool::get( $entityName )->getByPk($data['id'], true);
            $entity->hydrate($data);
            $entity->save();
            return true;
        }

    }