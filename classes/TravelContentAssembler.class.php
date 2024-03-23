<?php


class TravelContentAssembler extends ArticleContentAssembler
{
    
    public function __Construct()
    {
        parent::__Construct();
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
        $this->oTemplate->Set("iPageSize", $this->iPageSize);
        $this->oTemplate->Set("aPageOptions", $this->oContentMapping->GetOptions());
        
        $this->oTemplate->Set("oSearchResult", $this->oSearchResultPanel);
        
        $this->oTemplate->Set("aArticle", $this->aArticle); // blog article
        $this->oTemplate->Set("aAttachedArticle", $this->aAttachedArticle);
        $this->oTemplate->Set("aAttachedProfile", $this->oArticle->GetAttachedProfile());
        
        $this->oTemplate->Set("oReviewTemplate",$this->oReviewTemplate);
        $this->oTemplate->Set("aRelatedArticle", $this->aRelatedArticle);
        $this->oTemplate->Set("aRelatedProfile", $this->aRelatedProfile);
        
        $oActivity = new Activity();
        $aActivities = $oActivity->GetByCategory();
        
        $oCountry = new Country();
        $aDestinations = $oCountry->GetByContinent();
        
        $this->oTemplate->Set("aActivities",$aActivities);
        $this->oTemplate->Set("aDestinations",$aDestinations);

        $this->oTemplate->LoadTemplate("travel-page.php");
        
        
        print $oHeader->Render();
        print $this->oTemplate->Render();
        print $oFooter->Render();
        
        die();
    
    }
}