<?php


class Component_Pager extends fvComponent implements ArrayAccess, Iterator, Countable {

    protected $_manager;
    protected $_query;
    protected $_objects;
    protected $_pageCount;
    protected $_perPage = 10;
    protected $_getParametr = 'page';

    function getComponentName(){ return 'pager'; }

    function __construct( fvRootManager $manager, $alias = 'root' ) {
        $this->_manager = $manager;
        $this->_query = new fvQuery( $manager, $alias );

        $perPage = fvSite::$fvConfig->get("modules." . fvRoute::getInstance()->getModuleName() . ".pager.show_per_page");

        if( !($perPage = fvSite::$fvConfig->get("modules." . fvRoute::getInstance()->getModuleName() . ".pager.show_per_page")) )
            $perPage =fvSite::$fvConfig->get("pager.show_per_page");

        $this->setPerPage($perPage);
    }

    function query(){
        return $this->_query;
    }

    function execute(){
        $offset = $this->getCurrentPage() * $this->_perPage;
        $count = $this->query()->getCount();

        $this->setPageCount( ceil($count / $this->getPerPage()) );
        $this->_objects = $this->query()->limit($this->getPerPage(), $offset)->fetchAll();

        $this->prerender();

        return $this;
    }

    public function isEmpty(){
        return count( $this->_objects ) == 0;
    }

    public function setGetParametr( $getParametr ) {
        $this->_getParametr = (string)$getParametr;
        return $this;
    }

    public function getGetParametr() {
        return $this->_getParametr;
    }

    public function getCurrentPage(){
        return fvRequest::getInstance()->getRequestParameter($this->getGetParametr(), 'int', 0);
    }

    public function getObjectsCount(){
        return $this->query()->getCount();
    }

    public function setPerPage($perPage){
        $this->_perPage = $perPage;
        return $this;
    }

    public function getPerPage(){
        return $this->_perPage;
    }

    public function hasPaginate() {
        return ( $this->getPageCount() > 1);
    }

    public function getPagesLinks(){
        $pages = array();
        for( $i = 0; $i < $this->getPageCount(); $i++ ) {
            if(
                ($this->getPageCount() < 10) ||
                ($i < 3) ||
                (($this->getPageCount() - $i) < 4) ||
                (abs($i - $this->getCurrentPage()) < 3)
            )
                $pages[] = $i;
        }

        return $pages;
    }

    public function getPageHref( $page ){
        return fvRequest::getInstance()->parseQueryString( $_SERVER['REQUEST_URI'], $this->getGetParametr(), $page );
    }

    public function setPageCount($pageCount){
        $this->_pageCount = $pageCount;
        return $this;
    }

    public function getPageCount(){
        return $this->_pageCount;
    }

    public function getObjects(){
        return $this->_objects;
    }

    function offsetExists($offset) {
        return isset($this->_objects[$offset]);
    }

    function offsetGet($offset) {
        return $this->_objects[$offset];
    }

    function offsetUnset($offset) {
        unset($this->_objects[$offset]);
    }

    function offsetSet($offset, $newValue) {
        $this->_objects[$offset] = $newValue;
    }

    public function rewind() {
        reset($this->_objects);
    }

    public function current() {
        $var = current($this->_objects);
        return $var;
    }

    public function key() {
        $var = key($this->_objects);
        return $var;
    }

    public function next() {
        $var = next($this->_objects);
        return $var;
    }

    public function valid() {
        $var = $this->current() !== false;
        return $var;
    }

    public function getManager(){
        return $this->_manager;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     *       The return value is cast to an integer.
     */
    public function count() {
        return count($this->_objects);
    }
}
