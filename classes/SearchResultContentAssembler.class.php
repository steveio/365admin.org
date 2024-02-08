<?php

/**
 * Search Result Content Assembler 
 * 
 * Handles content provision and template render for pages displaying search results:
 * 
 * 		eg. /country/<country-name>
 * 			/continent/<continent-name>
 * 			/<activity-name>
 * 			/<category-name>
 * 			/search/<search-phrase>
 *
 *
 */

class SearchResultContentAssembler extends AbstractContentAssembler {

    private $oSolrQuery;
    private $oSolrSearch;

    public function __Construct() 
    {
        parent::__construct();

        $this->aProfile = array();
        $this->aArticle = array();
        
    }

    public function GetById($id) {}
    
    
    public function GetByPath($path, $website_id = 0)
    {

        global $oHeader, $oFooter;

        try {

            $oJsInclude = new JsInclude();
            $oJsInclude->SetSrc("/includes/js/autocomplete/jquery-ui.min.js");
            $oHeader->SetJsInclude($oJsInclude);

            $oJsInclude = new JsInclude();
            $oJsInclude->SetSrc("/includes/js/search_panel.js");
            $oHeader->SetJsInclude($oJsInclude);
            
            $oHeader->Reload();

            $oSearchPanel = new Template();


            print_r("<pre>");
            print_r($_REQUEST);
            print_r("</pre>");


            if(isset($_REQUEST['search-process']))
            {
                $oSearchPanel->Set('SEARCH_KEYWORDS', $_REQUEST['search-panel-keywords']);
                $this->ProcessSearch();
                
            }

            /*
             * retrieve any previously submitted search keywords
             * 
             * (Ajax search dispatch)
             * 
            $oSearchParameters = SolrSearchPanelSearch::getFromSession();
            if (is_object($oSearchParameters))
            {
                $oSearchPanel->Set('SEARCH_KEYWORDS', $oSearchParameters->getKeywords());
            }
            */

            $oSearchPanel->Set('ACTIVITY_LIST',Activity::getActivitySelectList());

            $oSearchPanel->LoadTemplate("search_panel.php");

            print $oHeader->Render();
            print $oSearchPanel->Render();
            print $oFooter->Render();

            
            die(__FILE__." :: ".__LINE__);

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function ProcessSearch()
    {
        try {
            global $solr_config, $oBrand;
            
            $this->oSolrQuery = new SolrQuery;
            
            $this->oSolrQuery->setQuery("Gap Year Australia");
    
            $aFilterQuery = array();
            $aFilterQuery['profile_type'] = "(1 OR 0)";
            $this->oSolrQuery->setFilterQuery($aFilterQuery);
    
            /*
             // now add activity, country or continent filters
             if (isset($aSearchParams['search-panel-activity']) && $aSearchParams['search-panel-activity'] != "NULL") {
             $fq['activity'] = '"'.$aSearchParams['search-panel-activity'].'"';
             }
             if (isset($aSearchParams['search-panel-country']) && $aSearchParams['search-panel-country'] != "NULL") {
             $fq['country'] = '"'.$aSearchParams['search-panel-country'].'"';
             }
             if (isset($aSearchParams['search-panel-continent']) && $aSearchParams['search-panel-continent'] != "NULL") {
             $fq['continent'] = '"'.$aSearchParams['search-panel-continent'].'"';
             }
             */
            /**
             * Select result processing class based on requested content type(s)
             */
            switch($this->oSolrQuery->getFilterQueryByName('profile_type')) {
                case "0" : // company
                    $class = "SolrCompanySearch";
                    break;
                case "1" : // placement
                    $class = "SolrPlacementSearch";
                    break;
                case "(1 OR 0)" : // company and placement
                    $class = "SolrCombinedProfileSearch";
                    break;
                case "2" : // articles
                    $class = "SolrCompanySearch";
                    break;
            }

            $this->oSolrSearch = new $class($solr_config);

            $this->oSolrSearch->setRows($iRows = 100);
            $this->oSolrSearch->setStart($iStart = 0);
            $this->oSolrSearch->setSiteId($oBrand->GetSiteId());
            $this->oSolrSearch->search($this->oSolrQuery->getQuery(),$this->oSolrQuery->getFilterQuery(),$this->oSolrQuery->getSort());
            $this->oSolrSearch->processResult();

            $this->ProcessSOLRSearchResult($this->oSolrSearch->getId());
            
            print_r("<pre>");
            var_dump($this->oSolrQuery);
            var_dump($this->oSolrSearch);
            print_r("</pre>");
            die();
        } catch (Exception $e) {
            print_r("<pre>");
            var_dump($e);
            print_r("</pre>");
            die(__FILE__."::".__LINE);
        }
    }
    
    public function ProcessSOLRSearchResult($aId = array(), $bSort = true)
    {
        // fetch returned id's, instantiate collection of content objects

        if (is_array($aId) && count($aId) >= 1) {
            if ($this->oSolrQuery->getFilterQueryByName('profile_type') == "1")  { // PLACEMENTS
                $aProfileUnsorted = PlacementProfile::Get("ID_LIST_SEARCH_RESULT",$aId);

                foreach($aProfile as $oProfile) {
                    $doc = $this->oSolrSearch->getResultByProfileId($oProfile->GetId());
                    $oProfile->SetDurationFrom($doc->duration_from);
                    $oProfile->SetDurationTo($doc->duration_to);
                }

            } elseif ($this->oSolrQuery->getFilterQueryByName('profile_type') == "0") { // COMPANY RESULTS
                $aProfileUnsorted = CompanyProfile::Get("ID",$aId);
            } elseif ($this->oSolrQuery->getFilterQueryByName('profile_type') == "2") { // articles
                
                foreach($aId as $id) {
                    
                    $oArticle = new Article;
                    $oArticle->SetFetchMode(FETCHMODE__SUMMARY);
                    $oArticle->GetById($id);
                    if (!is_numeric($oArticle->GetId())) continue;
                    $aProfileUnsorted[$id] = $oArticle;
                    
                }
                
            } elseif($this->oSolrQuery->getFilterQueryByName('profile_type') == "(1 OR 0)") { // company profiles & placements
                
                $this->aProfile = $this->oSolrSearch->getProfile();
                $bSort = false;
            }
            
            if ($bSort) // resort fetched profiles in SOLR search result order
            {
                $aProfile = array();
                foreach ($aId as $id) {
                    if (isset($aProfileUnsorted[$id])) {
                        $this->aProfile[$id] = $aProfileUnsorted[$id];
                    }
                }
            }

            print_r("<pre>");
            print_r("SOLR Count: ".$this->oSolrSearch->getNumFound()."<br />");
            print_r("ProfileId: ".count($aId)."<br />");
            print_r("NumberProfile: ".count($this->aProfile)."<br />");
            print_r($this->aProfile);
            print_r("</pre>");
            die();
            
        }

    }
}