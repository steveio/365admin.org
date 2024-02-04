<?php

/**
 * Abstract Content Assembler 
 * 
 * Interface for fetching page content (article | profile | activity | destination | search result) & provision template rendering 
 * 
 *
 */

define("CONTENT_TYPE_ARTICLE", "ARTICLE");
define("CONTENT_TYPE_CATEGORY", "CATEGORY");
define("CONTENT_TYPE_ACTVITY", "ACTIVITY");
define("CONTENT_TYPE_COUNTRY", "COUNTRY");
define("CONTENT_TYPE_COMPANY", "COMPANY");
define("CONTENT_TYPE_PLACEMENT", "PLACEMENT");


abstract class AbstractContentAssembler {
  
    private $oTemplateList;
    private $strTemplatePath;

    public function __Construct() 
    {
        $this->oTemplateList = new TemplateList();
        $this->oTemplateList->GetFromDB();
    }

    public function SetTemplatePath($templatePath)
    {
        $this->strTemplatePath = $templatePath;
    }

    abstract public function GetById($id);

    abstract public function GetByPath($path, $website_id = 0);

}