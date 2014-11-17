<?php
/**
 * 
 * Author: Andrey
 * Date: 15.10.13
 * Time: 18:41 
 */

class Component_Edit extends Component_Extended {

    public $entity;

    protected $title, $action;

    function __construct( fvRoot $entity ) {
        $this->entity = $entity;
    }

    function getComponentName()
    {
        return "edit";
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

}