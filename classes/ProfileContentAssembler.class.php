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
    
    public function __Construct() 
    {
        parent::__construct();
    }

    
    public function GetById($id) {}
    
    public function GetByPath($path, $website_id = 0) {}

    public function GetType() {
        return $this->profile_type;
    }
    
    public function SetType($iType) {
        $this->profile_type = $iType;
    }

}