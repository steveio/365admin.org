<?php

class TemplateList {
    
    public $id;
    public $title;
    public $filename;
    public $desc_short;
    
    public $arrTemplate = array();

    public function __Construct($id = null, $title = null, $filename = null, $desc_short = null) 
    {
        $this->id = $id;
        $this->title = $title;
        $this->filename = $filename;
        $this->desc_short = $desc_short;
    }

    public function GetTemplateList()
    {
        return $this->arrTemplate;
    }

    public function GetFromDB()
    {
        global $db;
        
        $db->query("SELECT * FROM template ORDER BY id ASC;");

        $aResult = $db->getRows();
        
        
        foreach($aResult as $aRow)
        {
            $oTemplate = new TemplateList($aRow['id'],$aRow['title'],$aRow['filename'],$aRow['desc_short']);
            
            $this->arrTemplate[$aRow['id']] = $oTemplate;
        }
    }
}
    