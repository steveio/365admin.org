<?php

/**
 * HomePage Content Assembler 
 * 
 * Class to fetch website homepage content & provision template rendering 
 * 
 * 
 *
 */

class HomepageContentAssembler extends AbstractContentAssembler {

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
            $oHomepageArticle->SetFetchMode(FETCHMODE__SUMMARY);
            $oHomepageArticle->SetAttachedArticleFetchLimit(10);
            $oHomepageArticle->Get($oBrand->GetWebsiteId(),"/homepage-intro");
            $this->oHomepageArticle = $oHomepageArticle;

            $this->Render();

        } catch (Exception $e) {
            throw $e;
        }
    }


    protected function Render()
    {
        global $oHeader, $oFooter, $oBrand;

        $this->oTemplate = new Template();

        $this->oTemplate->Set("oSearchPanel", $this->oSearchPanel);
        $this->oTemplate->Set("oBlogArticle", $this->oBlogArticle);
        $this->oTemplate->Set("oHomepageArticle", $this->oHomepageArticle);

        $this->oTemplate->LoadTemplate($this->strTemplatePath);


        print $oHeader->Render();
        //print $this->oTemplate->Render();
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
        
        $fq = array();
        $fq['profile_type'] = "1";
        //$fq['category_id'] = "7";
        $oSolrSearchPanel->setFilterQuery($fq);
        
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