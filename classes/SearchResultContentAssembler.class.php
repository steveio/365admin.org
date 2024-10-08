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
    
    private $strSearchQuery;
    
    protected $aArticle;
    protected $aProfile;

    public function __Construct() 
    {
        parent::__construct();

        $this->aProfile = array();
        $this->aArticle = array();
        
    }

    public function GetSearchQuery()
    {
        return $this->strSearchQuery;        
    }
    
    public function SetSearchQuery($query)
    {
        $this->strSearchQuery = $query;
    }

    public function GetById($id) {}
    
    
    public function GetByPath($path, $website_id = 0)
    {

        global $oHeader, $oFooter;

        try {


            $this->SetSearchPanel();

            print $oHeader->Render();
            print $this->oSearchPanel->Render();
            print $oFooter->Render();

            die();

        } catch (Exception $e) {
            throw $e;
        }
    }


    /*
     * Search Panel - Keywords, Destination (auto-complete), Activity
     * 
     * Submits (via AJAX) to /search-dispatch ( /webservices/searchPanelDispatch_ajax.php )
     * 
     */
    public function SetSearchPanel()
    {
        $oSolrSearchPanel = new SolrSearchPanel;
        
        $oSearchPanel = new Template;
        $oSearchPanel->Set('HOSTNAME',$_CONFIG['url']);
        
        $oSearchPanel->LoadTemplate("./search_panel.php");
        
        $this->oSearchPanel = $oSearchPanel;
    }

    /**
     * Search Panel & Search Results
     * 
     * Submits via AJAX to SOLR webservice API 
     * 
     */
    public function SetSearchResultPanel()
    {
        
        /* have a look for any search params in session */
        $oSolrSearchPanelSearch = SolrSearchPanelSearch::getFromSession();
        
        if (is_object($oSolrSearchPanelSearch)) {
            $oSolrSearchPanelSearch->setFiltersByUri($sUri);
            
        if ($oSolrSearchPanelSearch->filterEnabled('activity')) {
            $oSearchResultPanel->Set("FACET_ACTIVITY",$oSolrSearchPanelSearch->getFilterAsCheckbox('activity'));
        }
        
        if (is_numeric($oSolrSearchPanelSearch->getDurationFromId)) {
            //$oSearchResultPanel->Set("FACET_DURATION_FROM","<select id=''><option></option></select>");
        }
        
        if (is_numeric($oSolrSearchPanelSearch->getDurationToId)) {
            $oSearchResultPanel->Set("FACET_DURATION_FROM",$oSolrSearchPanelSearch->getFilterAsCheckbox('activity'));
        }
            
        /*
         $oSearchResultPanel->Set("FACET_COUNTRY",$sUri);
         $oSearchResultPanel->Set("FACET_CONTINENT",$sUri);
         */
        }
        
        $strQuery = '';
        $strProfileType = "(1 OR 0)"; // default: return company & placement profiles        
        
        $strQuery = $this->oRequestRouter->GetRequestUri(2);
        $this->oRequestRouter->validateUriNamespaceIdentifier($strQuery);

        $iSearchType = 1;
        
        $iRows = 24;
        
        $oSearchResultPanel = new Layout();
        $oSearchResultPanel->Set("API_URL",API_URL);
        $oSearchResultPanel->Set("SEARCH_QUERY",$strQuery);
        $oSearchResultPanel->Set("SEARCH_TYPE",$iSearchType);
        $oSearchResultPanel->Set("SEARCH_PROFILE_TYPE",$strProfileType);
        $oSearchResultPanel->Set("SEARCH_ROWS",$iRows);
        $oSearchResultPanel->Set('ARTICLE_DISPLAY_OPT_PTITLE',$aPageOptions[ARTICLE_DISPLAY_OPT_PTITLE]);
        $oSearchResultPanel->Set('ARTICLE_DISPLAY_OPT_PINTRO',$aPageOptions[ARTICLE_DISPLAY_OPT_PINTRO]);
        $oSearchResultPanel->Set('HIDE_FILTERS',false);
        
        $oSearchResultPanel->LoadTemplate('search_result.php');
        
        $this->oSearchPanel = $oSearchResultPanel;
    }

    
    /*
     * Run SOLR search
     *
     * @param profile_type { 0 = Company, 1 = Placement, 2 = Article, (1 OR 0) Comined profile)
     *
     */
    public function ProcessSearch($profile_type, $iRows = 100)
    {
        try {
            
            global $solr_config, $oBrand;
            
            $this->oSolrQuery = new SolrQuery;
            $this->oSolrQuery->setQuery($this->GetSearchQuery());
            
            $aFilterQuery = array();
            $aFilterQuery['profile_type'] = $profile_type;
            $this->oSolrQuery->setFilterQuery($aFilterQuery);
            
            if ($profile_type == 2) // article
            {
                $this->oSolrQuery->setSort('score','desc');
                $this->oSolrQuery->setSort('last_updated','asc');
            } else {
                $this->oSolrQuery->setSort('score','desc');
                $this->oSolrQuery->setSort('prod_type','desc');
            }
            
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
            
            $this->GetSolrSearchInstance();
            
            $this->oSolrSearch->setRows($iRows);
            $this->oSolrSearch->setStart($iStart = 0);
            $this->oSolrSearch->setSiteId($oBrand->GetSiteId());
            $this->oSolrSearch->search($this->oSolrQuery->getQuery(),$this->oSolrQuery->getFilterQuery(),$this->oSolrQuery->getSort());
            $this->oSolrSearch->processResult();
            
            $this->ProcessSOLRSearchResult($this->oSolrSearch->getId());
            
            
        } catch (Exception $e) {
        }
    }

    private function GetSolrSearchInstance()
    {
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
    }

    public function ProcessSOLRSearchResult($aId = array(), $bSort = true)
    {
        // fetch returned id's, instantiate collection of content objects

        if (is_array($aId) && count($aId) >= 1) {
            if ($this->oSolrQuery->getFilterQueryByName('profile_type') == "1")  { // PLACEMENTS
                
                $aProfileUnsorted = PlacementProfile::Get("ID_LIST_SEARCH_RESULT",$aId, FETCHMODE__SUMMARY);

                foreach($aProfile as $oProfile) {
                    $doc = $this->oSolrSearch->getResultByProfileId($oProfile->GetId());
                    $oProfile->SetDurationFrom($doc->duration_from);
                    $oProfile->SetDurationTo($doc->duration_to);
                }
                
                $this->SortResults($aId, $aProfileUnsorted, CONTENT_PLACEMENT);

            } elseif ($this->oSolrQuery->getFilterQueryByName('profile_type') == "0") { // COMPANY RESULTS

                $aProfileUnsorted = CompanyProfile::Get("ID",$aId, FETCHMODE__SUMMARY);
                
                $this->SortResults($aId, $aProfileUnsorted, CONTENT_COMPANY);

            } elseif ($this->oSolrQuery->getFilterQueryByName('profile_type') == "2") { // articles
                
                foreach($aId as $id) {
                    
                    $oArticle = new Article;
                    $oArticle->SetFetchMode(FETCHMODE__SUMMARY);
                    $oArticle->GetById($id);
                    if (!is_object($oArticle) || !is_numeric($oArticle->GetId())) continue;
                    $aProfileUnsorted[$id] = $oArticle;      
                }

                $this->SortResults($aId, $aProfileUnsorted, CONTENT_ARTICLE);

            } elseif($this->oSolrQuery->getFilterQueryByName('profile_type') == "(1 OR 0)") { // company profiles & placements
                
                $this->aProfile = $this->oSolrSearch->getProfile();
                $bSort = false;
            }
         

            print_r("<pre>");
            print_r("SOLR Count: ".$this->oSolrSearch->getNumFound()."<br />");
            print_r("SOLR ID Count: ".count($aId)."<br />");
            print_r("ProfileCount: ".count($this->aProfile)."<br />");
            print_r("ArticleCount: ".count($this->aArticle)."<br />");
            //print_r($this->aProfile);
            //print_r($this->aProfile);
            print_r("</pre>");

        }

    }
    
    private function SortResults($aId, $aProfileUnsorted, $iContentType)
    {
        foreach ($aId as $id) {
            if (isset($aProfileUnsorted[$id])) {
                if (in_array($iContentType, array(CONTENT_COMPANY, CONTENT_PLACEMENT)))
                {
                    if (is_object($aProfileUnsorted[$id]))
                        $this->aProfile[$id] = $aProfileUnsorted[$id];
                } else {
                    if (is_object($aProfileUnsorted[$id]))
                        $this->aArticle[$id] = $aProfileUnsorted[$id];
                }
            }
        }        
    }
}