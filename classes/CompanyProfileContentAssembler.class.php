<?php

/**
 * Company Profile Content Assembler 
 * 
 * Handles content provisioning & render of company profile pages  
 *
 */

class CompanyProfileContentAssembler extends ProfileContentAssembler {
  
    protected $oProfile;  // instance of ProfileCompany
    protected $oTemplate;  // instance of template
    protected $oReviewTemplate;  // instance of reviews / comments template

    protected $aPlacement = array(); // array of placements offered by company

    public function __Construct() 
    {
        parent::__construct();

        $this->SetType(PROFILE_COMPANY);
        $this->SetLinkTo(CONTENT_TYPE_COMPANY);

        $this->oProfile = new CompanyProfile();
        $this->oTemplate = new Template();
        $this->oReviewTemplate = new Template();

    }

    public function GetById($id)
    {
    }

    /*
     * Get By Path
     * 
     * Resolves profile based on URL segment /company/<url-name>
     * 
     */
    public function GetByPath($path, $website_id = 0)
    {

        global $db, $oHeader, $oFooter;

        try {
            
            parent::GetByUrlName($path);
            parent::SetPageHeader();

            // set Logo & Banner images
            if (is_object($this->oProfile->GetImage(0,LOGO_IMAGE)))
            {
                $this->oTemplate->Set('logo_img',$this->oProfile->GetImage(0,LOGO_IMAGE)->GetHtml("",$this->oProfile->GetTitle()));
                $this->oTemplate->Set('logo_url_sm',$this->oProfile->GetImage(0,LOGO_IMAGE)->GetUrl("_sm"));
            }
            if (is_object($this->oProfile->GetImage(1,LOGO_IMAGE))) 
            {
                $this->oTemplate->Set('banner_img',$this->oProfile->GetImage(1,LOGO_IMAGE)->GetHtml("",$this->oProfile->GetTitle()));
            }

            $this->GetEnquiryButtonHtml();

            if ($this->oProfile->GetListingType() >= BASIC_LISTING) 
            {
                $this->GetPlacements();
                $this->oTemplate->Set("displayRelatedProfile","COMPANY");
            } else {

                $iCompanyId = null;
                if ($this->oProfile->GetListingType() >= BASIC_LISTING)
                {
                    $iCompanyId = $this->oProfile->GetId();
                }
                $this->GetRelatedProfile($this->oProfile->GetOid(),CONTENT_PLACEMENT, $iCompanyId, $iLimit = 8);
                $this->aPlacement  = $this->aRelatedProfile;

                $this->oTemplate->Set("displayRelatedProfile","RELATED");

                $this->GetRelatedArticle($this->oProfile->GetOid(), $iLimit = 6);
            }

            $this->oTemplate->Set("aEnquiryButtonHtml", $this->aEnquiryButtonHtml);
            $this->oTemplate->Set("aPlacement",$this->aPlacement);
            $this->oTemplate->Set("oProfile",$this->oProfile);
            $this->oTemplate->Set("oReviewTemplate",$this->oReviewTemplate);
            $this->oTemplate->Set("aRelatedArticle", $this->aRelatedArticle);
            $this->oTemplate->Set("aRelatedProfile", $this->aRelatedProfile);

            $this->oTemplate->LoadTemplate("profile_company_view.php");

            print $oHeader->Render();
            print $this->oTemplate->Render();
            print $oFooter->Render();

            die();

        } catch (Exception $e) {
            throw $e;
        }
    }

    /* get placements associated with this company */
    public function GetPlacements()
    {
        $this->aPlacement = PlacementProfile::GetPlacementById($this->oProfile->GetCompanyId(),"company_id");
    }

    public function GetEnquiryButtonHtml()
    {
        $cssClass = "btn btn-primary rounded-pill px-3";

        if ($this->oProfile->GetListingType() >=  BASIC_LISTING )
        {
            if (strlen($this->oProfile->GetUrl()) > 1 && $this->oProfile->GetUrl() != "http://" && $this->oProfile->GetFilterFromSearch() != true) {
                $this->aEnquiryButtonHtml['WEBSITE'] = "<a class=\"".$cssClass."\" href=\"".$this->oProfile->GetUrl()."\" target=\"_new\" title=\"Visit Website\" target=\"_blank\">Visit Website</a>";
            }
    
            if (strlen($this->oProfile->GetApplyUrl()) > 1 && $this->oProfile->GetFilterFromSearch() != true) {
                $this->aEnquiryButtonHtml['APPLY'] = "<a class=\"".$cssClass."\"  href=\"".$this->oProfile->GetApplyUrl()."\" title=\"Apply Online\" target=\"_blank\">Apply Online</a>";
            }
        }

        if (strlen(trim($this->oProfile->GetEmail())) > 1) {
            if ($this->oProfile->HasEnquiryOption(ENQUIRY_BOOKING) && !isset($this->aEnquiryButtonHtml['APPLY'])) {

                $this->aEnquiryButtonHtml['BOOKING'] = "<a class=\"".$cssClass."\"  href=\"".Enquiry::GetRequestUrl('BOOKING',$this->oProfile->GetId(),PROFILE_COMPANY)."\" title=\"Booking Enquiry\">Booking Enquiry</a>";                
            }
            if ($this->oProfile->HasEnquiryOption(ENQUIRY_GENERAL) && !$this->oProfile->HasEnquiryOption(ENQUIRY_BOOKING) || $this->oProfile->GetListingType() < BASIC_LISTING ) {
                $this->aEnquiryButtonHtml['ENQUIRY'] = "<a class=\"".$cssClass."\"  href=\"".Enquiry::GetRequestUrl('GENERAL',$this->oProfile->GetId(),PROFILE_COMPANY)."\" title=\"Make an Enquiry\">Enquiry</a>";
            }
            if ($this->oProfile->HasEnquiryOption(ENQUIRY_JOB_APP)) {
                $this->aEnquiryButtonHtml['JOB_APP'] = "<a class=\"".$cssClass."\"  href=\"".Enquiry::GetRequestUrl('JOB_APP',$this->oProfile->GetId(),PROFILE_COMPANY)."\" title=\"Apply Online\" target=\"_blank\">Apply</a>";
            }
        }
    }
    
    

    public function ProcessCompanyAZPageRequest()
    {
        if ($this->aRequestUri[2] == "a-z" && $this->aRequestUri[3] != "") {
            $this->isLowerCaseLetter($this->aRequestUri[3]);
            $_REQUEST['letter'] = $this->aRequestUri[3];
        } else {
            $_REQUEST['letter'] = "a";
        }
        // @todo - migrate to OO template API
        require_once("./company_list.php");
    }
    
}
