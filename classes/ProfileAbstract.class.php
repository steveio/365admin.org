<?


/* profile types */
define("PROFILE_COMPANY",0);
define("PROFILE_PLACEMENT",1);
define("PROFILE_VOLUNTEER",2);
define("PROFILE_TOUR",3); 
define("PROFILE_JOB",4);
define("PROFILE_SUMMERCAMP",5); // company profile
define("PROFILE_VOLUNTEER_PROJECT",6); // company profile
define("PROFILE_SEASONALJOBS",7); // company profile
define("PROFILE_TEACHING",8); // company profile


/*
 * Base Profile Class
 * 
 * 
 */

abstract class AbstractProfile implements TemplateInterface {

    public $fetch_mode; /* FETCHMODE__FULL || FETCHMODE__SUMMARY */

	protected $profile_type; /* a constant integer indicating profile type */ 
	protected $link_to; /* a string (eg PLACEMENT || COMPANY) used to associate related attributes */ 

	protected $id;
	protected $oid;

	protected $added_by;
	protected $added_date;
	protected $last_updated;
	protected $last_indexed; // @deprecated
	protected $last_indexed_solr;

	protected $aImage; /* an array of objects associated with this profile */

	public $category_txt; /* string list of country labels associated with this profile */
	public $category_txt_array; /* category text labels as array */
	public $category_array; /* array of country id's associated with this profile */
	public $activity_txt; /* string list of activity labels associated with this profile */
	public $activity_txt_array;
	public $activity_array; /* array of activity id's associated with this profile */
	public $country_txt; /* string list of country labels associated with this profile */
	public $country_txt_array;
	public $continent_txt; /* string list of continent labels associated with this profile */
	public $continent_txt_array;
	public $country_array; /* array of country id's associated with this profile */

	protected $duration_from;
	protected $duration_to;
	protected $review_count;
	protected $review_rating;
	
	protected $oDurationRefdataObject;  // object instance of refdata holding duration lookup values	
	protected $oCostsRefdataObject; // object instance of refdata holding costs lookup label values
	protected $oCurrencyRefdataObject; 
	protected $oOrgTypeRefdataObject;
	protected $oNumberOfRefdataObject;
	
	protected $duration_from_id;
	protected $duration_to_id;
	
	private $oTemplate;
	

	public function __Construct() {
	
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		$this->SetFetchMode(FETCHMODE__FULL);
	}

	public function SetFetchMode($mode) {
	    $this->fetch_mode = $mode;
	}
	
	public function GetFetchMode() {
	    return $this->fetch_mode;
	}

	/**
	 * Return plaintext version of short description
	 * truncated to n chars
	 *
	 * @param int $trunc number of chars
	 * @return string
	 */
	public function GetDescShortFirstSentance()
	{
	    $str = htmlUtils::stripLinks(htmlUtils::convertToPlainText($this->desc_short));

	    $first_period_idx = strpos($str,".");
	    
	    if (!$first_period_idx) return $str;

	    return ucfirst(trim(substr($str, 0, $first_period_idx+1)));
	}

	public function GetDescShortMinusFirstSentance()
	{
	    $str = htmlUtils::stripLinks(htmlUtils::convertToPlainText($this->desc_short));
	    
	    $first_period_idx = strpos($str,".");
	    
	    if (!$first_period_idx) return "";
	    
	    return substr($str, $first_period_idx+1, strlen($str));
	}

	/**
	 * Return plaintext version of short description 
	 * truncated to n chars
	 * 
	 * @param int $trunc number of chars
	 * @return string
	 */
	public function GetDescShortPlaintext($trunc = null, $htmlEntityDecode = true)
	{
	    $str = htmlUtils::stripLinks(htmlUtils::convertToPlainText($this->desc_short, $htmlEntityDecode));
	    if (is_numeric($trunc) && strlen($str) > $trunc)
	    {
	        return substr($str, 0, $trunc)."...";
	    } else {
	        return $str;
	    }
	}

	/**
	 * Return body text (desc_long) with CKEditor markup cleaned 
	 * and all HTML links <a> tags removed 
	 *
	 * @return string
	 */
	public function GetDescLongClean()
	{
	    return htmlUtils::convertCkEditorFont2Html($this->GetDescLong(),"<p>");
	}

