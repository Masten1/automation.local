<?php

class fvFilterChain {

    private $filters;

    protected function __construct($params = null) {  
        foreach (fvSite::$fvConfig->get("filters") as $filterName) {
            $filterClass = 'fvFilter_' . $filterName;
            if (!isset($this->filters[$filterClass])) {
                $this->filters[$filterClass] = new $filterClass($params);
            }
        }
    }

    public static function getInstance() {
        static $instance;
        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    public function execute() { 
        foreach ($this->filters as $filter) {   
          if ($filter->execute() === false)  return false;
        }
    }
}
