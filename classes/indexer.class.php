<?


/*
* Indexer is used to create keyword indices from profile text
* 
* It is called by indexer_batch.php which in turn is invoked by cron
*
* Can be run in two modes ($this->mode) : 0 = keyword index, 1 = tag cloud, 2 = both
* 
*  - keyword index mode generates a stemmed keyword index which principle search runs off 
*  - tag cloud mode @depreciated generates an unstemmed index for a tag cloud 
*
*/



define("WEIGHT_LOW",1);
define("WEIGHT_MED",2);
define("WEIGHT_HIGH",4);
define("WEIGHT_MAX",6);


class Indexer {

	public function __construct(&$db) {
		$this->Indexer($db);
	}

	public function Indexer(&$db) {
		$this->db = $db;
		$this->debug = false;
		$this->mode = 0;

		$this->aKeywordExclude = array();
		
	}

	public function UpdateIndex($id,$type,$aWords,$index) {

		foreach($aWords as $word => $freq) {
			$sql = "INSERT INTO $index VALUES ($id,$type,'".addslashes($word)."',$freq);";
			$this->db->query($sql);
		}
	}

	public function PreProcess() {
		if (($this->mode == 1) || ($this->mode == 2)) {
			$this->db->query("DELETE FROM keyword_idx_1");
		}
		if (($this->mode == 0) || ($this->mode == 2)) {
			$this->db->query("DELETE FROM keyword_idx_2");
		}
	}

	public function PostProcess() {

		if (($this->mode == 1) || ($this->mode == 2)) {
			$this->db->query("update keyword_idx_1 set word = 'united states', count = 6 where word = 'united';");
			$this->db->query("delete from keyword_idx_1 where word = 'states';");
			$this->db->query("update keyword_idx_1 set word = 'united kingdom', count = 6 where word = 'kingdon';");
			$this->db->query("update keyword_idx_1 set count = 12 where word in ('camp america','ccusa');");
			$this->db->query("update keyword_idx_1 set count = 4 where word in ('bunac');");
			$this->db->query("delete from keyword_idx_1 where word = 'camps';");
			$this->db->query("delete from keyword_idx_1 where word = 'girl';");
			$this->db->query("update keyword_idx_1 set word = 'uk' where word = 'kingdom';");
		}
	}

	public function getWords($phrase,$weight,$split = true, $stemmed = false,$count = true,$filter = true)  {

		$phrase = strtolower($phrase);
		if ($split == true) {
			$phrase .= str_repeat(' '.$phrase, $weight);
			$words = str_word_count($phrase, 1);
			$words = $this->removeStopWordsFromArray($words);
		} else { // process whole phrase
			$phrase .= str_repeat('::'.$phrase, $weight);
			$words = explode("::",$phrase);
		}

		if ($stemmed == true) {
			$words = $this->stemPhrase($words);
		}
				
		if ($count == true) {
			$words = array_count_values($words);
		}		
		
		if (!$filter) return $words;

		$words = $this->FilterExcludedKeywords($words);
		
		return $words;

	}

	public function removeStopWordsFromArray($words)  {

		$stop_words = array(
			'-','i', 'me', 'my', 'myself', 'we', 'our', 'ours', 'ourselves', 'you', 'your', 'yours', 
			'yourself', 'yourselves', 'he', 'him', 'his', 'himself', 'she', 'her', 'hers', 
			'herself', 'it', 'its', 'itself', 'they', 'them', 'their', 'theirs', 'themselves', 
			'what', 'which', 'who', 'whom', 'this', 'that', 'these', 'those', 'am', 'is', 'are', 
			'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'having', 'do', 'does', 
			'did', 'doing', 'a', 'an', 'the', 'and', 'but', 'if', 'or', 'because', 'as', 'until', 
			'while', 'of', 'at', 'by', 'for', 'with', 'about', 'against', 'between', 'into', 
			'through', 'during', 'before', 'after', 'above', 'below', 'to', 'from', 'up', 'down', 
			'in', 'out', 'on', 'off', 'over', 'under', 'again', 'further', 'then', 'once', 'here', 
			'there', 'when', 'where', 'why', 'how', 'all', 'any', 'both', 'each', 'few', 'more', 
			'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 
			'than', 'too', 'very','p','pe','include','new','throughout','will','s','st','across','br','would',
			'we\'ve','you\'d','thing','you\'ll','i\'ve','can','you\'re'
		);

		return array_diff($words, $stop_words);
	}

