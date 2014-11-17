<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 21:53
 */
abstract class Script_Iterator extends Script
{
    protected $perIteration = 30;
    protected $itemsCount;
    protected $query;

    function __construct($lockScript=true, $log=true) {
        $this->query = $this->getQuery();
        parent::__construct($lockScript, $log);
    }

    function execute() {
        $this->itemsCount = $this->getCount();
        if ($this->itemsCount) {
            $this->log->notice("Found {$this->itemsCount} elements");
            $this->startIteration();
        }
        $this->finally();
    }

    /** @return fvQuery */
    abstract function getQuery();

    abstract function executeIteration($item);

    protected function startIteration() {
        $offset = 0;
        while ($offset <= $this->itemsCount) {
            $items = $this->query->limit($this->perIteration, $offset)->execute();
            foreach($items as $item) {
                try {
                    $this->executeIteration($item);
                }
                catch (Exception $e) {
                    $this->log->error($e->getMessage());
                }
            }
            $offset += $this->perIteration;
        }
    }

    protected function getCount() {
        return $this->query->getCount();
    }

    protected function finally() {}
}
