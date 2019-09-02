<?php



class Mapping {

	
	/*
	 * 
	 * @param bool Admin user account required?
	 * @param string mapping table name to update
	 * @param string key 
	 * 
	 */
	public static function Update($bAdminRequired,$sTbl,$sKey,$iId,$aFormValues,$sFormKeyPrefix,$sKey2) {

		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."()");
		
		global $db,$oAuth;

		if (!is_numeric($iId)) return false;
		if (!is_array($aFormValues)) return false;
		
		if ($bAdminRequired) {
			if (!$oAuth->oUser->isAdmin) {
				return false;
			}
		}
		
		$res = $db->query("DELETE FROM ".$sTbl." WHERE ".$sKey." = ".$iId);
		if (!$res) return false; 
		
		// insert new mappings....
		foreach ($aFormValues as $k => $v) {
			
			//if (DEBUG) Logger::Msg($sFormKeyPrefix.":::".$k.":::".$v);
			
			switch(true) {				
				case preg_match("/".$sFormKeyPrefix."/",$k) :
						$iId2 = (int) substr($k,4,5);

						//if (DEBUG) Logger::Msg($iId2);
						
						if ((is_numeric($iId2)) && ($v == "on")) {
							$res = $db->query("INSERT INTO ".$sTbl." ($sKey,".$sKey2.") VALUES ($iId,$iId2);");
							if (!$res) return false;
						}
					break;
			}
		}
		return true;
	}

	public static function GetFromRequest($aRequest,$mode = "KEYS") {
		
		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."()");
		
		if (!is_array($aRequest)) return array();
		
		$aResult['cat'] = array();
		$aResult['act'] = array();
		$aResult['cty'] = array();
		$aResult['ctn'] = array();
		
		foreach($aRequest as $k => $v) {

				switch(true) {
					case preg_match("/cat_/",$k) :
						if ($mode == "KEYS") {
							if ($v == "on") $aResult['cat'][] = preg_replace("/cat_/","",$k);
						}
						break;
					case preg_match("/act_/",$k) :
						if ($mode == "KEYS") {
							if ($v == "on") $aResult['act'][] = preg_replace("/act_/","",$k);
						}
						break;
					case preg_match("/cty_/",$k) :
						if ($mode == "KEYS") {
							if ($v == "on") $aResult['cty'][] = preg_replace("/cty_/","",$k);
						}
						break;
					case preg_match("/ctn_/",$k) :
						if ($mode == "KEYS") {
							if ($v == "on") $aResult['ctn'][] = preg_replace("/ctn_/","",$k);
						}
						break;
						
				}
		}
		
		return $aResult;
	}

	/*
	 * @NOTE - not sure this was ever finished or if it is used?
	 * 			looks like it should have been fully generalised
	 * 			use GetByKey() below instead			
	 * 
	 * 
	 * 
	 * 
	 */
	
	public static function GetFromRequestByKey($aRequest,$sKey) {
		
		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."()");
		
		if (!is_array($aRequest)) return array();
		
		$aResult = array();
		
		foreach($aRequest as $k => $v) {

				switch(true) {
					case preg_match("/".$sKey."/",$k) :
						if ($mode == "KEYS") {
							if ($v == "on") $aResult['cat'][] = preg_replace("/cat_/","",$k);
						}
						break;
						
				}
		}
		
		return $aResult;
	}
	
	public static function GetIdByKey($a,$key) {

		if (!is_array($a)) return false;
		
		$aOut = array();
		
		foreach($a as $k => $v) {
			if (preg_match("/".$key."/",$k)) {
				$id = preg_replace("/".$key."/","",$k);
				if (is_numeric($id)) {
					$aOut[] = (int) $id;
				}
			}
		}		

		return $aOut;
	}
	
	
	/*
	 * Given a key prefix and an array, create an 8bit bitmap based on submitted values
	 * 
	 * eg 
	 * $sKey = "prof_opt_";
	 * $aInput = array ("prof_opt_1" => "on","prof_opt_3" => "on"); 
	 *  
	 * return $sBitmap = "10100000";
	 * 
	 */
	public static function GetBitmapFromRequest($sKey,$aInput,$default = 0) {

		$aOut = array();
		
		/* initialise the bitmap */
		$sVal = ($default == 1) ? "1" : "0";
		for($i=1;$i<8;$i++) {
			$aOut[$i] = $sVal; 	
		}
 
		foreach($aInput as $k => $v) {
			//Logger::Msg($k);
			$aBits = explode("_",$k);
			if (isset($aBits[2])) {
				$idx = $aBits[2];
			} else {
				continue;
			}
			if (preg_match("/".$sKey."/",$k)) {
				$aOut[$idx] = ($v == "on") ? "1" : "0";  
			}
		}
		
		return implode("",$aOut);
	}

	/*
	 * Update mappings to refdata_map
	 * 
	 * @param string mapping table name to update (defaults to refdata_map)
	 * @param int object type refdata is linked to (0 = company profile, 1 = placement )
	 * @param int linked object id
	 * @param int refdata type id PK from table refdata_type
	 * @param array ids from db table refdata that will be mapped to object
	 * 
	 * @return TRUE on success, FALSE on failure
	 */
	public static function UpdateRefData($sTbl = "refdata_map",$link_to, $link_id, $refdata_type_id, $aRefdataId = array()) {

		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."() link_to: ".$link_to.", link_id: ".$link_id.", refdata_id: ".implode(",",$aRefdataId));
		
		global $db;

		if (!is_numeric($link_to)) return false;
		if (!is_numeric($link_id)) return false;
		if (!is_numeric($refdata_type_id)) return false;

		// delete existing mappings of refdata_type for object		
		$res = $db->query("DELETE FROM ".$sTbl." WHERE ".$link_to." = ".$link_to." AND link_id = ".$link_id." AND refdata_type = ".$refdata_type_id);
		if (!$res) return FALSE; 
		
		// insert new mappings....
		foreach ($aRefdataId as $id) {
			
			if (is_numeric($id)) {
				$res = $db->query("INSERT INTO ".$sTbl." (link_to,link_id,refdata_type,refdata_id) VALUES ($link_to,$link_id,$refdata_type_id,$id);");
				if (!$res) return FALSE;
			}
			
		}
		return TRUE;
	}
	
}



?>