	public function stemPhrase($words)
	{

		// stem words
		$stemmed_words = array();
		foreach ($words as $word)
		{
		  // ignore 1 and 2 letter words
		  if (strlen($word) <= 2)
		  {
			continue;
		  }

		  $stemmed_words[] = PorterStemmer::stem($word, true);
		}

		return $stemmed_words;
	}

	private function GetKeywordExcludeArray() {
		return $this->aKeywordExclude;
	}
	
	private function SetKeywordExcludeArray($a) {
		if (!is_array($a)) $a = array();
		$this->aKeywordExclude = $a;		
	}
	
	private function FilterExcludedKeywords($aWords) {
		
		if (!is_array($aWords)) return array();
		
		//Logger::Msg("input");
		//Logger::Msg($aWords);
		
		$a = array();

		$aExclude = $this->GetKeywordExcludeArray();
		
		//Logger::Msg("exclude");
		//Logger::Msg($aExclude);
		
		
		foreach($aWords as $sWord => $iFreq) {
			if(!in_array($sWord,$aExclude)) {
				$a[$sWord] = $iFreq;
			}
		}
		
		//Logger::Msg("output");
		//Logger::Msg($a);
		
		return $a;
	}
	

	function indexPlacement($oProfile,$sIndex = "keyword_idx_2") {

		if (LOG) Logger::DB(3,JOBNAME,__CLASS__."->".__FUNCTION__."() Processing Profile id: ".$oProfile->GetId());
		
		
		/* handle keywords excluded from index */
		$this->SetKeywordExcludeArray(array());		
		if (strlen($oProfile->GetKeywordExclude()) > 1) {
			$this->SetKeywordExcludeArray($this->getWords($oProfile->GetKeywordExclude(),0,$split = true, $stemmed = true,$count = false,$filter = false));
		}

		// title
		$aWords = $this->getWords($oProfile->GetTitle(),WEIGHT_HIGH,$split = true, $stemmed = true);			
		$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);

		//Logger::Msg($aWords,'plaintext');

