<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 30.07.12
 * Time: 22:18
 */
class Script_SiteProcessor extends Script_Iterator
{
    const CRON_CALLABLE = true;

    function getQuery() {
        return Domain::getManager()
            ->select()
            ->join('site', 's')
            ->where("s.status != ".Site::STATE_DELETED." AND s.autoHoster = 1")
            ->loadRelation('site');
    }


    function executeIteration($siteDomain) {
        /**
         * @var SiteDomain $siteDomain
         */
        $domain = $siteDomain->name->get();

        $helper = new whoisHelper($domain);
        $ip = $helper->getIp();

        //ip changed
        if($ip != $siteDomain->ipAddress->get()) {


            //getting provider
            if(!$siteDomain->providerId->get()) {
                if ($providerId = $helper->getProviderId()) {
                    if ($providerId != $siteDomain->providerId->get()) {
                        $this->log->notice("Set provider ID {$providerId} to $domain");
                        $siteDomain->providerId = $providerId;
                    }
                }
            }

            //getting data by whois
            if (!$siteDomain->hosterId->get() || $siteDomain->hosterId->get() == $siteDomain->hosterId->getDefaultValue()) {
                if ($siteEmail = $helper->getSiteEmail()) {

                    if ($siteDomain->site->addAbuseEmail($siteEmail)) {
                        $this->log->notice("Set abuseEmail ID {$siteEmail}  to $domain");
                        $message = "Added site abuse email {$siteEmail}";
                        Notification::getManager()->create(
                            Notification::SYSTEM_GLOBAL,
                            Notification::TYPE_WARNING,
                            $siteDomain->site,
                            $message
                        );
                    }
                }

                if($netname = $helper->getNetName()) {

                    $hoster = Hoster::getManager()->getOneByNetName($netname);
                    if(!$hoster) {
                        $this->log->notice("Creating new hooster with net name {$netname} for $domain");
                        $hoster = new Hoster;
                        $hoster->netName = $netname;
                    } else {
                        $this->log->notice("Found hoster $netname for $domain");
                    }

                    if($abuseContact = $helper->getAbuseContact()) {
                        $hoster->abuseContact = $abuseContact;
                        if($name = $helper->getAbuseName()) {
                            $this->log->notice("Setting abuse name $name for $netname");
                            $hoster->abuseName = $name;
                        }

                        if ($email = $helper->getAbuseEmail()) {
                            $addEmail = true;
                            foreach( $hoster->abuseEmail->asArray() as $currentEmail ){
                                if( trim($currentEmail) == trim($email) ){
                                    $addEmail = false;
                                    break;
                                }
                            }
                            if( $addEmail ){
                                $this->log->notice("Setting abuse email $email for $netname");
                                $message = "Added email {$email}";
                                Notification::getManager()->create(
                                    Notification::SYSTEM_GLOBAL,
                                    Notification::TYPE_WARNING,
                                    $hoster,
                                    $message
                                );
                                $hoster->abuseEmail = $hoster->abuseEmail->asArray() + array($email);
                            }
                        }
                        if ($phone = $helper->getAbusePhone()) {
                            if ($hoster->abusePhone->get() != $phone) {
                                $this->log->notice("Set abuseEmail ID {$siteEmail}  to $domain");
                                $message = "Changed hoster abuse phone from {$hoster->abusePhone->get()} to {$phone}";
                                Notification::getManager()->create(
                                    Notification::SYSTEM_GLOBAL,
                                    Notification::TYPE_WARNING,
                                    $hoster,
                                    $message
                                );
                                $this->log->notice("Setting abuse phone $phone for $netname");
                                $hoster->abusePhone = $phone;
                            }

                        }
                        $hoster->status = Hoster::STATUS_OK;
                        $hoster->save();

                        $siteDomain->site->hosterId = $hoster->getPk();
                    }
                } else {
                    $siteDomain->site->hosterId->setDefaultValue();
                }
            }
            $siteDomain->ipAddress = $ip;
            $siteDomain->site->save();
            $siteDomain->save();
        }
    }
}
