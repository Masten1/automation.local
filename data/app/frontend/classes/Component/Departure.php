<?php
/**
 *
 * Author: Andrey
 * Date: 15.10.13
 * Time: 17:15
 */

class Component_Departure extends Component_Extended
{

    public $manager, $list, $entity;

    protected $title, $module;

    protected $allowNew = true;
    protected $editable = true;
    protected $deletable = true;

    /**
     * @var fvQuery
     */
    protected $query;

    function __construct(fvRootManager $manager)
    {
        $this->manager = $manager;
        $this->entity = $manager->getEntity();
        $this->query = new fvQuery($manager);
    }

    function __toString()
    {
        return $this->display();
    }

    function getComponentName()
    {
        return "table";
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
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param mixed $module
     * @return $this
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowNew()
    {
        return $this->allowNew;
    }

    /**
     * @param boolean $allowNew
     * @return $this
     */
    public function setAllowNew($allowNew)
    {
        $this->allowNew = $allowNew;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEditable()
    {
        return $this->editable;
    }

    /**
     * @param boolean $editable
     * @return $this
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeletable()
    {
        return $this->deletable;
    }

    /**
     * @param boolean $deletable
     *
     * @return $this
     */
    public function setDeletable($deletable)
    {
        $this->deletable = $deletable;
        return $this;
    }

    public function display()
    {
        return parent::__toString();
    }

    public function addFilter($filter)
    {
        $this->query->andWhere($filter);
        return $this;
    }

    public function addRelation($relation)
    {
        $this->query->relationLoader()->addRelation($relation);
        return $this;
    }

    public function addOrder($order)
    {
        $this->query->orderBy($order);
        return $this;
    }

    public function addJoin($join)
    {
        $this->query->join($join);
        return $this;
    }

    public function getList()
    {
        $this->preload();
        return $this->list;
    }

    public function preload()
    {
        if (!$this->list) {
            $this->list = $this->query->fetchAll();
        }
        return $this;
    }
}