<?php

/**
 * Search Result Content Assembler 
 * 
 * Handles content provision and template render for pages displaying search results:
 * 
 * 		eg. /country/<country-name>
 * 			/continent/<continent-name>
 * 			/<activity-name>
 * 			/<category-name>
 * 			/search/<search-phrase>
 *
 *
 */

class SearchResultContentAssembler extends AbstractContentAssembler {

    public function __Construct() 
    {
        parent::__construct();
    }

    public function GetById($id) {}
    
    
    public function GetByPath($path, $website_id = 0)
    {

        global $oHeader, $oFooter;

        try {

            $oJsInclude = new JsInclude();
            $oJsInclude->SetSrc("/includes/js/autocomplete/jquery-ui.min.js");
            $oHeader->SetJsInclude($oJsInclude);

            $oJsInclude = new JsInclude();
            $oJsInclude->SetSrc("/includes/js/search_panel.js");
            $oHeader->SetJsInclude($oJsInclude);
            
            $oHeader->Reload();
            
            
            $oSearchPanel = new Template();
            $oSearchPanel->Set('ACTIVITY_LIST',Activity::getActivitySelectList());

            $oSearchPanel->LoadTemplate("search_panel.php");

            print $oHeader->Render();
            print $oSearchPanel->Render();
            print $oFooter->Render();

            
            die(__FILE__." :: ".__LINE__);

        } catch (Exception $e) {
            throw $e;
        }
    }

}