<?php

class fvForm {

    protected $_fields = array();
    protected $_messages = array();

    /** @var $_entity fvRoot */
    protected $_entity;
    protected $_name = 'f';
    protected $_action = '';
    protected $_template = 'base';
    protected $_validationResult;
    protected $_processed = false;

    function __construct( fvRoot $entity = null, $fields = null ) {
        if( $entity )
            $this->setEntity( $entity );
        if( is_array( $fields ) )
            $this->setFieldsPositions( $fields, false );
        elseif( !is_null($fields) )
            throw new Exception("fvForm::__construct second parametr must be array or null");

        $this->updateFields();
    }

    function updateFields(){
        $values = fvRequest::getInstance()->getRequestParameter( $this->getName() );

        foreach( $this->getFields() as $keyName => $field ){
            if( isset($values[$keyName]) ){
                $this->setFieldValue( $keyName, $values[$keyName] );
            }
        }
    }

    function setEntity( fvRoot $entity ){
        foreach( $entity->getFields() as $key => $field ){
            switch( $field->getEditMethod() ){
                case fvField::EDIT_METHOD_ENTITIES_LIST:
                    $value = array();
                    foreach( $field->get() as $e)
                        $value[] = (int)$e->getPk();

                    $this->addField( $key, $field->getEditMethod(), $value, $field->getForeigns() );

                    break;
                case fvField::EDIT_METHOD_MULTILIST:
                    $this->addField( $key, $field->getEditMethod(), $field->asArray(), $field->getList( $entity ) );
                    break;
                case fvField::EDIT_METHOD_LIST:
                    $this->addField( $key, $field->getEditMethod(), $field->get(), $field->getList( $entity ) );
                    break;
                case fvField::EDIT_METHOD_DATE:
                    $this->addField( $key, $field->getEditMethod(), is_null($field->get()) ? '' : date('d.m.Y', $field->asTimestamp()) );
                    break;
                case fvField::EDIT_METHOD_DATETIME:
                    $this->addField( $key, $field->getEditMethod(), is_null($field->get()) ? '' : date('d.m.Y H:i', $field->asTimestamp()) );
                    break;
                default:
                    $this->addField( $key, $field->getEditMethod(), $field->get() );
            }
        }
        $this->_entity = $entity;

        $this->setName( $entity->getEntity() );
    }

    function getEntity(){
        return $this->_entity;
    }

    function proceed(){
        if( $this->isValid() ) {
            $this->_processed = true;
            return $this->finally();
        } else
            return $this->error();
    }

    function isValid(){
        if( $this->_entity instanceof fvRoot )
            return $this->_entity->isValid();

        // если нужно доопределить схему форму и валидацию к ней
        return true;
    }

    function finally(){
        if( $this->_entity instanceof fvRoot )
            $this->_entity->save();

        return $this->_entity;
    }

    function error(){
        return false;
    }

    function setName( $value ){
        $this->_name = $value;
    }

    function getName(){
        return $this->_name;
    }

    function setAction( $value ){
        $this->_action = $value;
    }

    function getAction(){
        return $this->_action;
    }

    function addField( $keyName, $type, $value = null, $values = null ){
        if( isset($this->_fields[$keyName]) )
            throw new FormFieldException("Form Field {$keyName} already defined!");

        $this->_fields[ $keyName ] = array(
            'type' => $type,
            'value' => $value,
            'values' => $values,
        );
    }

    function removeField( $keyName ){
        unset( $this->_fields[ $keyName ] );
    }

    function getField( $keyName ){
        if( !isset($this->_fields[$keyName]) )
            throw new FormFieldException("Form Field {$keyName} not found!");

        return $this->_fields[$keyName];
    }

    function getFieldValue( $keyName ){
        if( !isset($this->_fields[$keyName]) )
            throw new FormFieldException("Form Field {$keyName} not found!");

        return $this->_fields[$keyName]['value'];
    }

    function setFieldValue( $keyName, $value ){
        if( !isset($this->_fields[$keyName]) )
            throw new FormFieldException("Form Field {$keyName} not found!");

        $this->_fields[$keyName]['value'] = $value;

        if( isset($this->_entity) )
            $this->_entity->$keyName = $value;
    }

    function getFieldError( $keyName ){
        if( !isset($this->_fields[$keyName]) )
            throw new FormFieldException("Form Field {$keyName} not found!");

        return $this->_fields[$keyName]['error'];
    }

    function setFieldError( $keyName, $value ){
        if( !isset($this->_fields[$keyName]) )
            throw new FormFieldException("Form Field {$keyName} not found!");

        $this->_fields[$keyName]['error'] = $value;
    }

    function setFieldsPositions( array $keysArray, $preserveFields = false ){
        $newFields = array();
        foreach( $keysArray as $keyName ){
            $newFields[$keyName] = $this->getField($keyName);
        }

        if( $preserveFields )
            foreach( $this->getFields() as $keyName => $field ){
                if( !isset($newFields[$keyName]) )
                    $newFields[$keyName] = $field;
            }

        $this->_fields = $newFields;
    }

    function getFields(){
        return $this->_fields;
    }

    function __get( $keyName ) {
        return $this->getFieldValue($keyName);
    }

    function setTemplateName( $templateName ){
        $this->_template = $templateName;
    }

    function getTemplateName(){
        return $this->_template;
    }

    function __set( $keyName, $value ) {
        $this->setFieldValue($keyName, $value);
    }

    function __toString() {
        try{
            fvSite::$Template->assign( "this", $this );

            $old_template_dir = fvSite::$Template->template_dir;
            $old_compile_dir = fvSite::$Template->compile_dir;

            fvSite::$Template->template_dir = fvSite::$fvConfig->get( "path.smarty.template" ) . "forms";
            fvSite::$Template->compile_dir = fvSite::$fvConfig->get( "path.smarty.compile" );

            $result = fvSite::$Template->fetch( $this->getTemplateName() . ".tpl" );

            fvSite::$Template->template_dir = $old_template_dir;
            fvSite::$Template->compile_dir = $old_compile_dir;

            return $result;
        } catch ( Exception $e ){
            return $e->getMessage();
        }
    }

    function addMessage( $type, $description ){
        $this->_messages[$type][] = $description;
    }

    function getMessages( $type = null ){
        if( is_null( $type ) )
            return $this->_messages;

        if( isset( $this->_messages[$type] ) )
            return $this->_messages[$type];

        return array();
    }

    function isNew(){
        if( $this->_entity instanceof fvRoot )
            return $this->_entity->isNew();

        return false;
    }

    function isProcessed(){
        return $this->_processed;
    }

}
