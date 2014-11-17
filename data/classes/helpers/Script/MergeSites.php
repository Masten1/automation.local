<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_MergeSites extends Script_Iterator {
    const CRON_CALLABLE = true;


    private $problemSites;

    function __construct() {
        $query = "select
            s.id as wwwId,
            s.domain as wwwDomain,
            s2.id as id,
            s2.domain as domain
        FROM
            empSites s
        JOIN
            empSites s2
        ON (
            s.domain = CONCAT('www.' , s2.domain )
            AND s2.id != s.id)
        WHERE
            s.status != ".Site::STATE_DELETED."
            AND s2.status != ".Site::STATE_DELETED;
        $this->problemSites = fvSite::$pdo->getAssoc($query);
        parent::__construct();
    }

    function getCount() {
        return count($this->problemSites);
    }

    function getQuery(){}

    function startIteration() {
        foreach ($this->problemSites as $problemSite) {
            try {
                $this->executeIteration($problemSite);
            }
            catch (Exception $e) {
                $this->log->error($e->getMessage());
            }
        }
    }

    function executeIteration($problemSite) {
        $siteWithWWW = Site::getManager()->getByPk($problemSite['wwwId']);
        $siteWithoutWWW = Site::getManager()->getByPk($problemSite['id']);

        if ($siteWithWWW->isRedirectedToDomain($problemSite['domain'])) {
            $this->log->notice("Merging {$problemSite['wwwDomain']} with {$problemSite['domain']}");
            $siteWithoutWWW->mergeWith($siteWithWWW);
        } elseif ($siteWithoutWWW->isRedirectedToDomain($problemSite['wwwDomain'])) {
            $this->log->notice("Merging {$problemSite['domain']} with {$problemSite['wwwDomain']}");
            $siteWithWWW->mergeWith($siteWithoutWWW);
        } else {
            $this->log->notice("No redirection in {$problemSite['domain']} and {$problemSite['wwwDomain']}");
        }
    }
}