<?php

/*
 * Job Profile Class
 * 
 * 
 */


// placement job profile
define('PROFILE_FIELD_PLACEMENT_JOB_REFERENCE','reference');
define('PROFILE_FIELD_PLACEMENT_JOB_DURATION_LABEL','job_duration_label');
define('PROFILE_FIELD_PLACEMENT_JOB_DURATION_FROM','job_duration_from_id');
define('PROFILE_FIELD_PLACEMENT_JOB_DURATION_TO','job_duration_to_id');
define('PROFILE_FIELD_PLACEMENT_JOB_START_DT','job_start_date'); // actually these 2date fields are divided into month / year
define('PROFILE_FIELD_PLACEMENT_JOB_CLOSING_DATE','close_date');
define('PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE','start_dt_multiple');
define('PROFILE_FIELD_PLACEMENT_JOB_CONTRACT_TYPE','contract_type');
define('PROFILE_FIELD_PLACEMENT_JOB_SALARY','job_salary');
define('PROFILE_FIELD_PLACEMENT_JOB_BENEFITS','job_benefits');
define('PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE','experience');
define('PROFILE_FIELD_PLACEMENT_JOB_OPTIONS','job_options');





class JobProfile extends PlacementProfile {

	protected $reference; 
	protected $start_dt_exact;
	protected $start_dt_multiple;
	protected $job_salary;
	protected $job_benefits;
	protected $contract_type;
	protected $experience;
	protected $closing_dt;
	
	protected $job_options;
	
	private $sTblName; /* profile table */	
	
	public function __Construct() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		$this->sTblName = "profile_job";
		$this->SetLinkTo("PLACEMENT");

