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
    
    
    public function GetByPath($path, $website_id = 0)
    {

        try {
            
            if ($this->oContentMapping->GetByPath($path))
            {
                $oTemplateCfg = $this->oTemplateList->GetById($this->oContentMapping->GetTemplateId());
            } else {
                $oTemplateCfg = $this->oTemplateList->GetById(CONTENT_DEFAULT_RESULT_TEMPLATE);
            }
            
            $this->strTemplatePath = $oTemplateCfg->filename;

            $this->oArticle = new Article;

            if ($this->oTemplate->fetch_mode == FETCHMODE__SUMMARY)
            {
                $this->oArticle->SetFetchMode(FETCHMODE__SUMMARY);
            }
            
            $exact = true; // exact or fuzzy path match
            $limit = 100;

            if ($this->oContentMapping->GetOptionEnabled(ARTICLE_DISPLAY_OPT_PATH))
            {
                $exact = false;
            }
            
            // fetch article and attached content                
            if (!$this->oContentMapping->GetOptionEnabled(ARTICLE_DISPLAY_OPT_ATTACHED))
            {
                $this->oArticle->SetFetchAttachedArticle(FALSE);
                $this->oArticle->SetFetchAttachedProfile(FALSE);
            }

            if (is_numeric($limit))
            {
                $this->oArticle->SetAttachedArticleFetchLimit($limit);
            }

            // fetch article mapped directly to URL path eg /blog, /country/brazil
            if (!$this->oArticle->Get($website_id, $this->oContentMapping->GetSectionUri(), $limit, true))
            {
                // no mapped article content, treat namespaced URL as search request
                if ($this->GetRequestRouter()->isNamespaceMatchedURL()) 
                {
                    return $this->oRequestRouter->ProcessSearchPageRequest();
                }
            }
            
            // put content id in scope of parent class for fetching associated content
            $this->SetLinkId($this->oArticle->GetId()); 

            if (!$exact)
            {
                // fetch content as articles published to main article sub-path eg /blog/post1, /blog/post2
                $this->oArticle->Get($website_id, $this->oContentMapping->GetSectionUri(), $limit, false);
            }
            
            $this->GetReviews($this->oArticle->GetId(), CONTENT_TYPE_ARTICLE, $this->oArticle->GetTitle());

            $this->Render();

        } catch (Exception $e) {
            throw $e;
        }
    }


    protected function Render()
    {
        global $oHeader, $oFooter;

        $this->oTemplate = new Template();

        $this->oTemplate->Set("oArticle",$this->oArticle);
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
}