	/*
	 * Return type of profile 0 = PROFILE_COMPANY, 1 = PROFILE_PLACEMENT
	 * @todo -read config from DB table profile_types
	*/
	public function GetGeneralType() {
		if (in_array($this->GetType(),array(PROFILE_COMPANY,
				PROFILE_SUMMERCAMP,
				PROFILE_VOLUNTEER_PROJECT,
				PROFILE_SEASONALJOBS,
				PROFILE_TEACHING,
		        PROFILE_COURSES
		))) {
			return PROFILE_COMPANY;
		}
		if (in_array($this->GetType(),array(PROFILE_PLACEMENT,
				PROFILE_VOLUNTEER,
				PROFILE_TOUR,
				PROFILE_JOB))) {
			return PROFILE_PLACEMENT;
		}
	
	}

	public function GetProfileType() {
	    return $this->profile_type;
	}

	public function GetType() {
		return $this->profile_type;
	}

	public function SetType($iType) {
	    $this->profile_type = $iType;
	}

	public function GetTypeLabel() 
	{
	    switch($this->GetGeneralType())
	    {
	        case PROFILE_COMPANY :
                return "COMPANY";
                break;
	        case PROFILE_PLACEMENT :
	            return "PLACEMENT";
	            break;
	    }

	}

	public function GetId() {
	    return $this->id;
	}
	
	public function SetId($id) {
	    $this->id = $id;
	}

	public function GetOid() {
	    return $this->oid;
	}

	public function GetAddedBy() {
		return $this->added_by;
	}
	
	public function GetAddedDate() {
		return $this->added_date;
	}
	
	public function GetLastUpdated() {
		return $this->last_updated;
	}
	
	public function GetLastIndexed() {
		return $this->last_indexed;
	}
	
	public function GetLastIndexedSolr() {
		return $this->last_indexed_solr;
	}
	
	/*
	 * return all properties of a derived class as an array
	 * 
	 */
	public function GetVisible() {
		
		$a = array();
		
		foreach($this as $k => $v) {
			$a[$k] = (is_string($v)) ? stripslashes($v) : $v;
		}

		return $a;
	}

    public function GetAllImages($iType = PROFILE_IMAGE) {
            if (is_array($this->aImage[$iType])) {
                    return $this->aImage[$iType];
            }

    }

    public function GetImageUrlArray() {
        
        $a = array(
            "SMALL" => array("URL" => ''),
            "MEDIUM" => array("URL" => ''),
            "LARGE" => array("URL" => '')
            
        );
        
        if (is_object($this->GetImage(0))) {
            $a['SMALL']['URL'] = $this->GetImage(0)->GetUrl("_sf","");
            if ($this->GetImage(0)->GetHtml("_mf","")) {
                $a['MEDIUM']['URL'] = $this->GetImage(0)->GetUrl("_mf","");
            }
            if ($this->GetImage(0)->GetHtml("_lf","")) {
                $a['LARGE']['URL'] = $this->GetImage(0)->GetUrl("_lf","");
            }
        }
        
        if (is_object($this->GetCompanyLogo())) {
            if (strlen($a['SMALL']['URL']) < 1  && $this->GetCompanyLogo()->GetHtml("_sm","")) {
                $a['SMALL']['URL'] =  $this->GetCompanyLogo()->GetUrl("_sm",$this->GetTitle(),'',FALSE);
            }
            if (strlen($a['MEDIUM']['URL']) < 1  && $this->GetCompanyLogo()->GetHtml("_sm","")) {
                $a['MEDIUM']['URL'] =  $this->GetCompanyLogo()->GetUrl("_sm",$this->GetTitle(),'',FALSE);
            }
        }
        
        return $a;
        
    }
    
    public function GetCompanyLogo() 
    {
        
        global $db;
        
        if (is_object($this->aImage[LOGO_IMAGE][0]))
        {
            return $this->aImage[LOGO_IMAGE][0];
        } elseif (is_numeric($this->GetCompanyid())) {
            $this->SetCompanyLogo();
            if (is_object($this->aImage[LOGO_IMAGE][0]))
            {
                return $this->aImage[LOGO_IMAGE][0];
            }
        }
    }
    
