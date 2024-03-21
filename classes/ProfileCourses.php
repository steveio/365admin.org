<?php


/* Extended profile - Courses including language schools */

define('PROFILE_FIELD_COURSES_DURATION_FROM','cs_duration_from_id');
define('PROFILE_FIELD_COURSES_DURATION_TO','cs_duration_to_id');
define('PROFILE_FIELD_COURSES_DURATION_LABEL','cs_duration_label');
define('PROFILE_FIELD_COURSES_PRICE_FROM','cs_price_from_id');
define('PROFILE_FIELD_COURSES_PRICE_TO','cs_price_to_id');
define('PROFILE_FIELD_COURSES_PRICE_LABEL','cs_price_label');
define('PROFILE_FIELD_COURSES_CURRENCY','cs_currency_id');
define('PROFILE_FIELD_COURSES_START_DATES','cs_start_dates');
define('PROFILE_FIELD_COURSES_REQUIREMENTS','cs_requirements');
define('PROFILE_FIELD_COURSES_QUALIFICATION','cs_qualification');
define('PROFILE_FIELD_COURSES_PREPARATION','cs_preparation');
define('PROFILE_FIELD_COURSES_HOW_TO_APPLY','cs_how_to_apply');

define('PROFILE_FIELD_COURSES_LANGUAGES','cs_languages');
define('PROFILE_FIELD_COURSES_COURSES','cs_courses');
define('PROFILE_FIELD_COURSES_COURSE_TYPE','cs_course_type');
define('PROFILE_FIELD_COURSES_ACCOMODATION','cs_accomodation');



class CoursesProfile extends CompanyProfile {
	
	const PROFILE_TABLE_NAME = "profile_courses";
	const PROFILE_LINK_TO_STR = "COMPANY"; // some of the old mapping tables (eg img_map) use string COMPANY as link_to
	const PROFILE_LINK_TO_ID = PROFILE_COURSES; // newer mapping tables eg refdata use profile_type_id as link to
	
	/* profile specific fields */

	protected $duration_from_id;
	protected $duration_to_id;
	protected $duration_label;
	protected $price_from_id;
	protected $price_to_id;
	protected $price_label;
	protected $currency_id;
	protected $start_dates;
	protected $requirements;
	protected $qualification;
	protected $preparation;
	protected $how_to_apply;
	
	protected $languages;
	protected $courses;
	protected $course_type;
	protected $accomodation;

	
	public function __Construct() {
		
		parent::__construct();
		
		$this->sTblName = self::PROFILE_TABLE_NAME;
		$this->SetLinkTo(self::PROFILE_LINK_TO_STR);
	
		$this->languages = array();
		$this->courses = array();
		$this->course_type = array();
		$this->accomodation = array();
	}
	

	public function GetById($id,$return = "ARRAY") {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		if (!is_numeric($id)) return false;

		parent::__Construct();
		
		
		parent::SetSubTypeTable($this->GetSubTypeTable());
		parent::SetSubTypeFields($this->GetSubTypeFields());

		
		$oResult = parent::GetById($id, $return = "PROFILE");

		if (!$oResult) return FALSE;

		$this->SetLanguages();
		$this->SetCourseType();
		$this->SetCourses();
		$this->SetAccomodation();

		return TRUE;

	}
	

	private function GetSubTypeTable() {
		return $sSubTypeTable = "LEFT OUTER JOIN ".$this->sTblName." c2 ON c.id = c2.company_id";
	}

	private function GetSubTypeFields() {

		return $sSubTypeFields = "
                            	,c2.duration_from_id
                            	,c2.duration_to_id
                            	,c2.price_from_id
                            	,c2.price_to_id
                            	,c2.currency_id
                            	,c2.start_dates
                            	,c2.requirements
                            	,c2.qualification
                            	,c2.preparation
                            	,c2.how_to_apply
							";
	}	
	
	public function DoAddUpdate(&$c,&$aResponse,$bRedirect = false,$bApproved = true, $tx = TRUE) {
		
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


		/* update refdata field mappings */
		$aId = Mapping::GetIdByKey($c,REFDATA_LANGUAGES_PREFIX);
		Mapping::UpdateRefData("refdata_map",PROFILE_COMPANY,$c['id'],REFDATA_LANGUAGES, $aId);
		
		$aId = Mapping::GetIdByKey($c,REFDATA_COURSES_PREFIX);
		Mapping::UpdateRefData("refdata_map",PROFILE_COMPANY,$c['id'],REFDATA_COURSES, $aId);
		
		$aId = Mapping::GetIdByKey($c,REFDATA_COURSE_TYPE_PREFIX);
		Mapping::UpdateRefData("refdata_map",PROFILE_COMPANY,$c['id'],REFDATA_COURSE_TYPE, $aId);

		$aId = Mapping::GetIdByKey($c,REFDATA_ACCOMODATION_PREFIX);
		Mapping::UpdateRefData("refdata_map",PROFILE_COMPANY,$c['id'],REFDATA_ACCOMODATION, $aId);
		
		
		$db->query("COMMIT");

		
		return TRUE;
		
	}

	public function AddSubTypeRecord($p) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;

		if (!is_numeric($p['id'])) return false;


