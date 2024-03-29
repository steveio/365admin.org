<?php

/*
 * Tour Profile Class
 *
 *
 */


// placement tour profile
define('PROFILE_FIELD_PLACEMENT_TOUR_CODE','code');
define('PROFILE_FIELD_PLACEMENT_ITINERY','itinery');
define('PROFILE_FIELD_PLACEMENT_TOUR_PRICE','tour_price');
define('PROFILE_FIELD_PLACEMENT_START_DATES','dates');
define('PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS','tour_requirements');
define('PROFILE_FIELD_PLACEMENT_TOUR_TRAVEL','tour_travel_transport');
define('PROFILE_FIELD_PLACEMENT_TOUR_MEALS','tour_meals');
define('PROFILE_FIELD_PLACEMENT_TOUR_ACCOM','tour_accom');
define('PROFILE_FIELD_PLACEMENT_TOUR_DURATION_FROM','tour_duration_from_id');
define('PROFILE_FIELD_PLACEMENT_TOUR_DURATION_TO','tour_duration_to_id');
define('PROFILE_FIELD_PLACEMENT_TOUR_DURATION_LABEL','tour_duration_label');
define('PROFILE_FIELD_PLACEMENT_TOUR_PRICE_FROM','tour_price_from_id');
define('PROFILE_FIELD_PLACEMENT_TOUR_PRICE_TO','tour_price_to_id');
define('PROFILE_FIELD_PLACEMENT_TOUR_PRICE_LABEL','tour_price_label');
define('PROFILE_FIELD_PLACEMENT_TOUR_CURRENCY','tour_currency_id');
define('PROFILE_FIELD_PLACEMENT_GROUP_SIZE','group_size_id');



class TourProfile extends PlacementProfile {

	protected $code;
	//protected $duration_txt; // @depreciated
	protected $dates;
	protected $itinery;
	protected $tour_price; // text - all info about tour costs 
    // @depreciated (merged into tour_price)
	//protected $local_payment;
	//protected $included;
	//protected $not_included;
	protected $tour_requirements;

	protected $duration_from_id;
	protected $duration_to_id;
	protected $price_from_id;
	protected $price_to_id;
	protected $currency_id;
	protected $group_size_id;

	protected $transport_id_list;
	protected $meals_id_list;
	protected $accomodation_id_list;
	
	
	private $sTblName; /* tour profile table */	

	public function __Construct() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		$this->sTblName = "profile_tour";
		$this->SetLinkTo("PLACEMENT");

		$this->transport_id_list = array();
		$this->meals_id_list = array();
		$this->accomodation_id_list = array();

