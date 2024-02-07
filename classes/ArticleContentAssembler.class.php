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

    public function __Construct() 
    {
        parent::__construct();

        $this->SetLinkTo(CONTENT_TYPE_ARTICLE);

    }

    public function GetById($id)
    {
        /* retrieve an unpublished article */
        $oArticle = new Article();
        $oArticle->GetById($id);
        $oArticle->LoadTemplate($this->strTemplatePath,$aOptions = array());
        
        return $oArticle;
    }
    
    
    public function GetByPath($path, $website_id = 0)
    {

        try {
            $oContentMapping = new ContentMapping(null, null, null);
            
            if ($oContentMapping->GetByPath($path))
            {
                $oTemplate = $this->oTemplateList->GetById($oContentMapping->GetTemplateId());
            } else {
                $oTemplate = $this->oTemplateList->GetById(CONTENT_DEFAULT_RESULT_TEMPLATE);
            }

            $oArticle = new Article;

            if ($oTemplate->fetch_mode == FETCHMODE__SUMMARY)
            {
                $oArticle->SetFetchMode(FETCHMODE__SUMMARY);
            }
            
            $exact = true; // exact or fuzzy path match
            $limit = 100;

            if ($oContentMapping->GetOptionEnabled(ARTICLE_DISPLAY_OPT_PATH))
            {
                $exact = false;
            }
            
            // fetch article and attached content                
            if (!$oContentMapping->GetOptionEnabled(ARTICLE_DISPLAY_OPT_ATTACHED))
            {
                $oArticle->SetFetchAttachedArticle(FALSE);
                $oArticle->SetFetchAttachedProfile(FALSE);
            }

            if (is_numeric($limit))
            {
                $oArticle->SetAttachedArticleFetchLimit($limit);
            }

            // fetch article mapped directly to URL path eg /blog, /country/brazil
            if (!$oArticle->Get($website_id, $oContentMapping->GetSectionUri(), $limit, true))
            {
                // no mapped article content, treat namespaced URL as search request
                if ($this->GetRequestRouter()->isNamespaceMatchedURL()) 
                {
                    return $this->oRequestRouter->ProcessSearchPageRequest();
                }
            }
            
            // put content id in scope of parent class for fetching associated content
            $this->SetLinkId($oArticle->GetId()); 

            if (!$exact)
            {
                // fetch content as articles published to main article sub-path eg /blog/post1, /blog/post2
                $oArticle->Get($website_id, $oContentMapping->GetSectionUri(), $limit, false);
            }

            $this->GetReviews($oArticle->GetId(), CONTENT_TYPE_ARTICLE, $oArticle->GetTitle());


            $oArticle->LoadTemplate($oTemplate->filename,$aOptions = array());

            return $oArticle;

        } catch (Exception $e) {
            throw $e;
        }
    }

}