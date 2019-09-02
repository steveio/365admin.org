<?php

/*
 * Option encapsulates lists of profile options 
 * 
 * 
 */



class Option {

	private $id;
	private $name;
	private $type;

	public function __Construct($id,$name,$type) {
		
		$this->id = $id;
		$this->name = $name;
		$this->type = $type;
		
	}
	
	
	public function GetId() {
		return $this->id;
	}

	public function GetName() {
		return $this->name;
	}

	public function GetType() {
		return $this->type;
	}
	
	public function GetCheckboxHtml($prefix = "opt_",$checked = false,$disabled_flag = false) {

		$key = $prefix.$this->GetId();
		
		$checked = ($checked) ? "checked" : "";
		$disabled = ($disabled_flag) ? "disabled" : "" ;
		
		return "<label for=\"".$key."\" class=\"checkbox_label\"><input type=\"checkbox\" class=\"option_checkbox\" name=\"".$key."\" ".$disabled."  ".$checked." >".$this->GetName()."</label>";
		
	}
}


class OptionGroup {
	
	private $aOption;
	
	public function GetAll() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;
		
		$sql = "SELECT id,name,type FROM option ORDER BY type,sort_order ASC";
		
		$db->query($sql);
		
		$aTmp = array();
		
		if ($db->getNumRows() >= 1) {
			$aTmp = $db->getObjects();				
		}
		
		foreach($aTmp as $o) {
			$this->aOption[$o->type][$o->id] = new Option($o->id,$o->name,$o->type); 
		}
				
	}
	
	public function GetByPlacementId($id,$prefix) {

		global $db;
		
		if (!is_numeric($id)) return array();

		$db->query("SELECT option_id as id FROM prod_opt_map WHERE prod_id =".$id);

		$aId = array();
		if ($db->getNumRows() >= 1) {
			$aTmp = $db->getRows();
			foreach($aTmp as $a) {
				$aId[$prefix.$a['id']] = "on";
			}
		}

		return $aId;
	}
	
	
	public function GetHtml($aChecked,$disabled = false) {

		$sHtml = "";

		$aGroup = array("ACCOM" => "Accomodation","MEALS" => "Meals","TRAVEL" => "Travel");
		
		foreach($aGroup as $code => $label) {

			
			$sHtml .= "<div class='info-panel'>\n";
			$sHtml .= "<div class='rounded-outer'>\n";
			$sHtml .= "<b class=\"rtop blue\"><b class=\"r1\"></b> <b class=\"r2\"></b> <b class=\"r3\"></b> <b class=\"r4\"></b></b>";
			$sHtml .= "<div class='rounded-hdr blue'><div class='pad-l'><h2>".$label."</h2></div></div>";
			$sHtml .= "<div class='rounded-inner'>";
			$sHtml .= "<div class='col one-sm pad'>\n";						
			foreach($this->aOption[$code] as $oOption) {

				$sHtml .= "<div>\n";
				
				$prefix = "opt_";
				$key = $prefix.$oOption->GetId();
				
				$checked = array_key_exists($key,$aChecked) ? "checked" : "";				
				$sHtml .= $oOption->GetCheckboxHtml($prefix,$checked, $disabled);
				
				
				$sHtml .= "</div>";

			}
			$sHtml .= "</div>\n";
			$sHtml .= "</div>\n";
			$sHtml .= "</div>\n";
			$sHtml .= "</div>\n";

		}
			
		return $sHtml;
	}

	
}

?>