		$this->transport_labels = array();
		$this->meals_labels = array();
		$this->accomodation_labels = array();
	}
	

	public function GetById($id) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		if (!is_numeric($id)) return false;

		parent::__Construct();
		
		parent::SetSubTypeTable($this->GetSubTypeTable());
		parent::SetSubTypeFields($this->GetSubTypeFields());

		$oResult = parent::GetProfileById($id);

		if (!$oResult) return FALSE;
		
		foreach($oResult as $k => $v) {
			$this->$k = is_string($v) ? stripslashes($v) : $v;
		}

		parent::GetCategoryInfo();
		parent::GetActivityInfo();
		parent::GetCountryInfo();
		parent::GetImages();

		$this->SetTransportIdList();
		$this->SetMealsIdList();
		$this->SetAccomodationIdList();
		
		return TRUE;		
	}


	private function GetSubTypeTable() {
		return $sSubTypeTable = "LEFT OUTER JOIN ".$this->sTblName." p2 ON p.id = p2.p_hdr_id";
	}

	private function GetSubTypeFields() {

		return $sSubTypeFields = "
							,p2.code
							,p2.dates
							,p2.itinery
							,p2.price as tour_price
							,p2.requirements as tour_requirements
							,p2.duration_from_id
							,p2.duration_to_id
							,p2.price_from_id
							,p2.price_to_id
							,p2.currency_id
							,p2.group_size_id
							";			
	}


	public function AddSubTypeRecord($p) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;

		/* check that profile_hdr id and subtype record exist */
		if (!is_numeric($p['id'])) return false;

		$sql = "INSERT INTO ".$this->sTblName." (
										p_hdr_id
										,code
										,dates
										,itinery
										,price
										,requirements
										,duration_from_id
										,duration_to_id
										,price_from_id
										,price_to_id
										,currency_id
										,group_size_id
									) VALUES (
										".$p['id']."
										,'".$p[PROFILE_FIELD_PLACEMENT_TOUR_CODE]."'
										,'".$p[PROFILE_FIELD_PLACEMENT_START_DATES]."'
										,'".$p[PROFILE_FIELD_PLACEMENT_ITINERY]."'
										,'".$p[PROFILE_FIELD_PLACEMENT_TOUR_PRICE]."'
										,'".$p[PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS]."'
										,".$p[PROFILE_FIELD_PLACEMENT_TOUR_DURATION_FROM]."
										,".$p[PROFILE_FIELD_PLACEMENT_TOUR_DURATION_TO]."
										,".$p[PROFILE_FIELD_PLACEMENT_TOUR_PRICE_FROM]."
										,".$p[PROFILE_FIELD_PLACEMENT_TOUR_PRICE_TO]."
										,".$p[PROFILE_FIELD_PLACEMENT_TOUR_CURRENCY]."
										,".$p[PROFILE_FIELD_PLACEMENT_GROUP_SIZE]."
										);";

		$db->query($sql);

		if ($db->getAffectedRows() == 1) {
			$this->UpdateRefData($p);
			return true;
		} else {
			Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$sql);
		}

	}



	public function UpdateSubTypeRecord($p) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;

		/* check that profile_hdr id and subtype record exist */
		if (!is_numeric($p['id'])) return false;

		//if (!is_numeric($p['type'])) $p['type'] = 'null';
		//if (!is_numeric($p['duration'])) $p['duration'] = 'null';

		/* is there an existing sub-type record ? */
		if (!$db->getFirstCell("SELECT 1 FROM ".$this->sTblName." WHERE p_hdr_id = ".$p['id'])) {
			return $this->AddSubTypeRecord($p);
		}
		
		$sql = "UPDATE ".$this->sTblName."
					SET  
					code = '".$p[PROFILE_FIELD_PLACEMENT_TOUR_CODE]."' 
					,dates = '".$p[PROFILE_FIELD_PLACEMENT_START_DATES]."'
					,itinery = '".$p[PROFILE_FIELD_PLACEMENT_ITINERY]."'
					,price = '".$p[PROFILE_FIELD_PLACEMENT_TOUR_PRICE]."'
					,requirements = '".$p[PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS]."'
					,duration_from_id = ".$p[PROFILE_FIELD_PLACEMENT_TOUR_DURATION_FROM]."
					,duration_to_id = ".$p[PROFILE_FIELD_PLACEMENT_TOUR_DURATION_TO]."
					,price_from_id = ".$p[PROFILE_FIELD_PLACEMENT_TOUR_PRICE_FROM]."					
					,price_to_id = ".$p[PROFILE_FIELD_PLACEMENT_TOUR_PRICE_TO]."
					,currency_id = ".$p[PROFILE_FIELD_PLACEMENT_TOUR_CURRENCY]."
					,group_size_id = ".$p[PROFILE_FIELD_PLACEMENT_GROUP_SIZE]."
					WHERE p_hdr_id = ".$p['id']."
				";
			
		$db->query($sql);

		if ($db->getAffectedRows() != 1) {
			Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$sql);
			return false;
		}

		$this->UpdateRefData($p);
		
		return true;
	}

	private function UpdateRefdata($p) {
						
		/* update refdata (multiple select) - travel/transport, accomodation, meals */
		$aId = Mapping::GetIdByKey($p,REFDATA_TRAVEL_TRANSPORT_PREFIX);
		Mapping::UpdateRefData("refdata_map",PROFILE_PLACEMENT,$p['id'],REFDATA_TRAVEL_TRANSPORT, $aId);
		
		$aId = Mapping::GetIdByKey($p,REFDATA_ACCOMODATION_PREFIX);
		Mapping::UpdateRefData("refdata_map",PROFILE_PLACEMENT,$p['id'],REFDATA_ACCOMODATION, $aId);
		
		$aId = Mapping::GetIdByKey($p,REFDATA_MEALS_PREFIX);
		Mapping::UpdateRefData("refdata_map",PROFILE_PLACEMENT,$p['id'],REFDATA_MEALS, $aId);
		
	}
	

	public function GetCode() {
		return $this->code;
	}


	public function GetDates() {
		return $this->dates;
	}

	public function GetItinery() {
		return $this->itinery;
	}

	public function GetPrice() {
		return $this->tour_price;
	}

	
	public function GetRequirements() {
		return $this->tour_requirements;
	}
	
	
	public function GetGroupSizeId() {
		return $this->group_size_id;
	}

	public function GetGroupSizeLabel()
	{
	    if (!is_object($this->oGroupSize))
	    {
	        $this->oGroupSize = new Refdata(REFDATA_INT_SMALL_RANGE);
	        $this->oGroupSize->GetByType();
	        $this->oGroupSize->SetOption(REFDATA_OPTION_CHECKBOXES_DISABLED, TRUE);
	        
	        return $this->group_size_label = $this->oGroupSize->GetValueById($this->GetGroupSizeId());
	    } else {
	        return $this->group_size_label;
	    }
	}

	private function SetTransportIdList() {
		
		$result = Refdata::Get(REFDATA_TRAVEL_TRANSPORT, PROFILE_PLACEMENT, $this->GetId(), $labels = TRUE);
		$this->transport_id_list = array_keys($result);
		$this->transport_labels = array_values($result);
	}
	
	public function GetTransportLabels() {
		return $this->transport_labels;
	}
		
	public function GetTransportIdList() {
		return $this->transport_id_list;
	}
	
	private function SetMealsIdList() { 
		$result = Refdata::Get(REFDATA_MEALS, PROFILE_PLACEMENT, $this->GetId(), $labels = TRUE);
		$this->meals_id_list  = array_keys($result);
		$this->meals_labels  = array_values($result);
	}
	
	public function GetMealsIdList() {
		return $this->meals_id_list;	
	}
	
	public function GetMealsLabels() {
		return $this->meals_labels;
	}

	public function SetAccomodationIdList() {
		
		$result = Refdata::Get(REFDATA_ACCOMODATION, PROFILE_PLACEMENT, $this->GetId(), $labels = TRUE);
		
		$this->accomodation_id_list = array_keys($result); 
		$this->accomodation_labels = array_values($result);
	}
	
	public function GetAccomodationIdList() {
		return $this->accomodation_id_list;
	}
	
	public function GetAccomodationLabels() {
		return $this->accomodation_labels;	
	}

	public function GetTravelOptions()
	{
	    if (!is_object($this->oTravelOptions))
	    {
	        $this->oTravelOptions = new Refdata(REFDATA_TRAVEL_TRANSPORT);
	        $aSelected = array();
	        $aSelected = $this->GetTransportIdList();
	        $this->oTravelOptions->SetOption(REFDATA_OPTION_CHECKBOXES_DISABLED, TRUE);
	        return $this->travel_options_array = $this->oTravelOptions->GetLabelsFromSelectedIds($aSelected);
	    } else {
	        return $this->travel_options_array;
	    }
	}

	public function GetAccomOptions()
	{
	    if (!is_object($this->oAccomOptions))
	    {
	        $this->oAccomOptions = new Refdata(REFDATA_ACCOMODATION);
	        $aSelected = array();
	        $aSelected = $this->GetAccomodationIdList();
	        $this->oAccomOptions->SetOption(REFDATA_OPTION_CHECKBOXES_DISABLED, TRUE);
	        return $this->accom_options_array = $this->oAccomOptions->GetLabelsFromSelectedIds($aSelected);
	    } else {
	        return $this->accom_options_array;
	    }
	}

	public function GetMealOptions()
	{
	    if (!is_object($this->oMealOptions))
	    {
	        $this->oMealOptions = new Refdata(REFDATA_MEALS);
	        $aSelected = array();
	        $aSelected = $this->GetMealsIdList();
	        $this->oMealOptions->SetOption(REFDATA_OPTION_CHECKBOXES_DISABLED, TRUE);
	        return $this->meal_options_array = $this->oMealOptions->GetLabelsFromSelectedIds($aSelected);
	    } else {
	        return $this->meal_options_array;
	    }
	}

        
}


?>
