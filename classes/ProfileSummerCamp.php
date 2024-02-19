<?php



class SummerCampProfile extends CompanyProfile {
	
	const PROFILE_TABLE_NAME = "profile_summercamp";
	const PROFILE_LINK_TO_STR = "COMPANY"; // some of the old mapping tables (eg img_map) use string COMPANY as link_to
	const PROFILE_LINK_TO_ID = PROFILE_SUMMERCAMP; // newer mapping tables eg refdata use profile_type_id as link to
	
	/* profile specific fields */
	protected $duration_from_id; // int
	protected $duration_to_id; // int

	protected $price_from_id; // int
	protected $price_to_id; // int
	protected $currency_id; // int
	
	protected $camp_gender;
	protected $camp_gender_label;
	protected $camp_religion;
	protected $camp_religion_label;
	protected $camper_age_from_id; // int
	protected $camper_age_to_id; // int
	protected $camper_age_from_label;
	protected $camper_age_to_label;
	
	
	// @depreciated - staff fields removed to focus profile on camp / programs
	protected $no_staff;
	protected $staff_gender;
	protected $staff_gender_label;
	protected $staff_origin;
	protected $staff_origin_label;

	protected $season_dates;
	protected $requirements;
	protected $how_to_apply;

	protected $camp_activity_list;
	protected $camp_type_list;
	protected $camp_job_type_list;
	protected $camp_activity_labels;
	protected $camp_type_labels;
	protected $camp_job_type_labels;
	
	public function __Construct() {
		
		parent::__construct();
		
		$this->sTblName = self::PROFILE_TABLE_NAME;
		$this->SetLinkTo(self::PROFILE_LINK_TO_STR);

		$this->camp_activity_list = array();
		$this->camp_type_list = array();
		$this->camp_job_type_list = array();
		$this->camp_type_labels = array();	
		$this->camp_job_type_labels = array();
		$this->camp_activity_labels = array();
	}
	

	public function GetById($id,$return = "ARRAY") {

		if (!is_numeric($id)) return false;

		parent::__Construct();
		
		
		parent::SetSubTypeTable($this->GetSubTypeTable());
		parent::SetSubTypeFields($this->GetSubTypeFields());

		
		$oResult = parent::GetById($id, $return = "PROFILE");

		if (!$oResult) return FALSE;
		
		$this->SetCampActivityList();
		$this->SetCampTypeList();
		$this->SetCampJobTypeList();
		
		return $this;
	}
	

	private function GetSubTypeTable() {
		$sSubTypeTable = "LEFT OUTER JOIN ".$this->sTblName." c2 ON c.id = c2.company_id ";
		//$sSubTypeTable .= "LEFT OUTER JOIN refdata r ON c2.staff_gender = r.id ";
		//$sSubTypeTable .= "LEFT OUTER JOIN refdata r2 ON c2.staff_origin = r2.id ";
		return $sSubTypeTable; 
	}

	private function GetSubTypeFields() {

		return $sSubTypeFields = "
							,c2.duration_from_id
							,c2.duration_to_id
							,c2.price_from_id
							,c2.price_to_id
							,c2.currency_id
							,c2.season_dates
							,c2.requirements
							,c2.how_to_apply
							,c2.camp_gender
							,c2.camp_religion
							,c2.camper_age_from_id
							,c2.camper_age_to_id
							";
	}
	
	
	public function DoAddUpdate($c,&$aResponse,$bRedirect = false,$bApproved = true, $tx = TRUE) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		$db->query("BEGIN");
		
		/* add/update base company profile record */
		$result = parent::DoAddUpdate($c,$aResponse,$bRedirect = false,$bApproved = true,$bTx = FALSE);
		
		 
		if (!$result) {
			$db->query("ROLLBACK");
			return FALSE;	
		} else {
			$c['id'] = $aResponse['id']; 	
		}

