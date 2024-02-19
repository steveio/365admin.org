<?php

class TemplateList {
    
    public $id;
    public $title;
    public $filename;
    public $desc_short;
    public $fetch_mode;
    public $fetch_limit;
    public $is_collection;
    
    public $arrTemplate;

    public function __Construct($id = null, $title = null, $filename = null, $desc_short = null, $fetch_mode = null, $fetch_limit = null, $is_collection = null) 
    {
        $this->id = $id;
        $this->title = $title;
        $this->filename = $filename;
        $this->desc_short = $desc_short;
        $this->fetch_mode = $fetch_mode;
        $this->fetch_limit = $fetch_limit;
        $this->is_collection = ($is_collection == "t") ? true : false;

        $this->arrTemplate = array();
    }

    public function GetById($id)
    {
        if (isset($this->arrTemplate[$id]) && is_object($this->arrTemplate[$id]))
        {
            return $this->arrTemplate[$id];
        } else {
            throw new Exception("Invalid template id :".$id);
        }
    }

    public function GetFilenameById($id)
    {
        return $this->arrTemplate[$id]->filename;
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
            $oTemplate = new TemplateList($aRow['id'],$aRow['title'],$aRow['filename'],$aRow['desc_short'],$aRow['fetch_mode'],$aRow['fetch_limit'],$aRow['is_collection']);

            $this->arrTemplate[$aRow['id']] = $oTemplate;
        }
    }
}