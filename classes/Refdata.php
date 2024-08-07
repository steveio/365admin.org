<?php



/*
 * Refdata system is a key => value store for abitrary lists
 * 
 * 
 */


/* refdata type mappings
 */
define('REFDATA_US_STATE',0);
define('REFDATA_CAMP_TYPE',1);
define('REFDATA_CAMP_JOB_TYPE',2);
define('REFDATA_ACTIVITY',3);
define('REFDATA_INT_RANGE',4);
define('REFDATA_DURATION',5);
define('REFDATA_ORG_SUBTYPE',6);
define('REFDATA_BONDING',7);
define('REFDATA_STAFF_ORIGIN',8);
define('REFDATA_GENDER',9);
define('REFDATA_APPROX_COST',10);
define('REFDATA_HABITATS',11);
define('REFDATA_SPECIES',12);
define('REFDATA_ACCOMODATION',13);
define('REFDATA_MEALS',14);
define('REFDATA_TRAVEL_TRANSPORT',15);
define('REFDATA_ADVENTURE_SPORTS',16);
define('REFDATA_ORG_PROJECT_TYPE',17);
define('REFDATA_CURRENCY',18);
define('REFDATA_JOB_OPTIONS',19);
define('REFDATA_INT_SMALL_RANGE',20);
define('REFDATA_JOB_CONTRACT_TYPE',21);
define('REFDATA_US_REGION',22);
define('REFDATA_AGE_RANGE',23);
define('REFDATA_RELIGION',24);
define('REFDATA_CAMP_GENDER',25);
define('REFDATA_LANGUAGES',26);
define('REFDATA_COURSE_TYPE',27);
define('REFDATA_COURSES',28);
define('REFDATA_ARTICLE_TYPE',29);

/* multiple choice refdata form element prefixes */
define('REFDATA_ACTIVITY_PREFIX','CA_');
define('REFDATA_CAMP_TYPE_PREFIX','CT_');
define('REFDATA_CAMP_JOB_TYPE_PREFIX','JT_');
define('REFDATA_SPECIES_PREFIX','SP_');
define('REFDATA_HABITATS_PREFIX','HA_');
define('REFDATA_TRAVEL_TRANSPORT_PREFIX','TT_');
define('REFDATA_ACCOMODATION_PREFIX','AC_');
define('REFDATA_MEALS_PREFIX','ML_');
define('REFDATA_JOB_OPTIONS_PREFIX','JO_');
define('REFDATA_LANGUAGES_PREFIX','LA_');
define('REFDATA_COURSE_TYPE_PREFIX','CR_');
define('REFDATA_COURSES_PREFIX','CS_');

define('REFDATA_OPTION_CHECKBOXES_DISABLED','CHECKBOXES_DISABLED');


class Refdata {
	
	private $type_id;
	
	private $id;
	private $name;
	private $desc;

	private $css_class;
	
	private $aValues;
	private $aOptions; // a key/value set of options caller can set to affect output behaviour eg disabled checkboxes
	
	private $order_by_sql;
	private $limit_sql;
	private $bDisplaySelectedOnly; // return only selected options
	
	public function __Construct($type_id) {
		
		$this->aValues = array();
		$this->aOptions = array();
		
		$this->type_id = $type_id;
		
		$this->GetRefdataTypeById($type_id);
		
		$this->order_by_sql = " value ASC";
		$this->limit_sql = '';
		$this->bDisplaySelectedOnly = false;
	}

	public function SetId($id)
	{
	    if (is_numeric($id)) {
	        $this->id = $id;	    
	    }
	}

	public function SetName($name)
	{
	    $this->name = $name;
	}

	public function SetDesc($desc)
	{
	    $this->desc = $desc;
	}
	
	public function SetType($type)
	{
	    $this->type_id = $type;
	}

	public function Insert()
	{
	    global $db;
	    
	    $db->query("SELECT 1 from refdata_type WHERE id = ".$this->type_id);
	    
	    if ($db->getAffectedRows() != 1)
	    {
	        return false;
	    }

	    $insert_id = $db->getFirstCell("SELECT max(id)+1 from refdata");
	    
	    if (!is_numeric($insert_id)) return false;

	    $sql = "INSERT INTO refdata (id,value,type) VALUES (".$insert_id.",'".addslashes($this->name)."',".$this->type_id.")";
	    
	    $db->query($sql);
	    
	    if ($db->getAffectedRows() == 1)
	    {
	        return true;
	    }
	}
	
	public function Update()
	{
        global $db;
        
        $db->query("SELECT 1 from refdata WHERE id = ".$this->id);

        if ($db->getAffectedRows() != 1)
        {
            return false;
        }

        $sql = "UPDATE refdata SET value='".addslashes($this->name)."' WHERE id = ".$this->id;
        
        $db->query($sql);
        
        if ($db->getAffectedRows() == 1)
        {
            return true;
        }
	}

	/**
	 * Singleton pattern, only a single instance of each refdata type object should be instantiated in memory
	 * 
	 * @param constant integer $cRefdataType
	 * @return object (Refdata) Refdata
	 */
	public static function GetInstance($cRefdataType)
	{

	    global $aRefdata;
	    
	    if (isset($aRefdata[$cRefdataType])) return $aRefdata[$cRefdataType];

	    $oRefdata = new Refdata($cRefdataType);

	    if (!isset($aRefdata)) $aRefdata = array();

	    $aRefdata[$cRefdataType] = $oRefdata;

	    return $oRefdata;
	}

	public function GetValues() {
		return $this->aValues;
	}
	
	public function SetElementId($id) {
		$this->id = $id;
	}
	
	public function SetElementName($name) {
		$this->name = $name;
	}
	