    public function SetCompanyLogo()
    {
        global $db;
        
        if (!is_numeric($this->GetCompanyId())) return false;
        
        $sql = "SELECT i.* FROM image i, image_map m WHERE m.link_id = ".$this->GetCompanyId()." AND m.link_to = 'COMPANY' AND m.type = ".LOGO_IMAGE." AND m.img_id = i.id ORDER BY i.id ASC";

        $db->query($sql);
        
        $aRow = $db->getRow();
        
        if (!is_array($aRow)) {
            return false;
        }
        
        $oImage = new Image($aRow['id'],$aRow['type'],$aRow['ext']);
        
        $this->SetImage($oImage,$iType = LOGO_IMAGE);
        
        return $oImage;

    }

    public function GetLogoUrl()
    {
        return $this->GetCompanyLogoUrl();
    }
    
    
    public function GetCompanyLogoUrl($size = '') {
        
        global $db;
        
        if (is_object($this->aImage[LOGO_IMAGE][0]))
        {
            return $this->aImage[LOGO_IMAGE][0]->GetUrl($size);
        } elseif (is_numeric($this->GetCompanyid())) {
            $this->SetCompanyLogo();
            if (is_object($this->aImage[LOGO_IMAGE][0]))
            {
                return $this->aImage[LOGO_IMAGE][0]->GetUrl($size);
            }
        }
    }

	public function SetFromArray($a) {
		
		foreach($a as $k => $v) {
			$this->$k = (is_string($v)) ? stripslashes($v) : $v;
		}
    }
	
	public function SetFromObject($o) {
				
		foreach($o as $k => $v) {
			$this->$k = (is_string($v)) ? stripslashes($v) : $v;
		}
	}

	/*
	 * Profile Images
	 * 
	 * 
	 */
	public function GetImageUrls() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		$aUrl = array();
				
		if (strlen($this->img_url1) > 1) $aUrl[] = $this->img_url1;
		if (strlen($this->img_url2) > 1) $aUrl[] = $this->img_url2;
		if (strlen($this->img_url3) > 1) $aUrl[] = $this->img_url3;
		if (strlen($this->img_url4) > 1) $aUrl[] = $this->img_url4;

