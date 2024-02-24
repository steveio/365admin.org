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
                $oTemplateCfg = $this->oTemplateList->GetById($this->oContentMapping->GetTemplateId());
            } else {
                $oTemplateCfg = $this->oTemplateList->GetById(CONTENT_DEFAULT_RESULT_TEMPLATE);
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
                $this->oArticle->SetAttachedArticle($fetchId = FALSE);
                $this->aArticle = $this->oArticle->oArticleCollection->Get();
            }

            
            if ($this->oContentMapping->GetDisplayOptSearchResult())
            {
                $this->SetSearchResultPanel($this->oContentMapping->GetOptions());
            }

            if ($this->oContentMapping->GetDisplayOptReview())
            {
                $this->SetReviewTemplate("comment.php");
                $this->GetReviews($this->oArticle->GetId(), CONTENT_TYPE_ARTICLE, $this->oArticle->GetTitle());
            }

            // fetch blog articles ( manually attached article take precedence )
            if ($this->oContentMapping->GetDisplayOptBlogArticle() && count($this->oArticle->GetAttachedArticleId()) < 1)
            {
                $this->GetRelatedArticle($this->oArticle->GetId(), $limit = 12, "blog");
                $this->aArticle = $this->aRelatedArticle;
            }

            if ($this->oContentMapping->GetDisplayOptRelatedArticle())
            {
                $this->GetRelatedArticle($this->oArticle->GetId(), $limit = 6, "blog", $exclude = true);
            }
            
            if ($this->oContentMapping->GetDisplayOptRelatedProfile())
            {
                $this->GetRelatedProfile($this->oArticle->GetId(), PROFILE_PLACEMENT, $limit = 4);
            }
           
            $this->Render();

        } catch (Exception $e) {
            throw $e;
        }
    }


    protected function Render()
    {
        global $oHeader, $oFooter, $oBrand;

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
        global $oBrand;

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

        $oSearchResultPanel = new Layout();
        $oSearchResultPanel->Set("URI",$this->oRequestRouter->GetRequestUri());
        $oSearchResultPanel->Set('ARTICLE_DISPLAY_OPT_PTITLE',$aPageOptions[ARTICLE_DISPLAY_OPT_PTITLE]);
        $oSearchResultPanel->Set('ARTICLE_DISPLAY_OPT_OTITLE',$aPageOptions[ARTICLE_DISPLAY_OPT_OTITLE]);
        $oSearchResultPanel->Set('ARTICLE_DISPLAY_OPT_PINTRO',$aPageOptions[ARTICLE_DISPLAY_OPT_PINTRO]);
        $oSearchResultPanel->Set('ARTICLE_DISPLAY_OPT_OINTRO',$aPageOptions[ARTICLE_DISPLAY_OPT_OINTRO]);
        
        $oSearchResultPanel->Set('ARTICLE_DISPLAY_OPT_PLACEMENT',$aPageOptions[ARTICLE_DISPLAY_OPT_PLACEMENT]);
        $oSearchResultPanel->Set('ARTICLE_DISPLAY_OPT_SEARCH_KEYWORD',trim($aPageOptions[ARTICLE_DISPLAY_OPT_SEARCH_KEYWORD]));
        
        $oSearchResultPanel->Set('HIDE_FILTERS',false);

        $oSearchResultPanel->LoadTemplate('search_result.php');
        
        $this->oSearchResultPanel = $oSearchResultPanel;
    }
}