	public function SetElementCssClass($css_class) {
		$this->css_class = $css_class;
	}
	
	public function SetOrderBySql($sql) {
		$this->order_by_sql = $sql;
	}
	
	public function SetLimitSQL($iLimit) { // only fetch $iLimit rows 
		$this->limit_sql = " LIMIT ".$iLimit;
	}
	
	public function SetOption($key,$value) {
		$this->aOptions[$key] = $value;
	}  
	
	public function GetOption($key) {
		if (array_key_exists($key, $this->aOptions)) {
			return $this->aOptions[$key];
		}
	}

	public function GetRefdataTypeById($id) {
	    
	    global $db;
	    
	    if (!is_numeric($id)) return false;
	    
	    $sql = "SELECT id,name,description FROM refdata_type WHERE id = ".$id;
	    
	    $db->query($sql);
	    
	    if ($db->getNumRows() == 1) {
	        $aRow = $db->getRow();
	        $this->SetId($id);
	        $this->SetName($aRow['name']);
	        $this->SetDesc($aRow['description']);
	        
	    }
	    
	    return false;
	}
	
	public function GetById($id) {
	    
	    global $db;
	    
	    if (!is_numeric($id)) return false;
	    
	    $sql = "SELECT id,value FROM refdata WHERE id = ".$id;
	    
	    $db->query($sql);
	    
	    if ($db->getNumRows() == 1) {
	        return $db->getRow();    
	    }
	    
	    return false;
	}
	
	/* return all refdata values of a specific type */
	public function GetByType() {
		
		global $db;
		
		$sql = "SELECT id,value FROM refdata WHERE type = ".$this->type_id." ORDER BY ".$this->order_by_sql . $this->limit_sql;

		$db->query($sql);

		$result = array();
		
		if ($db->getNumRows() >= 1) {

			foreach($db->getRows() as $row) {
				$result[$row['id']] = $row['value'];	
			}
		}
		
		$this->aValues = $result;
		
		return $this->aValues;
				
	}
	
	public function GetValueById($key) {
		if (isset($this->aValues[$key])) {
			return $this->aValues[$key];
		}
	}

	/* return refdata id's mapped to an object */ 
	public static function Get($refdata_type, $link_to, $link_id, $labels = FALSE) {

		if (DEBUG) Logger::Msg(get_class($this)."->".__FUNCTION__."() refdata_type: ".$refdata_type.", link_to: ".$link_to.", link_id: ".$link_id );
		
		global $db;
		
		if (!is_numeric($link_to) || 
			!is_numeric($link_id) || 
			!is_numeric($refdata_type)) 
		{
			return array();		
		}

		if ($labels) {
			$sql = "SELECT m.refdata_id, r.value as label FROM refdata_map m, refdata r WHERE m.refdata_id = r.id AND m.link_to = ".$link_to." AND m.link_id = ".$link_id." AND m.refdata_type = ".$refdata_type ." ORDER BY r.value asc";
		} else {
			$sql = "SELECT refdata_id FROM refdata_map WHERE link_to = ".$link_to." AND link_id = ".$link_id." AND refdata_type = ".$refdata_type;
		}

		$db->query($sql);
		
		if ($db->getNumRows() >= 1) {
			if ($labels) {
				$result = array();
				foreach($db->getRows() as $row) {
					$result[$row['refdata_id']] = $row['label'];
				}
				return $result;
			} else {
				return $db->getRowsNum();
			}
		} else {
			return array();
		}
		
		
	}
	
	
	public function GetDDlist($selected_id, $no_default = FALSE, $css_class = 'form-select') {

		$aValues = $this->GetByType();
		
		$oSelect = new Select($this->id,$this->name,$css_class,$aValues,$bKeysSameAsValues = false,$selected_id);
		
		if ($no_default) {
			$oSelect->SetNoDefault();
		}
		
		return $oSelect->GetHtml();
	}

	public function SetDisplaySelectedOnly($bool)
	{
	    $this->bDisplaySelectedOnly = $bool;
	}
	
	public function GetCheckboxList($prefix, $aSelected, $input_css = "select_list", $ul_css = "select_list", $li_css = "select_list_element", $label_css = "select_list") {
		
		$aValues = $this->GetByType();
		
		$aElements = array();
		
		foreach($aValues as $id => $value) {
			if (is_array($aSelected)) {
				$checked = (in_array($id,$aSelected)) ? "checked" : "";
			} else {
				$checked = '';
			}
 
			$disabled = ($this->GetOption(REFDATA_OPTION_CHECKBOXES_DISABLED) == TRUE) ? "disabled" : "" ;

			if ($this->bDisplaySelectedOnly && $checked == "checked")
			{			
                $aElements[] = "<li class='".$li_css."'><input class='".$input_css."' type='checkbox' name='".$prefix . $id."' $checked  $disabled /> <label class='".$label_css."'>".$value ."</label></li>\n";
			} elseif (!$this->bDisplaySelectedOnly) {
			    $aElements[] = "<li class='".$li_css."'><input class='".$input_css."' type='checkbox' name='".$prefix . $id."' $checked  $disabled /> <label class='".$label_css."'>".$value ."</label></li>\n";
			}
		}

		return $aElements;
	}
	
	public function GetLabelsFromSelectedIds($aSelected, $li_css = "select_list_element") {
		
		$aValues = $this->GetByType();
		
		//Logger::Msg($aSelected);
		//Logger::Msg($aValues);
		
		$aElements = array();
		
		foreach($aValues as $id => $value) {
			if (in_array($id,$aSelected)) {
				$aElements[] = "<li>".$value."</li>";
			}
		}
		return $aElements;
	}
	
}
