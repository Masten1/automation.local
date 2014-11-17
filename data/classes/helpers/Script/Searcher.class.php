<?php
/**
 * User: apple
 * Date: 15.08.12
 * Time: 18:47
 */
class Script_Searcher extends Script_Endless {

    const CRON_CALLABLE = true;

    function getQuery(){
        static $query;

        if( ! $query instanceof fvQuery ){
            $query = SearchKeyword::getManager()
                ->query("k")
                ->join("project", "p")
                ->where('(k.lastSearched < NOW() - INTERVAL 1 DAY) OR (k.mtime > k.lastSearched + INTERVAL 2 SECOND) OR k.lastSearched IS NULL')
                ->andWhere('k.status = :ks', array("ks" => SearchKeyword::STATE_OK))
                ->andWhere('p.searchStatus = :ps', array("ps" => Project::STATE_OK))
                ->loadRelation("project");
        }

        return $query;
    }

    function executeIteration( $keyword ){
        /** @var $keyword SearchKeyword */
        $keyword->search();
    }

    function prepare(){
        Search_Abstract::setLog( $this->log );
    }

    protected function finally() {
        // Чистим кеш
        // Так как скрипт висит постоянно в памяти, нам нужно точно знать, что все сущности будут перезагружены.
        Field_Foreign::clearCache();
        SiteDomain::getManager()->clearCache();
        ProjectSite::getManager()->clearCache();
    }
}
