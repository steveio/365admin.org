<?php

/**
 * Placement Profile Content Assembler 
 * 
 * Handles content provisioning & render of placement profile pages  
 *
 */

class PlacementProfileContentAssembler extends ProfileContentAssembler {
  
    protected $oProfile;  // instance of ProfilePlacement
    protected $oCompanyProfile;  // instance of ProfileCompany

    protected $oTemplate;  // instance of template
    protected $oReviewTemplate;  // instance of reviews / comments template

    protected $aEnquiryButtonHtml = array();

    public function __Construct() 
    {
        parent::__construct();

        $this->SetType(PROFILE_PLACEMENT);
        $this->SetLinkTo(CONTENT_TYPE_PLACEMENT);

        $this->oProfile = new PlacementProfile();
        $this->oCompanyProfile = new CompanyProfile();
        $this->oTemplate = new Template();
        $this->oReviewTemplate = new Template();

    }

    public function GetById($id)
    {
    }

    /*
     * Get By Path
     * 
     * Resolves profile based on URL segment <placement-name>  eg. /company/<company-name>/<placement-name>
     * 
     */
    public function GetByPath($path, $website_id = 0)
    {

        global $db, $oHeader, $oFooter;

        try {
            
            if (strlen($path) < 1) throw new Exception("View placement: invalid path: ".$path);

            die(__CLASS__."::".__METHOD__);

            print $oHeader->Render();
            print $this->oTemplate->Render();
            print $oFooter->Render();

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

}
