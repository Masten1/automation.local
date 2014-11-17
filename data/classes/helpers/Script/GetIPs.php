<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_GetIPs extends Script_Iterator {
    const CRON_CALLABLE = true;

    function getQuery() {
        return
            SiteDomain::getManager()->select()->where("ipAddress IS NULL OR ipAddress = ''");
    }

    /**
     * @param Site $site
     */
    function executeIteration($domain) {
        //$domain = $site->getMainDomainName();
        $helper = new whoisHelper($domain->name->get());
        $ip = $helper->getIp();
        $this->log->notice("{$domain} : $ip");
        $domain->ipAddress = $ip;
        $domain->save();
    }
}