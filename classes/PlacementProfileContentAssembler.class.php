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

    protected $aEnquiryUrl = array();

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

    /*
     * Get By Path
     * 
     * Resolves profile based on placement URL segment <placement-name>  eg. /company/<company-name>/<placement-name>
     * 
     */
    public function GetByPath($path, $website_id = 0)
    {

        global $db, $oHeader, $oFooter;

        try {
            
            parent::GetByUrlName($path);            
            parent::SetPageHeader();

            if (is_object($this->oProfile->GetCompanyLogo()))
            {
                $this->oTemplate->Set('logo_img',$this->oProfile->GetCompanyLogo()->GetHtml(""));
                $this->oTemplate->Set('logo_url_sm',$this->oProfile->GetCompanyLogo()->GetUrl("_sm"));
            }

            $this->GetEnquiryButtonHtml();
            
            $this->GetRelatedProfile($this->oProfile->GetOid(), PROFILE_PLACEMENT, "", $limit = 4);
            $this->GetRelatedArticle($this->oProfile->GetOid(), $limit = 6); 

            
            $this->oTemplate->Set("aEnquiryButtonHtml", $this->aEnquiryButtonHtml);
            $this->oTemplate->Set("oProfile",$this->oProfile);
            $this->oTemplate->Set("oReviewTemplate",$this->oReviewTemplate);
            $this->oTemplate->Set("aRelatedArticle", $this->aRelatedArticle);
            $this->oTemplate->Set("aRelatedProfile", $this->aRelatedProfile);
            
            $this->oTemplate->LoadTemplate("profile_placement_view.php");

            
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
        $this->aPlacement = PlacementProfile::Get("COMPANY_ID",$this->oProfile->GetId(), $filter_from_search = false);
    }

    public function GetEnquiryUrl()
    {
        return $this->aEnquiryUrl;
    }

    public function GetEnquiryButtonHtml()
    {
        $aEnquiryUrl['BOOKING'] = Enquiry::GetRequestUrl('BOOKING',$this->oProfile->GetId(),PROFILE_PLACEMENT);
        $aEnquiryUrl['GENERAL'] = Enquiry::GetRequestUrl('GENERAL',$this->oProfile->GetId(),PROFILE_PLACEMENT);
        $aEnquiryUrl['BROCHURE'] = Enquiry::GetRequestUrl('BROCHURE',$this->oProfile->GetId(),PROFILE_PLACEMENT);
        $aEnquiryUrl['JOB_APP'] = Enquiry::GetRequestUrl('JOB_APP',$this->oProfile->GetId(),PROFILE_PLACEMENT);

        $oCompanyProfile = new CompanyProfile();
        $oCompanyProfile->SetEnquiryOptionBitmap($this->oProfile->GetCompEnqOpt());

        $cssClass = "btn btn-primary rounded-pill px-3";

        if (in_array($this->oProfile->GetProfileType(), array(PROFILE_VOLUNTEER,PROFILE_TOUR))) {

            /* is this enquiry type enabled / disabled on the company profile? */
            if ($oCompanyProfile->HasEnquiryOption(ENQUIRY_BOOKING)) 
            {
                /* if apply/booking url is specified, button should redirect to external site */
                if (strlen($this->oProfile->GetApplyUrl()) > 1 && $this->oProfile->GetFilterFromSearch() != true) 
                {
                    /* button links to external apply/booking page */
                    $this->aEnquiryButtonHtml['APPLY'] = "<a class=\"".$cssClass."\" href=\"#\" onclick=\"javascript: goExternal('". $this->oProfile->GetApplyUrl() ."');\" title=\"Apply Online\" >Apply Online</a>";
                } else {
                    /* use our apply/booking enquiry form */
                    $this->aEnquiryButtonHtml['APPLY'] = "<a class=\"".$cssClass."\" href=\"". $aEnquiryUrl['BOOKING']."\" title=\"Book this placement\">Booking Enquiry</a>";
                }
            } else {
                $this->aEnquiryButtonHtml['APPLY'] = "<a class=\"".$cssClass."\" href=\"". $aEnquiryUrl['GENERAL']."\" title=\"Book this placement\">Make an Enquiry</a>";
            }
		} 


    	if (in_array($this->oProfile->GetProfileType(), array(PROFILE_JOB))) 
    	{
    	    if ($oCompanyProfile->HasEnquiryOption(ENQUIRY_JOB_APP)) 
    	    {
    	        if (strlen($this->oProfile->GetApplyUrl()) > 1 && $this->oProfile->GetFilterFromSearch() != true) 
    	        {
    				/* button links to external apply page */
    	            $this->aEnquiryButtonHtml['APPLY'] = "<a class=\"".$cssClass."\" target=\"_blank\"  href=\"". $this->oProfile->GetApplyUrl()."\" onclick=\"javascript: goExternal('".$this->oProfile->GetApplyUrl()."');\" title=\"Apply Online\">Apply Online</a>";
    			} else {				
    			    $this->aEnquiryButtonHtml['APPLY'] = "<a class=\"".$cssClass."\"  href=\"". $aEnquiryUrl['JOB_APP'] ."\" title=\"Apply Online\" target=\"_blank\">Apply Online</a>";			
    			}
    		}
    	}

    	if (strlen($this->oProfile->GetUrl()) > 1 && $this->oProfile->GetUrl() != "http://" && $this->oProfile->GetFilterFromSearch() != true) 
    	{
    	    $this->aEnquiryButtonHtml['WEBSITE'] = "<a class=\"".$cssClass."\" href=\"#\" onclick=\"javascript: goExternal('". $this->oProfile->GetUrl()."');\">Visit Website</a>";
        }
	}

	public function ProcessPlacementList()
	{
	    // @todo - placement list - view all placement for company /company/<comp-name>/placements
	    die("DISPLAY PLACEMENT LIST");
	    
	}
	
}