		$this->job_options = array();
	}
	
	public function GetById($id) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		if (!is_numeric($id)) return false;

		parent::__Construct();
		
		parent::SetSubTypeTable($this->GetSubTypeTable());
		parent::SetSubTypeFields($this->GetSubTypeFields());

		$oResult = parent::GetProfileById($id);

		if (!$oResult) return FALSE;
		
		/* set the dates in $_REQUEST scope - thats what Date::GetDateInput() looks for */
		if (strlen($oResult->start_dt_exact) > 1) {
			$a = explode("-",$oResult->closing_dt);
			$_REQUEST['StartDateDay'] = $a[0];
			$_REQUEST['StartDateMonth'] = $a[1];
			$_REQUEST['StartDateYear'] = $a[2];
		}
		if (strlen($oResult->closing_dt) > 1) {
			$a = explode("-",$oResult->closing_dt);
			$_REQUEST['CloseDateDay'] = $a[0];
			$_REQUEST['CloseDateMonth'] = $a[1];
			$_REQUEST['CloseDateYear'] = $a[2];
		}		
		foreach($oResult as $k => $v) {
			$this->$k = is_string($v) ? stripslashes($v) : $v;
		}

		parent::GetCategoryInfo();
		parent::GetActivityInfo();
		parent::GetCountryInfo();
		parent::GetImages();
		
		$this->SetJobOptions();

		return TRUE;

	}
	
	
	private function GetSubTypeTable() {
		return $sSubTypeTable = "LEFT OUTER JOIN ".$this->sTblName." p2 ON p.id = p2.p_hdr_id";
	}

	private function GetSubTypeFields() {

		return $sSubTypeFields = "
							,p2.reference
							,p2.duration_from_id
							,p2.duration_to_id
							,to_char(p2.start_dt_exact,'DD-MM-YYYY') as start_dt_exact
							,p2.start_dt_multiple
							,p2.salary as job_salary
							,p2.benefits as job_benefits
							,p2.contract_type
							,p2.experience 
							,to_char(p2.closing_dt,'DD-MM-YYYY') as closing_dt
							";
	}
	

	public function AddSubTypeRecord($p) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;

		/* check that profile_hdr id and subtype record exist */
		if (!is_numeric($p['id'])) return false;

		if ((is_numeric($p['StartDateDay'])) && (is_numeric($p['StartDateMonth'])) && (is_numeric($p['StartDateYear']))) { 
			$sStartDt = $p['StartDateDay']."-".$p['StartDateMonth']."-".$p['StartDateYear'];
			$sStartDtSQL = ",'".$sStartDt."'";
		} else {
			$sStartDtSQL = ",null";
		}
		if ((is_numeric($p['CloseDateDay'])) && (is_numeric($p['CloseDateMonth'])) && (is_numeric($p['CloseDateYear']))) {
			$sCloseDt = $p['CloseDateDay']."-".$p['CloseDateMonth']."-".$p['CloseDateYear'];
			$sCloseDtSQL = ",'".$sCloseDt."'";
		} else {
			$sCloseDtSQL = ",null";
		}
		
		
		
		$sql = "INSERT INTO ".$this->sTblName." (
										p_hdr_id
										,reference
										,duration_from_id
										,duration_to_id
										,start_dt_multiple
										,start_dt_exact
										,salary
										,benefits
										,contract_type
										,experience
										,closing_dt										
										) VALUES (
										".$p['id']."
										,'".$p[PROFILE_FIELD_PLACEMENT_JOB_REFERENCE]."'
										,".$p[PROFILE_FIELD_PLACEMENT_JOB_DURATION_FROM]."
										,".$p[PROFILE_FIELD_PLACEMENT_JOB_DURATION_TO]."
										,'".$p[PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE]."'
										$sStartDtSQL
										,'".$p[PROFILE_FIELD_PLACEMENT_JOB_SALARY]."'
										,'".$p[PROFILE_FIELD_PLACEMENT_JOB_BENEFITS]."'
										,".$p[PROFILE_FIELD_PLACEMENT_JOB_CONTRACT_TYPE]."
										,'".$p[PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE]."'
										$sCloseDtSQL										
										);";

		$db->query($sql);

		if ($db->getAffectedRows() == 1) {
			
			$this->UpdateRefData($p);
			
			return TRUE;
		} else {
			Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$sql);
		}

	}



	public function UpdateSubTypeRecord($p) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;

		/* check that profile_hdr id and subtype record exist */
		if (!is_numeric($p['id'])) return false;

		/* is there an existing sub-type record ? */
		if (!$db->getFirstCell("SELECT 1 FROM ".$this->sTblName." WHERE p_hdr_id = ".$p['id'])) {
			return $this->AddSubTypeRecord($p);
		}

		if ((is_numeric($p['StartDateDay'])) && (is_numeric($p['StartDateMonth'])) && (is_numeric($p['StartDateYear']))) { 
			$sStartDt = $p['StartDateDay']."-".$p['StartDateMonth']."-".$p['StartDateYear'];
			$sStartDtSQL = ",start_dt_exact = '".$sStartDt."'";
		} else {
			$sStartDtSQL = ",start_dt_exact = null";
		}
		if ((is_numeric($p['CloseDateDay'])) && (is_numeric($p['CloseDateMonth'])) && (is_numeric($p['CloseDateYear']))) {
			$sCloseDt = $p['CloseDateDay']."-".$p['CloseDateMonth']."-".$p['CloseDateYear'];
			$sCloseDtSQL = ",closing_dt = '".$sCloseDt."'";
		} else {
			$sCloseDtSQL = ",closing_dt = null";
		}
		
		
		$sql = "UPDATE ".$this->sTblName."
					SET  
					reference = '".$p[PROFILE_FIELD_PLACEMENT_JOB_REFERENCE]."'
					,duration_from_id = ".$p[PROFILE_FIELD_PLACEMENT_JOB_DURATION_FROM]."
					,duration_to_id = ".$p[PROFILE_FIELD_PLACEMENT_JOB_DURATION_TO]."
					,start_dt_multiple = '".$p[PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE]."'
					".$sStartDtSQL." 
					,salary = '".$p[PROFILE_FIELD_PLACEMENT_JOB_SALARY]."'
					,benefits = '".$p[PROFILE_FIELD_PLACEMENT_JOB_BENEFITS]."' 
					,contract_type = ".$p[PROFILE_FIELD_PLACEMENT_JOB_CONTRACT_TYPE]."
					,experience = '".$p[PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE]."'
					".$sCloseDtSQL."
					WHERE p_hdr_id = ".$p['id']."
				";
			
		$db->query($sql);

		if ($db->getAffectedRows() != 1) {
			Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$sql);
			return FALSE;
		}

		$this->UpdateRefData($p);
		return TRUE;
	}
	
	private function UpdateRefdata($p) {
				
		// job options checkboxes 
		$aId = Mapping::GetIdByKey($p,REFDATA_JOB_OPTIONS_PREFIX);
		
		Mapping::UpdateRefData("refdata_map",PROFILE_PLACEMENT,$p['id'],REFDATA_JOB_OPTIONS, $aId);

	}
	
	public function GetReference() { 
		return $this->reference; 
	}
	
	public function GetStartDateExact() { 
		return $this->start_dt_exact;
	}
	
	public function GetStartDateMultiple() { 
		return $this->start_dt_multiple;
	}
	
	public function GetSalary() { 
		return $this->job_salary;
	}
	
	public function GetBenefits() { 
		return $this->job_benefits;
	}
		
	public function GetContractType () { 
		return $this->contract_type;
	}

	public function SetContractTypeRefdataObject($oRefdata) {
	    $this->oContractTypeRefdataObject = $oRefdata;
	}
	
	public function GetContractTypeRefdataObject() {
	    return $this->oContractTypeRefdataObject;
	}
	
	public function GetContractTypeLabel() {
	    if (!is_object($this->GetContractTypeRefdataObject())) {
	        $oRefdata = Refdata::GetInstance(REFDATA_JOB_CONTRACT_TYPE);
	        $oRefdata->SetOrderBySql(' id ASC');
	        $oRefdata->GetByType();
	        $this->SetContractTypeRefdataObject($oRefdata);
	    }
	    
	    return $this->GetContractTypeRefdataObject()->GetValueById($this->contract_type);	    
	}
	
	public function GetExperience() { 
		return $this->experience;
	}
	
	public function GetClosingDate() { 
		return $this->closing_dt;	
	}

	public function SetJobOptionsRefdataObject($oRefdata) {
	    $this->oJobOptionsRefdataObject = $oRefdata;
	}
	
	public function GetJobOptionsRefdataObject() {
	    return $this->oJobOptionsRefdataObject;
	}

	public function SetJobOptions() {
		$result = Refdata::Get(REFDATA_JOB_OPTIONS, PROFILE_PLACEMENT, $this->GetId(), $labels = TRUE);
		$this->job_options  = array_keys($result);
		$this->job_options_labels  = array_values($result);
		
	}

	public function GetJobOptions() {
		return $this->job_options;
	}

	public function GetJobOptionsLabels() {
        return implode(" / ", $this->job_options_labels);	    
	}
	
}




?>