		// add slashes to extended profile fields
		Validation::AddSlashes($c);
		
		
		/* add/update extended profile subtype record */
		$result = $this->UpdateSubTypeRecord($c);
		
		
		if (!$result) {
			$aResponse = array();
			$aResponse['msg']['general_error'] = ERROR_COMPANY_PROFILE_EXTENDED_ERROR;
			$db->query("ROLLBACK");
			return FALSE;	
		}
		
		
		/* update summer camp specific refdata mappings */
		$aId = Mapping::GetIdByKey($c,REFDATA_ACTIVITY_PREFIX);
		Mapping::UpdateRefData("refdata_map",PROFILE_COMPANY,$c['id'],REFDATA_ACTIVITY, $aId);

		$aId = Mapping::GetIdByKey($c,REFDATA_CAMP_TYPE_PREFIX);
		Mapping::UpdateRefData("refdata_map",PROFILE_COMPANY,$c['id'],REFDATA_CAMP_TYPE, $aId);

		$aId = Mapping::GetIdByKey($c,REFDATA_CAMP_JOB_TYPE_PREFIX);
		Mapping::UpdateRefData("refdata_map",PROFILE_COMPANY,$c['id'],REFDATA_CAMP_JOB_TYPE, $aId);
				
		
		$db->query("COMMIT");

		
		return TRUE;
		
	}

	public function AddSubTypeRecord($p) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;

		if (!is_numeric($p['id'])) return false;
		if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_NO_STAFF])) $p[PROFILE_FIELD_SUMMERCAMP_NO_STAFF] = 'NULL';
        if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_NO_STAFF])) $p[PROFILE_FIELD_SUMMERCAMP_NO_STAFF] = 'NULL';
        if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER])) $p[PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER] = 'NULL';
        if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN])) $p[PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN] = 'NULL';
        if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER])) $p[PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER] = 'NULL';
        if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION])) $p[PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION] = 'NULL';
        if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_FROM])) $p[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_FROM] = 'NULL';
        if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_TO])) $p[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_TO] = 'NULL';
        

		$sql = "INSERT INTO ".self::PROFILE_TABLE_NAME ." (
					company_id
					,no_staff
					,staff_gender
					,staff_origin
					,season_dates
					,requirements
					,how_to_apply
					,duration_from_id
					,duration_to_id
            		,price_to_id 
            		,price_from_id 
            		,currency_id 
            		,camp_gender 
            		,camp_religion 
            		,camper_age_from_id 
            		,camper_age_to_id
				) VALUES (
					".$p['id']."
					,".$p[PROFILE_FIELD_SUMMERCAMP_NO_STAFF]."
					,".$p[PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER]."
					,".$p[PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN]."
					,'".$p[PROFILE_FIELD_SUMMERCAMP_SEASON_DATES]."'
					,'".$p[PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS]."'
					,'".$p[PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY]."'
					,".$p[PROFILE_FIELD_SUMMERCAMP_DURATION_FROM]."
					,".$p[PROFILE_FIELD_SUMMERCAMP_DURATION_TO]."
            		,".$p[PROFILE_FIELD_SUMMERCAMP_PRICE_TO]."
            		,".$p[PROFILE_FIELD_SUMMERCAMP_PRICE_FROM]."
            		,".$p[PROFILE_FIELD_SUMMERCAMP_CURRENCY]."
            		,".$p[PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER]."
            		,".$p[PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION]."
            		,".$p[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_FROM]."
            		,".$p[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_TO]."
				);";		

			
		$db->query($sql);
	
		if ($db->getAffectedRows() == 1) {
			return TRUE;
		}
		
	}
	
	
	public function UpdateSubTypeRecord($p) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;

		if (!is_numeric($p['id'])) return false;
                if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_NO_STAFF])) $p[PROFILE_FIELD_SUMMERCAMP_NO_STAFF] = 'NULL';
                if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER])) $p[PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER] = 'NULL';
                if (!is_numeric($p[PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN])) $p[PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN] = 'NULL';


		/* is there an existing sub-type record ? */
		if (!$db->getFirstCell("SELECT 1 FROM ".$this->sTblName." WHERE company_id = ".$p['id'])) {
			return $this->AddSubTypeRecord($p);
		}
		
		$sql = "UPDATE ".self::PROFILE_TABLE_NAME ." SET 
					 no_staff = ".$p[PROFILE_FIELD_SUMMERCAMP_NO_STAFF]."
					,staff_gender = ".$p[PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER]." 
					,staff_origin = ".$p[PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN]."
					,season_dates = '".addslashes($p[PROFILE_FIELD_SUMMERCAMP_SEASON_DATES])."'
					,requirements = '".addslashes($p[PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS])."'
					,how_to_apply = '".addslashes($p[PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY])."'
					,duration_from_id = ".$p[PROFILE_FIELD_SUMMERCAMP_DURATION_FROM]."
					,duration_to_id = ".$p[PROFILE_FIELD_SUMMERCAMP_DURATION_TO]."
                    ,price_to_id = ".$p[PROFILE_FIELD_SUMMERCAMP_PRICE_TO]."
                    ,price_from_id = ".$p[PROFILE_FIELD_SUMMERCAMP_PRICE_FROM]."
                    ,currency_id = ".$p[PROFILE_FIELD_SUMMERCAMP_CURRENCY]."
                    ,camp_gender = ".$p[PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER]."
                    ,camp_religion = ".$p[PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION]."
                    ,camper_age_from_id = ".$p[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_FROM]."
                    ,camper_age_to_id = ".$p[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_TO]."

				WHERE company_id = ".$p['id']." ;";

		$db->query($sql);
	
		if ($db->getAffectedRows() == 1) {
			return TRUE;
		}
		
	}
	
	/* inject type specific form values into _POST to pre-populate edit form 
	 * @todo - migrate back to CompanyStep class  
	 */ 
	public function SetTypeSpecificFormValues() {

		$_POST[PROFILE_FIELD_SUMMERCAMP_DURATION_FROM] = $this->GetDurationFromId();
		$_POST[PROFILE_FIELD_SUMMERCAMP_DURATION_TO] = $this->GetDurationToId();		
		$_POST[PROFILE_FIELD_SUMMERCAMP_NO_STAFF] = $this->GetNoStaff();
		$_POST[PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER] = $this->GetStaffGender();
		$_POST[PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN] = $this->GetStaffOrigin();
		$_POST[PROFILE_FIELD_SUMMERCAMP_SEASON_DATES] = $this->GetSeasonDates();
		$_POST[PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS] = $this->GetRequirements();
		$_POST[PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY] = $this->GetHowToApply();

		$_POST[PROFILE_FIELD_SUMMERCAMP_PRICE_TO] = $this->GetPriceToId();
		$_POST[PROFILE_FIELD_SUMMERCAMP_PRICE_FROM] = $this->GetPriceFromId();
		$_POST[PROFILE_FIELD_SUMMERCAMP_CURRENCY] = $this->GetCurrencyId();
		$_POST[PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER] = $this->GetCampGender();
		$_POST[PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION] = $this->GetCampReligion();
		$_POST[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_FROM] = $this->GetCamperAgeFromId();
		$_POST[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_TO] = $this->GetCamperAgeToId();

	}

	public function GetCampGender() {
		return $this->camp_gender;
	}
	
	public function GetCampGenderLabel() {
		if (!is_object($this->oCampGender)) {
			$this->oCampGender = new Refdata(REFDATA_CAMP_GENDER);
			$this->oCampGender->GetByType();
			$this->camp_gender_label = $this->oCampGender->GetValueById($this->camp_gender);
		}
		return $this->camp_gender_label;
	}	
	
	public function GetCampReligion() {
		return $this->camp_religion;
	}

	public function GetCampReligionLabel() {
		if (!is_object($this->oCampReligion)) {
			$this->oCampReligion = new Refdata(REFDATA_RELIGION);
			$this->oCampReligion->GetByType();
			$this->camp_religion_label = $this->oCampReligion->GetValueById($this->camp_religion);
		}
		return $this->camp_religion_label;
	}
	
	public function GetCamperAgeFromId() {
		return $this->camper_age_from_id;
	}
	
	public function GetCamperAgeToId() {
		return $this->camper_age_to_id;
	}
	
	public function GetCamperAgeFromLabel($type = "STRING") {
		
		if (!is_object($this->oCamperAgeFrom)) {
			$this->oCamperAgeFrom = new Refdata(REFDATA_AGE_RANGE);
			$this->oCamperAgeFrom->GetByType();
			$this->camper_age_from_label = $this->oCamperAgeFrom->GetValueById($this->camper_age_from_id);
		}
		if ($type == "INT") { // strip + < symbols to allow index range lookups
		    $this->camper_age_from_label = preg_replace("/[^0-9]/", "", $this->camper_age_from_label);
		}
		return $this->camper_age_from_label;
	}
	
	public function GetCamperAgeToLabel($type = "STRING") {
		if (!is_object($this->oCamperAgeTo)) {
			$this->oCamperAgeTo = new Refdata(REFDATA_AGE_RANGE);
			$this->oCamperAgeTo->GetByType();
			$this->camper_age_to_label = $this->oCamperAgeTo->GetValueById($this->camper_age_to_id);
		}
		if ($type == "INT") { // strip + < symbols to allow index range lookups
		    $this->camper_age_to_label = preg_replace("/[^0-9]/", "", $this->camper_age_to_label);
		}
		return $this->camper_age_to_label;
	}	
	
	public function GetStateId() {
		return $this->state_id;
	}

	public function GetNoStaff() {
		return $this->no_staff;
	}
	
	public function GetStaffGender() {
		return $this->staff_gender;
	} 

	public function GetStaffGenderLabel() {
		return $this->staff_gender_label;
	}
	
	public function GetStaffOrigin() {
		return $this->staff_origin;
	}
	
	public function GetStaffOriginLabel() {
		return $this->staff_origin_label;
	}
	
	public function GetSeasonDates() {
		return $this->season_dates;
	}
	
	public function GetRequirements() {
		return $this->requirements;
	}
	
	public function GetHowToApply() {
		return $this->how_to_apply;
	}

	public function SetCampActivityList() {
		$result = Refdata::Get(REFDATA_ACTIVITY, PROFILE_COMPANY, $this->GetId(), $labels= TRUE);
		
		$this->camp_activity_list = array_keys($result);
		$this->camp_activity_labels = array_values($result); 
	}
	
	public function GetCampActivityList() {
		return $this->camp_activity_list; 
	}
	
	public function GetCampActivityLabels() {
		return  $this->camp_activity_labels;
	}
	
	public function SetCampTypeList() {
		$result = Refdata::Get(REFDATA_CAMP_TYPE, PROFILE_COMPANY, $this->GetId(), $labels = TRUE);
		
		$this->camp_type_list = array_keys($result);
		$this->camp_type_labels = array_values($result);
	}
		
	public function GetCampTypeLabels() {
		return $this->camp_type_labels;
	}
	
	public function GetCampTypeList() {
		return $this->camp_type_list;
	}
	
	public function SetCampJobTypeList() {
		 $result = Refdata::Get(REFDATA_CAMP_JOB_TYPE, PROFILE_COMPANY, $this->GetId(), $labels = TRUE);
		 
		$this->camp_job_type_list = array_keys($result);
		$this->camp_job_type_labels = array_values($result);
	} 
	
	public function GetCampJobTypeLabels() {
		return $this->camp_job_type_labels;
	}
	
	
	public function GetCampJobTypeList() {
		return $this->camp_job_type_list;
	}	

}