		$sql = "INSERT INTO ".self::PROFILE_TABLE_NAME ." (
                    company_id
                    ,duration_from_id
                    ,duration_to_id
                    ,price_from_id
                    ,price_to_id
                    ,currency_id
                    ,start_dates
                    ,requirements
                    ,qualification
                    ,preparation
                    ,how_to_apply
                ) VALUES (
                    ".$p['id']."
                    ,".$p[PROFILE_FIELD_COURSES_DURATION_FROM]."
                    ,".$p[PROFILE_FIELD_COURSES_DURATION_TO]."
                    ,".$p[PROFILE_FIELD_COURSES_PRICE_FROM]."
                    ,".$p[PROFILE_FIELD_COURSES_PRICE_TO]."
                    ,".$p[PROFILE_FIELD_COURSES_CURRENCY]."
                    ,'".$p[PROFILE_FIELD_COURSES_START_DATES]."'
                    ,'".$p[PROFILE_FIELD_COURSES_REQUIREMENTS]."'
                    ,'".$p[PROFILE_FIELD_COURSES_QUALIFICATION]."'
                    ,'".$p[PROFILE_FIELD_COURSES_PREPARATION]."'
                    ,'".$p[PROFILE_FIELD_COURSES_HOW_TO_APPLY]."'
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

		/* is there an existing sub-type record ? */
		if (!$db->getFirstCell("SELECT 1 FROM ".$this->sTblName." WHERE company_id = ".$p['id'])) {
			return $this->AddSubTypeRecord($p);
		}
		
		
		$sql = "UPDATE ".self::PROFILE_TABLE_NAME ." SET
                    duration_from_id = ".$p[PROFILE_FIELD_COURSES_DURATION_FROM]."
                    ,duration_to_id = ".$p[PROFILE_FIELD_COURSES_DURATION_TO]."
                    ,price_from_id = ".$p[PROFILE_FIELD_COURSES_PRICE_FROM]."
                    ,price_to_id = ".$p[PROFILE_FIELD_COURSES_PRICE_TO]."
                    ,currency_id = ".$p[PROFILE_FIELD_COURSES_CURRENCY]."
                    ,start_dates = '".$p[PROFILE_FIELD_COURSES_START_DATES]."'
                    ,requirements = '".$p[PROFILE_FIELD_COURSES_REQUIREMENTS]."'
                    ,qualification = '".$p[PROFILE_FIELD_COURSES_QUALIFICATION]."'
                    ,preparation = '".$p[PROFILE_FIELD_COURSES_PREPARATION]."'
                    ,how_to_apply = '".$p[PROFILE_FIELD_COURSES_HOW_TO_APPLY]."'
				WHERE company_id = ".$p['id']." ;";
		
		$db->query($sql);
	
		if ($db->getAffectedRows() == 1) {
			return TRUE;
		}
		
	}
	
	/* inject type specific form values into _POST to pre-populate edit form */ 
	public function SetTypeSpecificFormValues() {

	    $_POST[PROFILE_FIELD_COURSES_DURATION_FROM] = $this->GetDurationFromId();
	    $_POST[PROFILE_FIELD_COURSES_DURATION_TO] = $this->GetDurationToId();
	    $_POST[PROFILE_FIELD_COURSES_PRICE_FROM] = $this->GetPriceFromId();
	    $_POST[PROFILE_FIELD_COURSES_PRICE_TO] = $this->GetPriceToId();
	    $_POST[PROFILE_FIELD_COURSES_CURRENCY] = $this->GetCurrencyId();
	    $_POST[PROFILE_FIELD_COURSES_START_DATES] = $this->GetStartDates();
	    $_POST[PROFILE_FIELD_COURSES_REQUIREMENTS] = $this->GetRequirements();
	    $_POST[PROFILE_FIELD_COURSES_QUALIFICATION] = $this->GetQualifications();
	    $_POST[PROFILE_FIELD_COURSES_PREPARATION] = $this->GetPreparation();
	    $_POST[PROFILE_FIELD_COURSES_HOW_TO_APPLY] = $this->GetHowToApply();
	    
	}

	public function GetStartDates() {
	    return $this->start_dates;
	}

	public function GetRequirements() {
		return $this->requirements;
	}

	public function GetQualifications() {
	    return $this->qualification;
	}

	public function GetPreparation() {
	    return $this->preparation;
	}

	public function GetHowToApply() {
	    return $this->how_to_apply;
	}
	
	public function SetLanguages() {
	    $result = Refdata::Get(REFDATA_LANGUAGES, PROFILE_COMPANY, $this->GetId(), $labels= TRUE);
	    
	    $this->languages = array_keys($result);
	    $this->languages_labels = array_values($result);
	}
	
	public function GetLanguages() {
	    return $this->languages;
	}
	
	public function GetLanguagesLabels() {
	    return  $this->languages_labels;
	}

	public function SetCourses() {
	    $result = Refdata::Get(REFDATA_COURSES, PROFILE_COMPANY, $this->GetId(), $labels= TRUE);
	    
	    $this->courses = array_keys($result);
	    $this->courses_labels = array_values($result);
	}
	
	public function GetCourses() {
	    return $this->courses;
	}
	
	public function GetCoursesLabels() {
	    return  $this->courses_labels;
	}

	public function SetCourseType() {
	    $result = Refdata::Get(REFDATA_COURSE_TYPE, PROFILE_COMPANY, $this->GetId(), $labels= TRUE);
	    
	    $this->course_type = array_keys($result);
	    $this->course_type_labels = array_values($result);
	}
	
	public function GetCourseType() {
	    return $this->course_type;
	}
	
	public function GetCourseTypeLabels() {
	    return  $this->course_type_labels;
	}

	public function SetAccomodation() {
	    $result = Refdata::Get(REFDATA_ACCOMODATION, PROFILE_COMPANY, $this->GetId(), $labels= TRUE);
	    
	    $this->accomodation = array_keys($result);
	    $this->accomodation_labels = array_values($result);
	}
	
	public function GetAccomodation() {
	    return $this->accomodation;
	}
	
	public function GetAccomodationLabels() {
	    return  $this->accomodation_labels;
	}

}

?>