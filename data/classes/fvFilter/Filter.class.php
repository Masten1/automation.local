<?php

class fvFilter_Filter implements iFilter {
    
    public function __construct() {
        
    }
    
    public function execute() {
        $currentUrl = fvRequest::getInstance()->getRequestParameter('requestURL');
        
        $filterArray = fvRequest::getInstance()->getRequestParameter('filter');
         
        if (is_array($filterArray)) {
            fvResponse::getInstance()->setHeader('filtered', true);
            if ($filterArray['_clear'] == "1") {
                fvSite::$fvSession->remove("$currentUrl/filter", $filterArray);
            } else {
                fvSite::$fvSession->set("$currentUrl/filter", $filterArray);
            }
        }
        return true;
     }
}
