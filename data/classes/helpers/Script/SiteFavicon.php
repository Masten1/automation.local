<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:13
 */

class Script_SiteFavicon extends Script_Iterator {
    const CRON_CALLABLE = true;

    function getQuery() {
        return Site::getManager()
            ->select()
            ->join('domain', 'd')
            ->where("status != ".Site::STATE_DELETED." AND autoHoster = 1")
            ->loadRelation('domain');
    }

    function executeIteration($site) {
        $siteDomain = $site->getMainDomain();
        $domain = $siteDomain->name->get();

        //getting favicon
        $path = dirname(__FILE__).'/../../images/favicon/'.$site->getPk().'.ico';
        if( !file_exists($path) ){
            $icoPath = 'http://'.$domain.'/favicon.ico';
            $ico = @file_get_contents($icoPath);
            if( $ico ) {
                $this->log->notice("Saved icon from $icoPath to $domain");
                file_put_contents( $path, $ico );
            }
        }
    }
}