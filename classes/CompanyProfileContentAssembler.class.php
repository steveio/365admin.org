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
    
    protected $aEnquiryButtonHtml = array();

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
            
            $this->oProfile->SetProfileCount();

            // set Logo & Banner images
            if (is_object($this->oProfile->GetImage(0,LOGO_IMAGE))) $this->oTemplate->Set('logo_img',$this->oProfile->GetImage(0,LOGO_IMAGE)->GetHtml("",$this->oProfile->GetTitle()));
            if (is_object($this->oProfile->GetImage(1,LOGO_IMAGE))) $this->oTemplate->Set('banner_img',$this->oProfile->GetImage(1,LOGO_IMAGE)->GetHtml("",$this->oProfile->GetTitle()));
            

            if ($this->oProfile->GetListingType() >= BASIC_LISTING) 
            {
                $this->GetPlacements();
            } else {
                $this->GetRelatedProfile($this->oProfile->GetOid(),CONTENT_PLACEMENT);
            }

            $this->oTemplate->Set("aPlacement",$this->aPlacement);
            $this->oTemplate->Set("oProfile",$this->oProfile);
            $this->oTemplate->Set("oReviewTemplate",$this->oReviewTemplate);
            $this->oTemplate->Set("oRelatedArticle", $this->oRelatedArticle);            
            $this->oTemplate->LoadTemplate("profile_company_view.php");

            print $oHeader->Render();
            print $this->oTemplate->Render();
            print $oFooter->Render();


            /*
            print_r("<pre>");
            var_dump($this->oProfile);
            print_r("</pre>");            
            die(__FILE__."::".__LINE__);
            */

        } catch (Exception $e) {
            throw $e;
        }
    }

    /* get placements associated with this company */
    public function GetPlacements()
    {
        $this->aPlacement = PlacementProfile::Get("COMPANY_ID",$this->oProfile->GetId(), $filter_from_search = false);
    }

    public function GetEnquiryButtonHtml()
    {

        if (strlen($this->oProfile->GetUrl()) > 1 && $this->oProfile->GetUrl() != "http://") {
            $this->aButtonHtml['WEBSITE'] = "<a class=\"btn btn-primary\" href=\"".$this->oProfile->GetUrl()."\" target=\"_new\" onclick=\"javascript: hit('/outgoing/".$this->oProfile->GetUrlName()."/www');\" title=\"Visit Website\" target=\"_blank\">Visit Website</a>";
        }
        
        if (strlen($this->oProfile->GetApplyUrl()) > 1) {
            $this->aButtonHtml['APPLY'] = "<a class=\"btn btn-primary\"  href=\"".$this->oProfile->GetApplyUrl()."\" onclick=\"javascript: hit('/outgoing/".$this->oProfile->GetUrlName()."/www');\" title=\"Apply Online\" target=\"_blank\">Apply Online</a>";
        }
        if (strlen(trim($this->oProfile->GetEmail())) > 1) {
            if ($this->oProfile->HasEnquiryOption(ENQUIRY_BOOKING) && (strlen($this->oProfile->GetApplyUrl()) < 1)) {
                
                $this->aButtonHtml['BOOKING'] = "<a class=\"btn btn-primary\"  href=\"".Enquiry::GetRequestUrl('BOOKING',$this->oProfile->GetId(),PROFILE_COMPANY)."\" title=\"Booking Enquiry\">Booking Enquiry</a>";
                
            }
            if ($this->oProfile->HasEnquiryOption(ENQUIRY_GENERAL) && !$this->oProfile->HasEnquiryOption(ENQUIRY_BOOKING)) {
                $this->aButtonHtml['ENQUIRY'] = "<a class=\"btn btn-primary\"  href=\"".Enquiry::GetRequestUrl('GENERAL',$this->oProfile->GetId(),PROFILE_COMPANY)."\" title=\"Make an Enquiry\">Enquiry</a>";
            }
            if ($this->oProfile->HasEnquiryOption(ENQUIRY_JOB_APP)) {
                $this->aButtonHtml['JOB_APP'] = "<a class=\"btn btn-primary\"  href=\"".Enquiry::GetRequestUrl('JOB_APP',$this->oProfile->GetId(),PROFILE_COMPANY)."\" title=\"Apply Online\" target=\"_blank\">Apply</a>";
            }
        }
        if($this->oProfile->GetListingType() < BASIC_LISTING) {
            $this->aButtonHtml = array();
            $this->aButtonHtml['ENQUIRY'] = $this->aButtonHtml['ENQUIRY'] = "<a class=\"btn btn-primary\"  href=\"".Enquiry::GetRequestUrl('GENERAL',$this->oProfile->GetId(),PROFILE_COMPANY)."\" title=\"Make an Enquiry\">Enquiry</a>";;
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
