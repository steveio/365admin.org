<?php

/**
 * Article Content Assembler 
 * 
 * Class to fetch article, associated content & provision template rendering 
 * 
 * 
 *
 */

class ArticleContentAssembler extends AbstractContentAssembler {

    protected $oArticle;
    protected $oContentMapping;
    protected $strTemplatePath;
    protected $oTemplate;
    protected $oSearchResultPanel;
    
    protected $iTotalMatchedArticle = 0;

    public function __Construct() 
    {
        parent::__construct();

        $this->SetLinkTo(CONTENT_TYPE_ARTICLE);

        $this->oTemplateList = new TemplateList();
        $this->oTemplateList->GetFromDB();

        $this->oContentMapping = new ContentMapping(null, null, null);

    }

    public function GetById($id)
    {

        $oTemplateCfg = $this->oTemplateList->GetById(CONTENT_DEFAULT_RESULT_TEMPLATE);

        /* retrieve an unpublished article */
        $this->oArticle = new Article();
        $this->oArticle->GetById($id);

        $this->aArticle = array();

        $this->strTemplatePath = $oTemplateCfg->filename;

        $this->Render();

    }
    
    
    public function GetByPath($path)
    {

        global $oBrand;

        try {

            if ($this->oContentMapping->GetByPath($path))
            {
                $iTemplateId = (is_numeric($this->oContentMapping->GetTemplateId())) ? $this->oContentMapping->GetTemplateId() : CONTENT_DEFAULT_RESULT_TEMPLATE;                
                $oTemplateCfg = $this->oTemplateList->GetById($iTemplateId);
            }
            
            $this->strTemplatePath = $oTemplateCfg->filename;

            $this->oArticle = new Article;
            
            if ($oTemplateCfg->fetch_mode == FETCHMODE__SUMMARY)
            {
                $this->oArticle->SetFetchMode(FETCHMODE__SUMMARY);
            }

            // handle collection tempates with paged result sets
            if (is_numeric($oTemplateCfg->fetch_limit) &&  $oTemplateCfg->fetch_limit >= 1)
            {
                $this->oArticle->SetAttachedArticleFetchLimit($oTemplateCfg->fetch_limit);

                $iPage = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
            }


            if(!$oTemplateCfg->is_collection) // individual article template
            {
                // set article fetch options based on publisher settings
                if ($this->oContentMapping->GetDisplayOptRelatedArticle())
                    $this->oArticle->SetFetchAttachedArticle(false);

                if ($this->oContentMapping->GetDisplayOptRelatedProfile())
                    $this->oArticle->GetFetchAttachedProfile(false);

            } else {
                $this->oArticle->SetFetchAttachedArticle(false);
                $this->oArticle->GetFetchAttachedProfile(false);
            }

            // fetch article mapped directly to URL path eg /blog, /country/brazil
            if (!$this->oArticle->Get($oBrand->GetSiteId(), $this->oContentMapping->GetSectionUri(), $limit = null, $exact = true))
            {
                // no mapped article content, treat namespaced URL as search request
                if ($this->GetRequestRouter()->isNamespaceMatchedURL()) 
                {
                    return $this->oRequestRouter->ProcessSearchPageRequest();
                }
            }
            
            // setup HTML header meta tags
            $this->SetPageHeader();


            //$this->oArticle->SetAttachedArticleFetchLimit(null);

            // fetch associated articles for collection template
            if ($this->oContentMapping->GetOptionEnabled(ARTICLE_DISPLAY_OPT_PATH)) // fetch by path eg /blog/post1, /blog/post2
            {
                $this->oArticle->SetAttachedArticleIdByPath($this->oContentMapping->GetSectionUri());
            } else { // fetch 0..n "attached" articles
                $this->oArticle->SetAttachedArticleId();
            }

            if (is_numeric($oTemplateCfg->fetch_limit))
            {
                $startIndex = ($iPage == 1) ? 0 : ($iPage * $oTemplateCfg->fetch_limit);
                $endIndex = $startIndex + $oTemplateCfg->fetch_limit -1;
                $this->oArticle->PaginateAttachedArticleId($startIndex, $endIndex);
            }

            if (count($this->oArticle->GetAttachedArticleId()) >= 1)
            {
                $this->aArticle = $this->oArticle->oArticleCollection->Get();
            }

            
            if ($this->oContentMapping->GetDisplayOptSearchPanel())
            {
                $this->SetSearchResultPanel($this->oContentMapping->GetOptions());
            }

            if ($this->oContentMapping->GetDisplayOptReview())
            {
                $this->SetReviewTemplate("comment.php");
                $this->GetReviews($this->oArticle->GetId(), CONTENT_TYPE_ARTICLE, $this->oArticle->GetTitle());
            }

            // fetch related blog articles ( there are 0 attached articles )
            if ($this->oContentMapping->GetDisplayOptBlogArticle())
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
                $this->aRelatedArticle = array();
                $this->GetRelatedArticle($this->oArticle->GetId(), $limit = 6);
            }
            
            if ($this->oContentMapping->GetDisplayOptRelatedProfile())
            {
                $this->GetRelatedProfile($this->oArticle->GetId(), PROFILE_PLACEMENT, null, $limit = 8);
            }
            
            $this->Render();

        } catch (NotFoundException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }


