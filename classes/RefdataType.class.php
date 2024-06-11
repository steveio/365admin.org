<?php

class RefdataType
{

    private $id;
    private $name;
    private $description;
    
    public function __construct()
    {
        
    }
    
    public function GetId()
    {
        return $this->id;
    }

    public function SetId($id)
    {
        $this->id = $id;
    }
    
    public function GetName()
    {
        return $this->name;
    }

    public function SetName($name)
    {
        $this->name = $name;
    }

    public function GetDescription()
    {
        return $this->description;
    }

    public function SetDescription($description)
    {
        $this->description = $description;
    }
    
    public function GetAll()
    {
        global $db;
        
        $sql = "SELECT id,name ||' ('||description||')' as name from refdata_type ORDER BY id ASC";
        
        $db->query($sql);
        
        $result = array();
        
        if ($db->getNumRows() >= 1) {
            
            foreach($db->getRows() as $row) {
                $result[$row['id']] = $row['name'];
            }
        }
        
        $this->aValues = $result;
        
        return $this->aValues;
    }
}