		return $aUrl;
	
	}
	
	public function SetImgUrl($idx,$sUrl,$sPrefix = "img_url") {	
		$var = $sPrefix.$idx;
		$this->$var = $sUrl;
	}

	public function GetImgUrl($idx,$sPrefix = "img_url") {	
		$var = $sPrefix.$idx;
		return $this->$var;
	}
	
	/*
	 * Used as key to retrieve related attributes via mapping tables
	 */

	public function GetLinkId() {
	    return $this->link_id;
	}
	
	public function SetLinkId($sLinkId) {
	    $this->link_id = $sLinkId;
	}

	public function GetLinkTo() {
		return $this->link_to;
	}

	public function SetLinkTo($sLinkTo) {
		$this->link_to = $sLinkTo;
	}

	public function GetImageArray() {
	    return $this->aImage;
	}

	public function GetImageByType($iType = PROFILE_IMAGE, $index = 0) {
	    return $this->aImage[$iType][$index];
	}

	public function GetImagesByType($iType = PROFILE_IMAGE) {
	    return $this->aImage[$iType];
	}
	
	public function GetImages($iType = PROFILE_IMAGE) {

		global $db,$_CONFIG;
		
		if (is_array($this->aImage) && count($this->aImage) >= 1) return;

		$sql = "SELECT i.*,m.type FROM image_map m, image i WHERE m.img_id = i.id AND m.link_to = '".$this->GetLinkTo()."' AND m.link_id = ".$this->GetId()." ORDER BY i.id ASC";

		$db->query($sql);

		if ($db->getNumRows() >= 1) {

			unset($this->aImage[$iType]);					

			$aObj = $db->getObjects();
			foreach($aObj as $o) {
				$oImage = new Image($o->id,$o->type,$o->ext,$o->dimensions,$o->width,$o->height,$o->aspect);
				$this->SetImage($oImage,$o->type);					
			}
		}
		
		return $this->aImage[$iType]; 
	}
	

	private function SetImage($oImage,$iType = PROFILE_IMAGE) {
		$this->aImage[$iType][] = $oImage;
	}

	public function GetImage($idx = 0,$iType = PROFILE_IMAGE) {
		
		if (is_object($this->aImage[$iType][$idx])) {
			return $this->aImage[$iType][$idx];
		}
	}	
  	
  	public function GetMetaKeywords() {

  		$oCategory = new Category();
  		$category_text = $oCategory->GetMetaKeywords($this->category_array);
  		
  		return $this->GetTitle() . "," . $this->GetCategoryTxt(",") .",". $this->GetActivityTxt(",") .",". $this->GetCountryTxt(",") .",". $this->GetContinentTxt(",") .",".$category_text;

  	}
  
	public function GetCountryTxtArray() {
		return $this->country_txt_array;
	}

	public function GetContinentTxtArray() {
		return $this->continent_txt_array;
	}	

	public function GetActivityTxtArray() {
		return $this->activity_txt_array;
	}
	
	public function GetCategoryArray() {
		return $this->category_array;	
	}
	
	public function GetActivityArray() {
		return $this->activity_array;
	}
		
	public function GetContinentArray() {
		return $this->continent_array;
	}
	
	public function GetCategoryTxtArray() {
		return $this->category_txt_array;
	}
	
	/*
	 * Related profile meta-data (Category, Activity, Country) mappings 
	 * 
	 * 
	 */
	
	public function GetCountryTxt($delimeter = "") {
		if (strlen($delimeter) == 1) {
			return preg_replace("/\//",",",$this->country_txt);				
		} else {
 			return $this->country_txt;
		}
	}
	
	public function GetContinentTxt($delimeter = "") {
		if (strlen($delimeter) == 1) {
			return preg_replace("/\//",",",$this->continent_txt);				
		} else {
			return $this->continent_txt;
		}
	}
	
	public function GetActivityTxt($delimeter = "") {
		if (strlen($delimeter) == 1) {
			return preg_replace("/\//",",",$this->activity_txt);				
		} else {
			return $this->activity_txt;
		}
	}

	public function GetCategoryTxt($delimeter = "") {
		if (strlen($delimeter) == 1) {
			return preg_replace("/\//",",",$this->category_txt);				
		} else {		
			return $this->category_txt;
		}
	}
	
	private function GetRelationalKey() { 
		if ($this->GetLinkTo() == "PLACEMENT") {
			return "placement_id";
		} elseif ($this->GetLinkTo() == "COMPANY") {
		 	return "company_id";
		}
	}
	
	
	public function GetActivityInfo() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		$oActivity = new Activity($db);
				
		$arr = $oActivity->GetActivitiesById($this->GetId(),$this->GetRelationalKey());

		if (is_array($arr))
		{
    		unset($sText);
    		for ($i=0; $i<count($arr);$i++) {
    			unset($comma);
    			$comma = ($i < (count($arr) -1)) ? " / " : "";
    			$sText .= $arr[$i]['name'] . $comma;
    			$aText[] = trim($arr[$i]['name']);
    			$aId[] =  $arr[$i]['id'];
    		}		
    				
    		$this->activity_txt = $sText;
    		$this->activity_txt_array = $aText;
    		$this->activity_array = $aId;
		} else {
		    $this->activity_txt = '';
		    $this->activity_txt_array = array();
		    $this->activity_array = array();
		}
	}	


	public function GetCategoryInfo() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;
		
		$oCategory = new Category($db);
		$arr = $oCategory->GetCategoriesById($this->GetId(),$this->GetRelationalKey());
		
		unset($sText);

		$iCount = is_array($arr) ? count($arr) : 0;

		for ($i=0; $i<$iCount;$i++) {
			unset($comma);
			$comma = ($i < (count($arr) -1)) ? " / " : "";
			$sText .= $arr[$i]['name'] . $comma;
			$aText[] = trim($arr[$i]['name']);
			$aId[] =  $arr[$i]['id'];
		}
		$this->category_txt = $sText;
		$this->category_txt_array = $aText;
		$this->category_array = $aId;
	}


	public function GetCountryInfo() {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		$oCountry = new Country($db);
		$arr = $oCountry->getCountriesById($this->GetId(),$this->GetRelationalKey());
		
		unset($sText);
		unset($sText2);
		
		$a = array();

		$iCount = is_array($arr) ? count($arr) : 0;

		for ($i=0; $i<$iCount;$i++) {
			unset($comma);
			$comma = ($i < (count($arr) -1)) ? " / " : "";
			$sText .= $arr[$i]['name'] . $comma;
			$aText[] = trim($arr[$i]['name']);
			$aId[] =  $arr[$i]['id'];
			
			/* build continent text string */
			if (!in_array($arr[$i]['continent'],$a)) {
				$sText2 .= $arr[$i]['continent'] ." / ";
				$aText2[] = trim($arr[$i]['continent']);
				$aId2[] =  $arr[$i]['continent_id'];
				$a[$arr[$i]['continent']] = $arr[$i]['continent']; 
			}

		}
		
		$this->country_txt = $sText;
		$this->country_txt_array = $aText;
		$this->continent_txt = substr_replace($sText2,"",-2); /* strip extra slash */
		$this->continent_txt_array = $aText2;
		$this->country_array = $aId;
		$this->continent_array = $aId2;

	}
	
	public function GetCountryLabel() {
		global $db;
		$oCountry = new Country($db);
		if (is_array($this->country_array) && count($this->country_array) >= 1) {
			return $oCountry->GetCountryLinkList($mode = "text",$this->country_array);
		}
	
	}

	public function GetLocationLabel() 
	{

	    if ($this->GetLinkTo() == "COMPANY")
	    {

	        if (is_array($this->country_array))
	        {
	            if (count($this->country_array) == 1) {
    	            return $this->country_txt;
    	        } else {
        	        return count($this->country_array) ." Destinations";
    	        }
	        }

	    } else { // PLACEMENT
	        if (strlen($this->GetLocation()) > 30 || strlen($this->GetLocation()) < 1)
	        {
	            if (count($this->country_array) == 1) {
	                return $this->country_txt;
	            } else {
	                return "Multiple Destinations";
	            }
	        } else {
	            return $this->GetLocation();
	        }
	    }
	}
	
	public function GetPriceFromId() {
	    return $this->price_from_id;
	}
	
	public function GetPriceToId() {
	    return $this->price_to_id;
	}
	
	public function GetCurrencyId() {
	    return $this->currency_id;
	}
	
	public function GetCurrencyLabel($ISO4217 = false) {
	    
	    if (!is_object($this->GetCurrencyRefdataObject()))
	    {
	        $oCurrency = Refdata::GetInstance(REFDATA_CURRENCY);
	        $oCurrency->GetByType();
	        $this->SetCurrencyRefdataObject($oCurrency);
	    }

	    if ($ISO4217)
	    {
	        if ($this->currency_id == 292)
	        {
	            return "USD";
	        } elseif ($this->currency_id == 290)
	        {
	            return "GBP";
	        } elseif ($this->currency_id == 291)
	        {
	            return "EUR";
	        }
            
	    } else {
	       return $this->GetCurrencyRefdataObject()->GetValueById($this->currency_id);
	    }
	}
	
	public function GetPriceFromLabel() 
	{

	    if (!is_object($this->GetCostsRefdataObject())) {
	        $oPrice = Refdata::GetInstance(REFDATA_APPROX_COST);
	        $oPrice->SetOrderBySql(' sort_order ASC');
	        $oPrice->GetByType();
	        $this->SetCostsRefdataObject($oPrice);
	    }

	    return $this->GetCostsRefdataObject()->GetValueById($this->price_from_id);
	}

	public function GetPriceToLabel() 
	{

	    if (!is_object($this->GetCostsRefdataObject())) {

	        $oPrice = Refdata::GetInstance(REFDATA_APPROX_COST);
	        $oPrice->SetOrderBySql(' sort_order ASC');
	        $oPrice->GetByType();
	        $this->SetCostsRefdataObject($oPrice);
	    }
	    
	    return $this->GetCostsRefdataObject()->GetValueById($this->price_to_id);
	}

	public function GetReviewRating()
	{
	    if (!isset($this->review_count) || !isset($this->review_rating))
	    {
    	    $oReview = new Review();
    	    $aReview = $oReview->GetReviewRating($this->GetId(),$this->GetLinkTo(), 1);

    	    $this->review_count = isset($aReview['count']) ? $aReview['count'] : 0;
    	    $this->review_rating = isset($aReview['rating']) ? $aReview['rating'] : null;
	    }
	}

	public function GetReviewCount()
	{
	    return $this->review_count;
	}

	public function GetRating()
	{
	    return $this->review_rating;
	}

	public function SetOrgTypeRefdataObject($oRefdata) {
		$this->oOrgTypeRefdataObject = $oRefdata;
	}
	
	public function GetOrgTypeRefdataObject() {
		return $this->oOrgTypeRefdataObject;
	}
	
	public function SetDurationRefdataObject($oRefdata) {
		$this->oDurationRefdataObject = $oRefdata;
	}
	
	public function GetDurationRefdataObject() {
		return $this->oDurationRefdataObject;
	}

	public function GetDurationFromId() {
		return $this->duration_from_id;
	}
	
	public function GetDurationToId() {
		return $this->duration_to_id;
	}
	
	public function GetDurationFromLabel() {
		
		if (!is_object($this->GetDurationRefdataObject())) {
			$oDuration = Refdata::GetInstance(REFDATA_DURATION);
			$oDuration->SetOrderBySql(' id ASC');
			$oDuration->GetByType();
			$this->SetDurationRefdataObject($oDuration);
		}
		
		return $this->GetDurationRefdataObject()->GetValueById($this->duration_from_id);
	}
	
	public function GetDurationToLabel() {

		if (!is_object($this->GetDurationRefdataObject())) {
			$oDuration = Refdata::GetInstance(REFDATA_DURATION);
			$oDuration->SetOrderBySql(' id ASC');
			$oDuration->GetByType();
			$this->SetDurationRefdataObject($oDuration);
		}

		return $this->GetDurationRefdataObject()->GetValueById($this->duration_to_id);		
	}

	public function SetDurationFrom($i) {
		$this->duration_from = $i;
		$this->duration_from_id = $this->DurationWeeksToRefdataLabels($i);
	}
	
	public function GetDurationFrom() {
		return $this->duration_from;
		
	}
	
	public function SetDurationTo($i) {
		$this->duration_to = $i;
		$this->duration_to_id = $this->DurationWeeksToRefdataLabels($i);
	}
	
	public function GetDurationTo() {
		return $this->duration_to;
	}
	
	public function DurationWeeksToRefdataLabels($weeks) {
		$aDuration2WeeksMapping = array(
				0 => 116,
				1 => 117,
				2 => 118,
				3 => 119,
				4 => 120,
				6 => 121,
				8 => 122,
				12 => 123,
				16 => 124,
				36 => 125,
				52 => 126,
				5200 => 127
		);
		
		return $aDuration2WeeksMapping[$weeks];
		
	}
	
	public function SetCurrencyRefdataObject($oRefdata) {
		$this->oCurrencyRefdataObject = $oRefdata;
	}
	
	public function GetCurrencyRefdataObject() {
		return $this->oCurrencyRefdataObject;
	}

	public function SetCostsRefdataObject($oRefdata) {
		$this->oCostsRefdataObject = $oRefdata;
	}
	
	public function GetCostsRefdataObject() {
		return $this->oCostsRefdataObject;
	}
	
	public function SetNumberOfRefdataObject($oRefdata) {
		$this->oNumberOfRefdataObject = $oRefdata;
	}
	
	public function GetNumberOfRefdataObject() {
		return $this->oNumberOfRefdataObject;
	}		
	
	public function GetCountryArray($idx = NULL) {
		if (is_numeric($idx)) {
			return $this->country_array[$idx];
		} else {
			return $this->country_array;
		}
	}

	
	/*
	 * Must be over-ridden by specialised derived classes (ProfilePlacement,ProfileCompany)
	 * so these can inject type specific profile properties into template 
	 * @note - should be declared abstract protected but i cant get this to work properly
	 * 
	 */
	public function LoadTemplate($sFilename) {		
	}

	public function Render() {
	}

	public function GetProfileTypeLabel($oProfile) {

		// its a tour using a general profile
		if (is_array($oProfile->GetCategoryArray()) && in_array(2,$oProfile->GetCategoryArray()) && !in_array(0,$oProfile->GetCategoryArray())) {
			return "Tour";
		}
		
		switch($oProfile->GetType()) {
			case PROFILE_COMPANY :	
			case PROFILE_VOLUNTEER_PROJECT :
				return "Company";
				break;
			case PROFILE_PLACEMENT :
			case PROFILE_VOLUNTEER :
			case PROFILE_TEACHING :
				return "Program";
				break;
			case PROFILE_TOUR :
				return "Tour";
				break;
			case PROFILE_JOB :
				return "Job";
				break;
			case PROFILE_SEASONALJOBS :
				return "Employer";
				break;				
			case PROFILE_SUMMERCAMP :
				return "Summer Camp";
				break;
			default:
				return "Profile";		
		}
		
	}
        
}

?>