    protected function Render()
    {
        global $oHeader, $oFooter, $oBrand;

        $oCssInclude = new CssInclude();
        $oCssInclude->SetHref('/css/jquery.rateyo.min.css');
        $oCssInclude->SetMedia('screen');
        $oHeader->SetCssInclude("CSS_GENERIC", $oCssInclude);
        $oHeader->Reload();

        $oJsInclude = new JsInclude();
        $oJsInclude->SetSrc("/includes/js/jquery.rateyo.min.js");
        $oHeader->SetJsInclude($oJsInclude);

        $oHeader->Reload();

        $this->oTemplate = new Template();
        
        $this->oTemplate->Set("oArticle",$this->oArticle);
        
        $this->oTemplate->Set("iTotalMatchedArticle",$this->iTotalMatchedArticle); // number matched articles (excluding pagination)
        $this->oTemplate->Set("aPageOptions", $this->oContentMapping->GetOptions());

        $this->oTemplate->Set("oSearchResult", $this->oSearchResultPanel);

        $this->oTemplate->Set("aArticle", $this->aArticle);

        $this->oTemplate->Set("oReviewTemplate",$this->oReviewTemplate);
        $this->oTemplate->Set("aRelatedArticle", $this->aRelatedArticle);
        $this->oTemplate->Set("aRelatedProfile", $this->aRelatedProfile);

        $this->oTemplate->LoadTemplate($this->strTemplatePath);

        
        print $oHeader->Render();
        print $this->oTemplate->Render();
        print $oFooter->Render();
        
        die();
        
    }
    
    public function SetPageHeader()
    {
        global $oHeader, $oBrand;
        
        $aKeywords = $this->GetKeywords($this->oArticle->GetId(),2);
        $oHeader->SetTitle($this->oArticle->GetTitle());
        $oHeader->SetDesc($this->oArticle->GetDescShortPlaintext($trunc = 160));
        $oHeader->SetKeywords(implode(",",$aKeywords));
        $oHeader->SetUrl($oBrand->GetWebsiteUrl().$this->oRequestRouter->GetRequestUri());
        
        $oHeader->Reload();
    }

    public function SetSearchResultPanel($aPageOptions)
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
        

        if ($aPageOptions[ARTICLE_DISPLAY_OPT_SEARCH_CONFIG] == ARTICLE_SEARCH_KEYWORDS)
        {
            // search query from keywords
            $strQuery = $this->oContentMapping->GetSearchKeywords();
            $iSearchType = 1;
        } elseif ($aPageOptions[ARTICLE_DISPLAY_OPT_SEARCH_CONFIG] == ARTICLE_SEARCH_URL) {
            // search query from URI eg /volunteer-with-animals 
            $strQuery = $this->oRequestRouter->GetRequestUri();
            $iSearchType = 1;
        } else { // SEARCH_PANEL_ONLY (facet counts (for query on URI / Keywords), no results displayed)
            $strQuery = (strlen($aPageOptions[ARTICLE_DISPLAY_OPT_SEARCH_KEYWORD]) > 1) ? $this->oContentMapping->GetSearchKeywords() : $this->oRequestRouter->GetRequestUri();
            $iSearchType = 0;
        }

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
        
        $this->oSearchResultPanel = $oSearchResultPanel;
    }
    
}