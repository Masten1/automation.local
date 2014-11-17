<?php
/**
 * 
 * Author: Andrey
 * Date: 28.10.13
 * Time: 11:49 
 */

class Component_Comment extends Component_Extended {

    public $entity, $field;
    private $_action;
    private $_foreignKey = "orderId";

    function getComponentName()
    {
        return "comment";
    }

    function __construct( fvRoot $entity, $field ) {
        $this->entity = $entity;
        $this->statuses = Emp_OrderStatus::getManager()->getAll();

        $this->field = $entity->$field->get(null, "ctime desc");
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * @param mixed $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->_foreignKey;
    }

    /**
     * @param string $foreignKey
     * @return $this
     */
    public function setForeignKey($foreignKey)
    {
        $this->_foreignKey = $foreignKey;
        return $this;
    }

}