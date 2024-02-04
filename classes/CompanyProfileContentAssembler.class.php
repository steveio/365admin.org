<?php

/**
 * Profile Content Assembler 
 * 
 * Base class for fetching profile (company | placement) data & template rendering 
 * 
 * Contains functionality company to all profile types
 *
 */

class CompanyProfileContentAssembler extends ProfileContentAssembler {
  
    public function __Construct() 
    {
        parent::__construct();
    }

    public function GetById($id)
    {
    }
    
    
    public function GetByPath($path, $website_id = 0)
    {

        global $db, $oHeader;

        try {

            $oCompany = new Company($db);
            $aRes = $aCompany = $oCompany->GetByUrlName($path);

            if (!is_array($aRes))
            {
                throw new NotFoundException("Company Profile '".$path."' not found");
            }

            $title = htmlUtils::convertToPlainText($aRes['title']);
            $desc_short = htmlUtils::convertToPlainText($aRes['desc_short'],0,160);
            
            $oHeader->SetTitle($title);
            $oHeader->SetDesc($desc_short);
            $oHeader->SetKeywords("");
            
            // @deprecated
            $_REQUEST['page_title'] = $title;
            $_REQUEST['page_meta_description'] = $desc_short;
            $_REQUEST['page_keywords'] = "";

            print_r($aRes);
            die();

        } catch (Exception $e) {
            throw $e;
        }
 
    }

}