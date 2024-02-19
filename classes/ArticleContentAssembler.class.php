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
                if ($this->oContentMapping->GetDisplayOptArticle())
                    $this->oArticle->SetFetchAttachedArticle(false);
                
                if ($this->oContentMapping->GetDisplayOptProfile())
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

            // put content id in scope of parent class for fetching common associated content (reviews etc)
            $this->SetLinkId($this->oArticle->GetId()); 


            //$this->oArticle->SetAttachedArticleFetchLimit(null);

            // fetch associated articles for collection template
            if ($oTemplateCfg->is_collection)
            {
                if ($this->oContentMapping->GetOptionEnabled(ARTICLE_DISPLAY_OPT_PATH)) // fetch by path eg /blog/post1, /blog/post2
                {
                    $this->oArticle->SetAttachedArticleIdByPath($this->oContentMapping->GetSectionUri());
    
                } elseif ($this->oContentMapping->GetOptionEnabled(ARTICLE_DISPLAY_OPT_ATTACHED) ) { // fetch "attached" articles
    
                    $this->oArticle->SetAttachedArticleId();

                }
                
                $this->iTotalMatchedArticle = $this->oArticle->GetAttachedArticleTotal();
    
                //print_r("<pre>");
                //print_r("Attached Article Id: ".$this->oArticle->GetAttachedArticleTotal());
    
                $startIndex = ($iPage == 1) ? 0 : ($iPage * $oTemplateCfg->fetch_limit);
                $endIndex = $startIndex + $oTemplateCfg->fetch_limit -1;
    
                //print_r("start: ".$startIndex.", end: ".$endIndex);
    
                $this->oArticle->PaginateAttachedArticleId($startIndex, $endIndex);
                
                //print_r("Paginated Total: ".$this->oArticle->GetAttachedArticleTotal());
                //print_r("</pre>");
    
                $this->oArticle->SetAttachedArticle($fetch = FALSE);
            }

            if (!$oTemplateCfg->is_collection && $this->oContentMapping->GetDisplayOptReview())
            {
                $this->GetReviews($this->oArticle->GetId(), CONTENT_TYPE_ARTICLE, $this->oArticle->GetTitle());
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
        $this->oTemplate->Set("iTotalMatchedArticle",$this->iTotalMatchedArticle);
        
        $this->oTemplate->Set("oReviewTemplate",$this->oReviewTemplate);

        $this->oTemplate->Set("aPageOptions", $this->oContentMapping->GetOptions());

        //$this->oTemplate->Set("oSearchResult", $this->);
        //$this->oTemplate->Set("oRelatedArticle", $this->oRelatedArticle);
        //$this->oTemplate->LoadTemplate("profile_company_view.php");

        $this->oTemplate->LoadTemplate($this->strTemplatePath);

        
        print $oHeader->Render();
        print $this->oTemplate->Render();
        print $oFooter->Render();
        
        die();
        
    }
    
    public function SetPageHeader()
    {
        global $oHeader, $oBrand;
        
        $aKeywords = $this->GetKeywords($this->oArticle->GetId());
        $oHeader->SetTitle($this->oArticle->GetTitle());
        $oHeader->SetDesc($this->oArticle->GetDescShortPlaintext($trunc = 160));
        $oHeader->SetKeywords(implode(",",$aKeywords));
        $oHeader->SetUrl($oBrand->GetWebsiteUrl().$this->oRequestRouter->GetRequestUri());
        
        $oHeader->Reload();
    }
    
}