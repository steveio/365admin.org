<?php

/**
 * HomePage Content Assembler 
 * 
 * Class to fetch website homepage content & provision template rendering 
 * 
 * 
 *
 */
require_once(BASE_PATH."/classes/ArticleContentAssembler.class.php");



class HomepageContentAssembler extends ArticleContentAssembler {

    protected $strTemplatePath;
    protected $oTemplate;
    protected $oSearchResultPanel;
    
    public function __Construct() 
    {
        parent::__construct();

        $this->oTemplateList = new TemplateList();
        $this->oTemplateList->GetFromDB();

        $this->oContentMapping = new ContentMapping(null, null, null);

    }

    public function GetById($id) {}
    
    
    public function GetByPath($path)
    {

        global $oBrand;

        try {

            $this->SetTemplatePath("homepage.php");
            $this->SetSearchResultPanel($aPageOptions = array());
            
            // homepage news articles
            
            $oBlogArticle = new Article();
            $oBlogArticle->SetFetchMode(FETCHMODE__SUMMARY);
            $oBlogArticle->SetAttachedArticleFetchLimit(12);
            $oBlogArticle->Get($oBrand->GetWebsiteId(),"/blog");
            $this->oBlogArticle = $oBlogArticle;

            $oHomepageArticle = new Article;
            $oHomepageArticle->SetFetchMode(FETCHMODE__FULL);
            $oHomepageArticle->Get($oBrand->GetWebsiteId(),"/");            
            
            if ($this->oContentMapping->GetDisplayOptReview())
            {
                $this->SetReviewTemplate("comment.php");
                $this->GetReviews($this->oArticle->GetId(), CONTENT_TYPE_ARTICLE, $this->oArticle->GetTitle());
            }
            
            // fetch related blog articles ( there are 0 attached articles )
            if ($this->oContentMapping->GetDisplayOptBlogArticle() && count($this->oArticle->GetAttachedArticleId()) < 1)
            {
                // search keywords specified, fetch blog articles related to these
                if (strlen($this->oContentMapping->GetSearchKeywords()) > 1)
                {
                    $strQuery = $this->oContentMapping->GetSearchKeywords();
                    
                    $this->SolrQuery($strQuery, $profile_type = 2, $rows = 25, $start = 0);
                    $this->aArticle = $this->aRelatedArticle;
                    
                } else {
                    // Default: Fetch articles via SOLR MLT
                    $this->GetRelatedArticle($this->oArticle->GetId(), $limit = 12, "blog");
                    $this->aArticle = $this->aRelatedArticle;
                }
            }
            
            if ($this->oContentMapping->GetDisplayOptRelatedArticle())
            {
                $this->GetRelatedArticle($this->oArticle->GetId(), $limit = 6, "blog", $exclude = true);
            }
            
            if ($this->oContentMapping->GetDisplayOptRelatedProfile())
            {
                $this->GetRelatedProfile($this->oArticle->GetId(), PROFILE_PLACEMENT, null, $limit = 8);
            }

            $this->oHomepageArticle = $oHomepageArticle;

            $oMessageProcessor = new MessageProcessor();
            $this->oMessagePanel = $oMessageProcessor->GetMessagePanel();

            $this->Render();

        } catch (Exception $e) {
            throw $e;
        }
    }


    protected function Render()
    {
        global $oHeader, $oFooter, $oBrand;

        $oJsInclude = new JsInclude();
        $oJsInclude->SetSrc("/includes/js/autocomplete/jquery-ui.min.js");
        $oHeader->SetJsInclude($oJsInclude);
        
        $oJsInclude = new JsInclude();
        $oJsInclude->SetSrc("/includes/js/search_panel.js");
        $oHeader->SetJsInclude($oJsInclude);
        
        $oHeader->Reload();
        
        
        $this->oTemplate = new Template();

        $this->oTemplate->Set("aPageOptions", $this->oContentMapping->GetOptions());

        $this->oTemplate->Set("oSearchPanel", $this->oSearchPanel);
        $this->oTemplate->Set("oArticle", $this->oHomepageArticle);

        $this->oTemplate->Set("aArticle", $this->oBlogArticle->oArticleCollection->Get());
        $this->oTemplate->Set("aAttachedArticle", $this->oHomepageArticle->oArticleCollection->Get());

        $this->oTemplate->Set("oReviewTemplate",$this->oReviewTemplate);
        $this->oTemplate->Set("aRelatedArticle", $this->aRelatedArticle);
        $this->oTemplate->Set("aRelatedProfile", $this->aRelatedProfile);
        
        $this->oTemplate->LoadTemplate($this->strTemplatePath);


        print $oHeader->Render();
        print $this->oMessagePanel->Render();
        print $this->oTemplate->Render();
        print $oFooter->Render();
        
        die();        
    }
    
    public function SetPageHeader()
    {
        global $oHeader, $oBrand;
        
        $oHeader->SetTitle();
        $oHeader->SetDesc();
        $oHeader->SetKeywords();
        $oHeader->SetUrl();
        
        $oHeader->Reload();
    }

    public function SetSearchResultPanel($aPageOptions)
    {
        $oSolrSearchPanel = new SolrSearchPanel;
        
        // clear any previous search
        SolrSearchPanelSearch::clearFromSession();
        
        //$fq = array();
        //$fq['profile_type'] = "1";
        //$fq['category_id'] = "7";
        //$oSolrSearchPanel->setFilterQuery($fq);
        
        $aFacetField = array();
        $aFacetField[] = array("country" => "country");
        $aFacetField[] = array("continent" => "continent");
        $aFacetField[] = array("activity" => "activity");
        $oSolrSearchPanel->setFacetField($aFacetField);
        $oSolrSearchPanel->setup($_CONFIG['site_id']);
        
        $oSearchPanel = new Template;
        $oSearchPanel->Set('HOSTNAME',$_CONFIG['url']);
        $oSearchPanel->Set('ACTIVITY_LIST',Activity::getActivitySelectList());

        $oSearchPanel->LoadTemplate("./search_panel.php");

        $this->oSearchPanel = $oSearchPanel;
    }

}