		// short desc
		$aWords = $this->getWords($oProfile->GetDescShort(),WEIGHT_HIGH,$split = true, $stemmed = true);
		$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);

		//Logger::Msg($aWords,'plaintext');

		// full desc
		$aWords = $this->getWords($oProfile->GetDescLong(),WEIGHT_LOW,$split = true, $stemmed = true);
		$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);

		//Logger::Msg($aWords,'plaintext');

		// category, activity, country, continent 
		$str = $oProfile->GetActivityTxt() . " " .$oProfile->GetCategoryTxt() . " " .$oProfile->GetCountryTxt() . " " . $oProfile->GetContinentTxt() . " " . $oProfile->GetLocation();
		$aWords = $this->getWords($str,WEIGHT_MED,$split = true, $stemmed = true);
		$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		

		//Logger::Msg($aWords,'plaintext');
		
		if ($oProfile instanceof GeneralProfile) {

			// Start Dates			
			$words = $oProfile->GetStartDates();
			//Logger::Msg("StartDates: ".$words);
			$aWords = $this->getWords($words,WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		
			
			// Costs / Salary / Benefits
			$words = $oProfile->GetBenefits();
			//Logger::Msg("CostsSalary: ".$words);
			$aWords = $this->getWords($words,WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		
			
			// Requirements
			$words = $oProfile->GetRequirements();
			//Logger::Msg("Requirements: ".$words);
			$aWords = $this->getWords($words,WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		
			
		}
		
		if ($oProfile instanceof TourProfile) {
			
			// Start Dates
			$words = $oProfile->GetDates();
			//Logger::Msg("Dates: ".$words);
			$aWords = $this->getWords($words,WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		
			
			// Itinery
			$words = $oProfile->GetItinery();
			//Logger::Msg("Itinery: ".$words);
			$aWords = $this->getWords($words,WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		
			
			// Price / Costs
			$words = $oProfile->GetPrice();
			//Logger::Msg("Price/Costs/Benefits: ".$words);
			$aWords = $this->getWords($words,WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		
			
			// Travel / Transport
			$words = implode(" ",$oProfile->GetTransportLabels());
			//Logger::Msg("Travel/Transport: ".$words);
			$aWords = $this->getWords($words,WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		
			
			// Accomodation
			$words = implode(" ",$oProfile->GetAccomodationLabels());
			//Logger::Msg("Accomodation: ".$words);
			$aWords = $this->getWords($words,WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		
			
			// Meals
			$words = implode(" ",$oProfile->GetMealsLabels());
			//Logger::Msg("Meals: ".$words);
			$aWords = $this->getWords($words,WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		
			
			// Requirement
			$words = $oProfile->GetRequirements();
			//Logger::Msg("Requirements: ".$words);
			$aWords = $this->getWords($words,WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oProfile->GetId(),2,$aWords,$sIndex);		
			
		}

		if ($oProfile instanceof JobProfile) {
			
		}
				

		// set the last_indexed date
		$this->db->query("UPDATE profile_hdr SET last_indexed = now()::timestamp WHERE id = ".$oProfile->GetId()."");

		unset($aWords,$str,$oProfile,$sIndex);
		
	}


	function indexCompany($oCProfile,$sIndex = "keyword_idx_2") {

		
		if (LOG) Logger::DB(3,JOBNAME,__CLASS__."->".__FUNCTION__."() Processing Profile id: ".$oCProfile->GetId().", listing_type: ".$oCProfile->GetListingType());

		if (!is_numeric($oCProfile->GetId())) return;

		$this->SetKeywordExcludeArray(array());		
		if (strlen($oCProfile->GetKeywordExclude()) > 1) {
			$this->SetKeywordExcludeArray($this->getWords($oCProfile->GetKeywordExclude(),0,$split = true, $stemmed = true,$count = false,$filter = false));
		}
		
		
		// STEMMED - KEYWORD SEARCH INDEX

		$weight = ($oCProfile->GetListingType() >= BASIC_LISTING) ?  WEIGHT_HIGH : WEIGHT_MED;  
		
		// title (as words)
		$aWords = $this->getWords($oCProfile->GetTitle(),$weight,$split = true, $stemmed = true);
		$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
		
		// company title (as phrase)
		$aWords = $this->getWords($oCProfile->GetTitle(),$weight,$split = false, $stemmed = false);
		$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
		
		$weight = ($oCProfile->GetListingType() >= BASIC_LISTING) ?  WEIGHT_HIGH : WEIGHT_MED;
		
		// short desc
		$aWords = $this->getWords($oCProfile->GetDescShort(),$weight,$split = true, $stemmed = true);
		$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
		
		// desc long
		$aWords = $this->getWords($oCProfile->GetDescLong(),WEIGHT_LOW,$split = true, $stemmed = true);
		$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
		
		// job info
		$aWords = $this->getWords($oCProfile->GetPlacementInfo(),WEIGHT_LOW,$split = true, $stemmed = true);
		$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
		
		
		// associated info (activity, category, country)
		$extra = $oCProfile->GetActivityTxt() . " " .$oCProfile->GetCategoryTxt() . " " .$oCProfile->GetCountryTxt() . " " . $oCProfile->GetContinentTxt();
		$weight = ($oCProfile->GetListingType() >= BASIC_LISTING) ?  WEIGHT_MED : WEIGHT_LOW;
		$aWords = $this->getWords($extra,$weight,$split = true, $stemmed = true);
		$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);

		// Index SummerCamp Company Profile Extra Fields
		if ($oCProfile instanceof SummerCampProfile) {
			
			//Logger::Msg($oCProfile);
			
			// state name
			$words = $oCProfile->GetStateName();
			//Logger::Msg("StateName: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_HIGH,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Season Dates
			$words = $oCProfile->GetSeasonDates();
			//Logger::Msg("SeasonDates: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Requirements
			$words = $oCProfile->GetRequirements();
			//Logger::Msg("Requirements: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//How to apply
			$words = $oCProfile->GetHowToApply();
			//Logger::Msg("HowToApply: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Staff Gender
			$words = $oCProfile->GetStaffGenderLabel();
			//Logger::Msg("StaffGender: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Staff Origin
			$words = $oCProfile->GetStaffOriginLabel();
			//Logger::Msg("StaffOrigin: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Camp Type
			$words = implode(" ",$oCProfile->GetCampTypeLabels());
			//Logger::Msg("CampType: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_MED,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Job Types
			$words = implode(" ",$oCProfile->GetCampJobTypeLabels());
			//Logger::Msg("JobType: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_MED,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Activities
			$words = implode(" ",$oCProfile->GetCampActivityLabels());
			//Logger::Msg("Activities: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_MED,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			
		}

		// Index Volunteer Project Company Profile Extra Fields
		if ($oCProfile instanceof VolunteerTravelProjectProfile) {
		
			//Awards
			$words = $oCProfile->GetAwards();
			//Logger::Msg("Awards: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Support
			$words = $oCProfile->GetSupport();
			//Logger::Msg("Support: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Safety
			$words = $oCProfile->GetSafety();
			//Logger::Msg("Safety: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Organisation Type
			$words = $oCProfile->GetOrgTypeLabel();
			//Logger::Msg("OrgType: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Species
			$words = implode(" ",$oCProfile->GetSpeciesLabels());
			//Logger::Msg("Species: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			//Habitats
			$words = implode(" ",$oCProfile->GetHabitatsLabels());
			//Logger::Msg("Habitats: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
		}

		// Index SeasonalJobEmployerProfile Company Profile Extra Fields
		if ($oCProfile instanceof SeasonalJobEmployerProfile) {
	
			// Job Types
			$words = $oCProfile->GetJobTypes();
			//Logger::Msg("JobTypes: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			// Salary / Pay
			$words = $oCProfile->GetPay();
			//Logger::Msg("Salary: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			// Benefits
			$words = $oCProfile->GetBenefits();
			//Logger::Msg("Benefits: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			// Requirements
			$words = $oCProfile->GetRequirements();
			//Logger::Msg("Requirements: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
						
			// How to apply
			$words = $oCProfile->GetHowToApply();
			//Logger::Msg("HowToApply: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
		}

		// Index TeachingProjectProfile Company Profile Extra Fields
		if ($oCProfile instanceof TeachingProjectProfile) {
		
			// Salary / Costs
			$words = $oCProfile->GetSalary();
			//Logger::Msg("Salary: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
						
			// Benefits
			$words = $oCProfile->GetBenefits();
			//Logger::Msg("Benefits: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			// Qualifications
			$words = $oCProfile->GetQualifications();
			//Logger::Msg("Qualifications: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			// Requirements / Nationalities
			$words = $oCProfile->GetRequirements();
			//Logger::Msg("Requirements: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
			// How to apply
			$words = $oCProfile->GetHowToApply();
			//Logger::Msg("HowToApply: ".$words);
			$aWords = $this->getWords($words,$weight = WEIGHT_LOW,$split = true, $stemmed = true);
			$this->UpdateIndex($oCProfile->GetId(),1,$aWords,$sIndex);
			
		}
		
		// set the last_indexed date
		$this->db->query("UPDATE company SET last_indexed = now()::timestamp WHERE id = ".$oCProfile->GetId()."");

		unset($title,$desc,$desc_long,$job_info,$extra,$aWords,$oCProfile);

	}



	function deleteEntryFromIndex($id,$type,$index_tbl = 'keyword_idx_2') {

		if (LOG) Logger::DB(3,JOBNAME,__CLASS__."->".__FUNCTION__."() id:".$id.", type:".$type.", index_tbl: ".$index_tbl);
		
		if (!is_numeric($id)) return;

		if ($type == "company") {
			$sql = "DELETE FROM ".$index_tbl." WHERE type = 1 AND id = $id"; 
			$this->db->query($sql);
		} elseif ($type == "placement") {
			$this->db->query("DELETE FROM ".$index_tbl." WHERE type = 2 AND id = $id");
		}

	}

}


?>
