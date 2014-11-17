<?php
    /**
     * @author Iceman
     * @since 03.01.13 19:15
     */
    class Component_View extends Component_Extended{
        public $entity;

        public function __construct( $entity, $templateName = "asAdorned" ){
            $this->entity = $entity;
            $this->setTemplateName( $templateName );
        }

        function getComponentName(){
            return "view/" . strtolower( $this->entity->getEntity() );
        }
    }
