<?php

class PageManager extends fvRootManager {

    function htmlSelect ($field, $empty = "", $where = null, $order = null, $limit = null, $args = array()) {
        $result = array('0' => 'корневая страница');

        if (!is_array($args)) $args = array(($args)?$args:'');

        $result = $result + parent::htmlSelect($field, $empty, $where, $order, $limit, $args);
        return $result;
    }

    public function getControl($current_page_id = null) {

        $where = "";
        if (!empty($current_page_id)) $where = " AND id <> " . intval($current_page_id);

        $Pages = $this->getAll("parentId = 0 AND name <> 'default'{$where}");

        $result = array('0' => 'корневая страница');

        foreach ($Pages as $Page) {
            $result[$Page->getPk()] = $Page->get('name');
        }

        return $result;
    }

    public function getPagesByURL($url) {
        $params = array($url);

        $query = "SELECT f.* FROM ".$this->rootObj->getTableName()." f
        LEFT JOIN ".$this->rootObj->getTableName()." f_p on (f_p.id = f.parentId)
        WHERE ? RLIKE CONCAT_WS(\"/\", IFNULL(f_p.url, ''), f.url)
        order by IF (f.parentId > 0, 1, IF(f.name <> 'default', 2, 3))";

        $res = fvSite::$pdo->query($query, $params);

        $result = array();

        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $o = clone $this->rootObj;
            $o->hydrate($row);

            $result[] = $o;
        }

        return $result;        
    }
    
    /**
     * pages tree
     * @return array
     */
    public function getPageTree(){
        $cPage = $this->getAll( null, null, null, null, 'id' );
        $cDomains = Domain::getManager()->getAll( null, null, null, null, 'id' );
        
        $tree = array();
        foreach( $cPage as $pageKey => $iPage ){
            $domainID = $iPage->domainId->get();
            
            if( $iPage->parentId->get() == 0 ){
                $tree[$domainID]["pages"][$pageKey]["parent"] = $iPage;
            }
            else{
                $parentID = (int)$iPage->parentId;
                $tree[$domainID]["pages"][$parentID]["nodes"][$pageKey] = $iPage;
            }
        }
        
        foreach( $cDomains as $domainKey => $iDomain ){
            $tree[$domainKey]["domain"] = $iDomain;
        }
        return $tree;
    }
}