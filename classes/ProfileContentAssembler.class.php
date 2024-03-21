<?php

/**
 * Profile Content Assembler 
 * 
 * Base class for fetching Profile (Company | Placement) Data
 * 
 * Contains functionality common to assembling any profile type
 *
 */

class ProfileContentAssembler extends AbstractContentAssembler {

    protected $profile_type; /* constant integer indicating profile type */

    protected $aEnquiryButtonHtml = array();

    public function __Construct() 
    {
        parent::__construct();

        $this->LoadDependencies(); // include CSS / JS common to all view profile screens
    }

    public function SetPageHeader()
    {
        global $oHeader;

        $aKeywords = $this->GetKeywords($this->oProfile->GetOid(),$this->profile_type);
        $oHeader->SetDesc($this->oProfile->GetDescShortPlaintext($trunc = 160));
        $oHeader->SetKeywords(implode(",",$aKeywords));
        
        $oHeader->Reload();
    }

    public function GetByPath($path, $website_id = 0) {}
    
    public function GetById($id) {}

    /*
     * Handle fetch (data provision) common to all profile types
     */
    public function GetByUrlName($path, $website_id = 0) 
    {

        global $db, $oHeader, $oFooter;

        if (strlen($path) < 1) throw new Exception("View profile: invalid path".$path);
        
        // pre-fetch to validate resource exists
        $aRes = $this->oProfile->GetByUrlName($path);

        if (!is_array($aRes) || !is_numeric($aRes['id']))
        {
            throw new NotFoundException("Profile ".$this->oProfile->GetTypeLabel()." '".$path."' not found");
        }
        
        // setup page header / metadata
        $title = htmlUtils::convertToPlainText($aRes['title']);
        $desc_short = htmlUtils::convertToPlainText($aRes['desc_short'],0,160);
        
        $oHeader->SetTitle($title);
        $oHeader->SetDesc($desc_short);
        $oHeader->SetKeywords("");
        
        unset($this->oProfile);
        $this->oProfile = $oProfile = ProfileFactory::Get($aRes['type']);

        $this->oProfile->SetType($aRes['type']);

        
        // fetch full profile
        $this->oProfile->GetById($aRes['id']);

        
        if ($this->oProfile->GetId() != $aRes['id']) {
            throw new Exception("Profile not found  id:".$aRes['id'].", title: ".$aRes['title']." url_name: ".$path);
        }

        $this->SetLinkId($aRes['id']); // put profile id in scope of parent class for fetching associated content
        
        $this->GetReviews($this->oProfile->GetId(), $this->oProfile->GetTypeLabel(), $this->oProfile->GetTitle());

    }

    public function GetType() {
        return $this->profile_type;
    }
    
    public function SetType($iType) {
        $this->profile_type = $iType;
    }

    public function LoadDependencies()
    {
    }
}