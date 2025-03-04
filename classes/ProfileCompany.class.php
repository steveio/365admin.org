<?php


define("CK_EDITOR_PROFILE_INTRO_DT","2012-12-29");


/* Company profile field id's */
define('PROFILE_FIELD_COMP_PROFILE_TYPE_ID','profile_type');
define('PROFILE_FIELD_COMP_TITLE','title');
define('PROFILE_FIELD_COMP_DESC_SHORT','desc_short');
define('PROFILE_FIELD_COMP_DESC_LONG','desc_long');
define('PROFILE_FIELD_COMP_URL','url');
define('PROFILE_FIELD_COMP_EMAIL','email');
define('PROFILE_FIELD_COMP_APPLY_URL','apply_url');
define('PROFILE_FIELD_COMP_ADDRESS','address');
define('PROFILE_FIELD_COMP_COUNTRY_ID','country_id');
define('PROFILE_FIELD_COMP_STATE_ID','state_id');
define('PROFILE_FIELD_COMP_LOCATION','location');
define('PROFILE_FIELD_COMP_TELEPHONE','tel');

/* Company profile - admin options */
define('PROFILE_FIELD_COMP_PROD_TYPE','prod_type');
define('PROFILE_FIELD_COMP_LISTING_TYPE','listing_type');
define('PROFILE_FIELD_COMP_LISTING_START_DATE','listing_start_date');
define('PROFILE_FIELD_COMP_PROFILE_QUOTA','profile_quota');
define('PROFILE_FIELD_COMP_PROFILE_OPTIONS','prof_opt');
define('PROFILE_FIELD_COMP_ENQUIRY_OPTIONS','enq_opt');


/* Extended profile - general company profile */
define('PROFILE_FIELD_COMP_GENERAL_PLACEMENT_INFO','job_info');
define('PROFILE_FIELD_COMP_GENERAL_DURATION','duration');
define('PROFILE_FIELD_COMP_GENERAL_COSTS','costs');




/*
 * Company Profile
 * 
 * 
 */


class CompanyProfile extends AbstractProfile {

	protected $profile_type;
	
	protected $title;
	protected $url_name;
	protected $desc_short;
	protected $desc_long;
	protected $url;
	protected $apply_url;
	protected $email;
	protected $tel;
	protected $fax;
	protected $address;
	protected $location;
	protected $state_id;
	protected $country_id;
	protected $logo_url;  // @deprecated
	protected $logo_banner_url; // @deprecated
	protected $img_url1; // @deprecated
	protected $img_url2; // @deprecated
	protected $img_url3; // @deprecated
	protected $video;	
	protected $keywords;
	protected $active;
	protected $homepage; // @deprecated
	protected $prod_type;
	protected $profile_quota;
	protected $profile_filter_from_search;
	protected $profile_dormant;
	protected $enq_opt;
	protected $prof_opt;
	protected $aEnqOpt;
	protected $aProfOpt;
	protected $bHasListingRecord;
	protected $profile_count;
	protected $user_act_exists;
	protected $status;
	protected $added_date;
	protected $last_updated;
	protected $last_indexed;
	protected $last_indexed_solr;
	protected $keyword_exclude; /* word list to exlude from indexer */
	
	/* @todo - migrate to a specialised derived class (eg VolunteerCompanyProfile) */
	protected $duration;
	protected $costs;
	protected $job_info;
	
	/*
	 * Profile Version Details
	 * 
	 * Specific versions of some profile fields (title, short_desc, full_desc) can be created
	 * 
	 */
	private $iProfileVersionToFetch; // int (optional) version_id to fetch specific field data for if present
	private $aProfileVersionData; // array optional profile version field data 

	private $sSubTypeFields; /* SQL additional fields to return for profile sub-types */
	private $sSubTypeTable; /* SQL profile sub-type table name */
	

	public function __Construct() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		$this->SetType(PROFILE_COMPANY);
		$this->SetLinkTo("COMPANY");
		
		
		$this->aImage = array();
		$this->aEnqOpt = array();
		$this->aProfOpt = array();
		
		$this->iProfileVersionToFetch = NULL;
		$this->aProfileVersionData = array();
		$this->iProfileVersionNoDefault = FALSE;
		
		$this->SetListingRecordFl(false);
		
	}

	/*
	 * Get Company Profile Type (eg PROFILE_COMPANY, PROFILE_SUMMERCAMP etc )
	 * 
	 * @param int company id
	 * @return mixed int profile type on SUCCESS, FALSE on failure
	 */
	public static function GetTypeById($iCompanyId) {
	
		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."()");		
		
		global $db, $_CONFIG;
		
		if (!is_numeric($iCompanyId)) return FALSE;
		
		return $db->getFirstCell("SELECT profile_type FROM ".$_CONFIG['company_table']." WHERE id = ".$iCompanyId);

	}
	
	// used by request router to validate request and setup page header
	public function GetByUrlName($url_name)
	{
	    global $db ,$_CONFIG;
	    
	    $sql = "SELECT id,title, desc_short, profile_type as type FROM ".$_CONFIG['company_table']." WHERE url_name = '".$url_name."'";
	    
	    $db->query($sql);
	    if ($db->getNumRows() == 1) {
	        return $aRes = $db->getRow();
	    } else {
	        throw new NotFoundException("404 Company Profile ".$url_name." not found");
	    }
	}

	public static function GetIdByUrlName($url_name) {
	    global $db, $_CONFIG;
	    
	    return $db->getFirstCell("SELECT id FROM ".$_CONFIG['company_table']." WHERE url_name = '".$url_name."'");
	    
	}

	public function GetUrlNameById($iCompanyId) {
		global $db, $_CONFIG;
		
		if (!is_numeric($iCompanyId)) return FALSE;
		
		return $db->getFirstCell("SELECT url_name FROM ".$_CONFIG['company_table']." WHERE id = ".$iCompanyId);
		
	}

	public function GetCompanyId() { 
		return $this->id;
	}
	
  	public function GetTitle($trunc = 0) {
  		
  		$title = $this->title;
  		
  		if ($trunc >= 1) {
  			$s = $title;
			if (strlen($s) > $trunc) {
				$s = $s." ";
				$s = substr($s,0,$trunc);
				$s = $s."...";
			}
			return $s;	
  		} else {
 			return $title;
  		}
  	}

  	public function SetTitle($sTitle) {
 		$this->title = $sTitle;
  	}

  	/*
  	 * So that we can call $oProfile->GetFullyQualifiedTitle() on comp or placements
  	 *  comp returns <company-name>
  	 *  placement return <company-name> : <placement-name>
  	 * 
  	 */
  	public function GetFullyQualifiedTitle() {
  		return $this->title;
  	}
  	
  	public function GetCompanyName() {
  		return $this->title;
  	}
  	
  	public function GetUrlName() {
  		return $this->url_name;
  	}

	public function SetUrlName($sUrlName) {
  		$this->url_name = $sUrlName;
  	}

  	public function GetUri()
  	{
  	    global $_CONFIG;
  	    return "/".$_CONFIG['company_home']."/".$this->GetUrlName();
  	}
  	
  	public function GetDescShort($trunc = 0) {

  		$desc_short = (is_numeric($this->GetProfileVersionIdToFetch())) ? $this->GetFieldByProfileVersion('desc_short') : $this->desc_short;

  		if ($this->GetLastUpdatedAsTs() < strtotime(CK_EDITOR_PROFILE_INTRO_DT) ) {
  			$desc_short = nl2br($desc_short);
  		}
  		
  		if ($trunc >= 1) {
  			$s = $desc_short;
			if (strlen($s) > $trunc) {
				$s = $s." ";
				$s = substr($s,0,$trunc);
				$s = substr($s,0,strrpos($s,' '));
				$s = $s."...";
				$s = strip_tags($s); // in case we left an open <b> tag
			}
			return $s;	
  		} else {
 			return $desc_short;
  		}
  	}
  	
  	private function GetLastUpdatedAsTs() {
  		$date = str_replace('/', '-', $this->last_updated);
  		return strtotime($date);
  	}
  	 

  	public function SetDescShort($sDescShort) {
  		$this->desc_short = $sDescShort;
  	}
  	
  	public function GetDescLong() {
  		$desc_long = (is_numeric($this->GetProfileVersionIdToFetch())) ? $this->GetFieldByProfileVersion('desc_long') : $this->desc_long;
  	
  		if ($this->GetLastUpdatedAsTs() < strtotime(CK_EDITOR_PROFILE_INTRO_DT) ) {
  			$desc_long = nl2br($desc_long);
  		}
  		return $desc_long;
  	
  	}

  	public function GetUrl() {
  		if (!preg_match("/http/",$this->url)) {
			$this->url = "http://".$this->url;
		}
		return $this->url;
  	}

  	public function GetCompUrlName()
  	{
        return $this->GetUrlName();  	    
  	}

  	/* returns the url to the company profile */
  	public function GetCompanyProfileUrl() {
  	    global $_CONFIG;
  	    return $_CONFIG['url']."/".$_CONFIG['company_home']."/".$this->GetUrlName();
  	}
  	
  	public function GetApplyUrl() {
  		return $this->apply_url;
  	}
  	
  	public function GetEmail() {
  		return $this->email;
  	}
  	
  	public function GetTel() {
  		return $this->tel;
  	}
  	
  	public function GetFax() {
  		return $this->fax;
  	}
  	
  	public function GetAddress() {
  		return $this->address;
  	}

  	public function GetLocation() {
  		return $this->location;
  	}
  	
  	public function GetStateId() {
  		return $this->state_id;
  	}

  	public function GetStateName() {
  	    return $this->state_name;
  	}

  	public function GetStateLabel() {
  	    if (is_numeric($this->state_id) && !is_object($this->oState)) {
  	        $this->oState = new Refdata(REFDATA_US_STATE);
  	        $this->oState->GetByType();
  	        $this->state_label = $this->oState->GetValueById($this->state_id);
  	    }
  	    return $this->state_label;
  	}

  	public function GetCountryId() {
  		return $this->country_id;
  	}
  	
	public function GetProfileUrl() {
  		global $_CONFIG;  		
  		return $_CONFIG['url']."/company/".$this->GetUrlName();
  	}

	public function GetLogoUrlTxt() {
  		return $this->logo_url;
	}
	
	public function GetLogoBannerUrl() {
		return $this->logo_banner_url;
	}
  	
	public function GetActiveFl() {
		return $this->active;
	}
	
	public function GetVideo() {
		return $this->video;
	}
	
	/*
	 * @deprecated
	 */
	public function GetHomepageFl() {
		return $this->homepage;
	}
	
	public function GetListingType() {
		return $this->prod_type;
	}

	public function SetListingLevel($listing_level) {
	    $this->prod_type = $listing_level;
	}

	public function GetProdType() {
		return $this->GetListingType();
	}
	
	public function GetListingTypeLabel() {
		
		switch($this->GetListingType()) {
			case SPONSORED_LISTING :
				return "Sponsored Listing";
				break; 
			case ENHANCED_LISTING :
				return "Enhanced Listing";
				break; 
			case BASIC_LISTING :
				return "Basic Listing";
				break;
			default :				
				return "Free Listing";
				break;
		}		
	}

	
	
	public function GetListingRecordFl() {
		return $this->bHasListingRecord;
	}
	
	public function SetListingRecordFl($bVal) {
		$this->bHasListingRecord = $bVal;
	}
	
	
	public function GetProfileQuota() {
		return $this->profile_quota;
	}

	public function SetProfileQuota($iProfileQuota) {
		$this->profile_quota = $iProfileQuota;
	}

	public function SetProfileFilterFromSearch($bFilter)
	{
		$this->profile_filter_from_search = $bFilter;
	}

	public function GetFilterFromSearch()
	{
	    return ($this->profile_filter_from_search == 't') ? true : false;
	}

	public function GetProfileFilterFromSearch()
	{
		return $this->profile_filter_from_search;		
	}

	public function SetProfileDormant($bFilter)
	{
	    $this->profile_dormant = $bFilter;
	}
	
	public function GetProfileDormant()
	{
	    return $this->profile_dormant;
	}

	public function GetProfileDormantBool()
	{
	    return ($this->profile_filter_from_search == 't') ? true : false;
	}

	public function GetProfileCount() {
		return $this->profile_count;
	}

	public function SetProfileCount() {
		$oProfile = new PlacementProfile();
		$this->profile_count = $oProfile->GetProfileCount("BY_COMPANY",$this->GetId());
	}
	
	public function GetKeywordExclude() {
		return $this->keyword_exclude;
	}
	
	public function GetStatus() {
		return $this->status;
	}
	
	public function GetUserAccountExists() {
		return $this->user_act_exists;
	}
	
	public function GetAdded() {
		return $this->added_date;
	}

	
	/* bitmap indicating which enquiries are enabled for this profile */
	public function GetEnquiryOptionBitmap() {
		return $this->enq_opt;
	}

	public function SetEnquiryOptionBitmap($s) {
		$this->enq_opt = $s;
	}
	
	public function SetEnquiryOption() {
		for($i=0;$i<7;$i++){
			$this->aEnqOpt[$i] = (int) substr($this->enq_opt,$i,1);
		}		
	}

	public function GetEnquiryOption() {
		$this->SetEnquiryOption();
		return $this->aEnqOpt;	
	}
	
	public function HasEnquiryOption($sOpt) {
		if(count($this->aEnqOpt) < 1) $this->SetEnquiryOption();
		switch($sOpt) {
			case ENQUIRY_GENERAL :
				return ($this->aEnqOpt[0] == 1) ? true : false;
				break;
			case ENQUIRY_BOOKING :
				return ($this->aEnqOpt[1] == 1) ? true : false;
				break;
			case ENQUIRY_JOB_APP :
				return ($this->aEnqOpt[2] == 1) ? true : false;
				break;
		}
	}
	
	/* bitmap indicating which profile types can be posted by this company */
	public function GetProfileOptionBitmap() {
		return $this->prof_opt;
	}
	
	public function GetProfileOptionBitmapFromDB() {
		global $db,$_CONFIG;		
		$this->SetProfileOptionBitmap($db->getFirstCell("SELECT prof_opt FROM ".$_CONFIG['company_table']." WHERE id = ".$this->GetId()));
	}
	
	public function SetProfileOptionBitmap($s) {
		$this->prof_opt = $s;
	}
	
	/* profile type bitmap structured as array */
	public function SetProfileOption() {
		for($i=0;$i<7;$i++){
			$this->aProfOpt[$i] = (int) substr($this->prof_opt,$i,1);
		}		
	}
	
	public function GetProfileOption() {
		$this->SetProfileOption();
		return $this->aProfOpt;	
	}

	public function HasProfileOption($sOpt) {
		if(count($this->aProfOpt) < 1) $this->SetProfileOption();
		
		switch($sOpt) {
			case PROFILE_VOLUNTEER :
				return ($this->aProfOpt[0] == 1) ? true : false;
				break;
			case PROFILE_TOUR :
				return ($this->aProfOpt[1] == 1) ? true : false;
				break;
			case PROFILE_JOB :
				return ($this->aProfOpt[2] == 1) ? true : false;
				break;
		}
	}
	
	public function GetEnabledProfileOption() {

		if ($this->aProfOpt[0] == 1) return PROFILE_VOLUNTEER;
		if ($this->aProfOpt[1] == 1) return PROFILE_TOUR;
		if ($this->aProfOpt[2] == 1) return PROFILE_JOB;
		
	}
	
	public function GetProfileTypeCount() {
		
		if(count($this->aProfOpt) < 1) $this->SetProfileOption();
		
		$i = 0;
		foreach($this->aProfOpt as $v) {
			if ($v == "1") $i++;
		}
		return $i;
	}
	
	
	
	/*
	 * @todo - migrate methods below (duration, costs etc) to specialised sub class
	 * 
	 */
	public function GetDuration() {
		return $this->duration;
	}

	public function GetCosts() {
		return $this->costs;
	}
	
	public function GetPlacementInfo() {
		return $this->job_info;
	}
		
  	
	/*
	 * To allow derived class to setup GetProfileById SQL
	 * @param string SQL list of fields (including table alias)  
	 */
	protected function SetSubTypeFields($sSubTypeFields) {
		$this->sSubTypeFields = $sSubTypeFields;
	}

	/*
	 * To allow derived class to setup GetProfileById SQL
	 * @param string SQL profile subtype table name (including alias to match SetSubTypeFields() above   
	 */
	protected function SetSubTypeTable($sSubTypeTable) {
		$this->sSubTypeTable = $sSubTypeTable;
	}
	
	public function GetIdByUri($url_name) {
		
		global $db,$_CONFIG;
		
		$sql = "SELECT id FROM ".$_CONFIG['company_table']." WHERE url_name = '".$url_name."'";
        $db->query($sql);
        if ($db->getNumRows() == 1) {
			$row = $db->getRow();
            return $row['id'];
        }
	}
	
	public function GetById($id,$return = "PROFILE")
	{
	    return $this->GetProfileById($id, $return);
	}

	public function GetProfileById($id,$return = "ARRAY") {
		
		global $db,$_CONFIG;

		if (!is_numeric($id)) return false;

		$sSql = "SELECT
						c.oid
						,c.id
						,c.profile_type
						,c.title
						,c.url_name
						,c.desc_short
						,c.desc_long
						,c.url
						,c.apply_url
						,c.email
						,c.tel
						,c.fax
						,c.address
						,c.location
						,c.state_id
						,s.name as state_name
						,c.country_id
						,cty.name as country_name
						,c.video
						,c.keywords
						,c.active
						,c.prod_type
						,c.profile_quota as profile_quota
						,c.enq_opt
						,c.prof_opt
                        ,c.profile_filter_from_search
						,c.status
						,c.job_info
						,CASE
							WHEN (select 1 from euser u where u.company_id = c.id limit 1)=1 THEN 1
							ELSE 0
						END as user_act_exists
						,to_char(c.added,'DD/MM/YYYY HH24:MM') as added_date
						,to_char(c.last_updated,'DD/MM/YYYY HH24:MM') as last_updated
						,to_char(c.last_indexed_solr,'DD/MM/YYYY HH24:MM') as last_indexed_solr
						$this->sSubTypeFields
					FROM
						".$_CONFIG['company_table']." c
						LEFT OUTER JOIN country cty ON c.country_id = cty.id
						LEFT OUTER JOIN state s ON c.state_id = s.id
						$this->sSubTypeTable
					WHERE
						c.id = $id
				";

			$db->query($sSql);
			
			if ($db->getNumRows() == 1) {
				if ($return == "ARRAY") {
					return $aResult = $db->getRow();
				} elseif ($return == "OBJECT") {
    			    return $oResult = $db->getObject();
				} elseif ($return == "PROFILE") {

					$this->SetFromObject($db->getObject());
					if ($this->GetId() != $id) throw new Exception(ERROR_COMPANY_PROFILE_NOT_FOUND.$id);
					$this->GetImages();
					$this->GetCategoryInfo();
					$this->GetCountryInfo();
					$this->GetActivityInfo();
					$this->GetProfileVersionData();
					$this->SetProfileCount();

					return TRUE;
				}				
			} else {
				throw new Exception(ERROR_COMPANY_PROFILE_NOT_FOUND.$id);
			}
		
	}
	

	public function GetCompanyListByName($name) {
		
		global $db,$_CONFIG;
		
		$sql = "SELECT id,title,url_name FROM ".$_CONFIG['company_table']." WHERE title ilike '%".$name."%' ORDER BY title ASC";
        $db->query($sql);
        $aResult = array();
        $i = 0;
        if ($db->getNumRows() >= 1) {
			$rows = $db->getRows();
			foreach($rows as $row) {
            	$aResult[$i++] = $row;
			}	
        }
        return $aResult;
	}
	
	
	/*
	 * Controller for ADD/UPDATE requests 
	 * 
	 * @param array reference company params
	 * @param array reference for response structure for passing back messages / id
	 * @param boolean redirect - issue a http header after add?
	 * @param boolean approved - flag to indicate profile approval status
	 * 
	*/
	public function DoAddUpdate(&$c,&$aResponse,$bRedirect = true,$bApproved,$bTx = true) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $oAuth,$_CONFIG,$db;
		
		
		/* validate the submitted params */
		if (!Validation::ValidateCompany($c,$aResponse)) return false;
	
		/* Sanitize & DB Escape Params */
		Validation::AddSlashes($c);
	
		if ($bTx) $db->query("BEGIN");

		/* set approval status */
		$c['status'] = ($bApproved) ? 1 : 0; // $bApproved is passed in by caller 		
				
		/* set profile / enquiry option bitmap - only admin can update these */
		if ($oAuth->oUser->isAdmin) {
			$c['prof_opt'] = Mapping::GetBitmapFromRequest("prof_opt_",$c);
			$c['enq_opt'] = Mapping::GetBitmapFromRequest("enq_opt_",$c);
		}			
		
		
		if (!is_numeric($c['id'])) { /* ADD a new company */ 			
			
			$mode = "add";
			
			/* generate unique url namespace identifier */
			$oNs = new NameService();
			$c['url_name'] = $oNs->GetUrlName($c['title'],'company','url_name');
		
			
			/* add the company */
			$c['id'] = $this->Add($c);

		
			if (is_numeric($c['id'])) {
				
				Logger::DB(2,get_class($this)."::".__FUNCTION__."()","ADD_COMPANY OK : ".$c['id']);
				
				$aResponse['msg'] = "SUCCESS: Added company : ".$c['title']."";
				$aResponse['id'] = $c['id'];
				$aResponse['url_name'] = $c['url_name'];
				
				if ($oAuth->oUser->isAdmin) { /* add a new paid listing record */
					CompanyProfile::UpdateListing($c);
				}

				
			} else {
				
				Logger::DB(1,get_class($this)."::".__FUNCTION__."()","ADD_COMPANY FAIL : ".serialize($c));
				
				$aResponse['msg']['sql_update'] = "ERROR: There was a problem adding company, please contact us for assistance.";
				if ($bTx) $db->query("ROLLBACK");
				return false;
			}
		        
		} else { /* UPDATE company */
		
			$mode = "update";

			if ($oAuth->oUser->isAdmin) { /* only admin can update a paid listing */
				CompanyProfile::UpdateListing($c);
			}
			
			
			// non admin can only edit their own company profile
			if ((!$oAuth->oUser->isAdmin) && ($oAuth->oUser->company_id != $c['id'])) {
				$aResponse['msg']['access_rights'] = "ERROR: We don't think you are authorised to edit this company profile.";
				if ($bTx) $db->query("ROLLBACK");
				return false;
			}

			/* update url_name */ 
			$sExistingTitle = stripslashes($db->getFirstCell("SELECT title FROM company WHERE id = ".$c['id'].";"));
			if ($c['title'] != $sExistingTitle) { /* generate a new unique url namespace identifier */
				$oNs = new NameService();
				$c['url_name'] = $oNs->GetUrlName($c['title'],'company','url_name');
				$bUrlChanged = true;			
			} else {
				$c['url_name'] = $db->getFirstCell("SELECT url_name FROM company WHERE id = ".$c['id'].";");
			}						
									
			
			if ($this->Update($c)) { /* update the company */
				
				Logger::DB(2,get_class($this)."::".__FUNCTION__."()","EDIT_COMPANY OK : ".$c['id']);
				
				$aResponse['msg'] = "SUCCESS: Updated company : ".$c['title']."";
				$aResponse['id'] = $c['id'];
				$aResponse['retVal'] = 1;
				
				if ($bUrlChanged) {
					$aResponse['url_change'] = TRUE;
					$aResponse['edit_url'] = BASE_URL."/".ROUTE_COMPANY."/".$c['url_name']."/edit";
				}
				
			} else {
				
				Logger::DB(1,get_class($this)."::".__FUNCTION__."()","EDIT_COMPANY FAIL : ".serialize($c));
				
				$aResponse['msg']['update'] = "ERROR: There was a problem updating company, please contact us for assistance";
				if ($bTx) $db->query("ROLLBACK");
				return false;
			}
		}
		
		
		/* update mappings */

		if (!is_numeric($c['id'])) return false; /* check that we have a valid company id */

                /* save profile version data (if supplied) */
                if (is_array($c['profile_version_data'])) {
                        $this->SaveProfileVersionData($c['profile_version_data'],$c['id']);
                }

		/* update website mapping */
		Mapping::Update($bAdminRequired = true,$sTbl = "comp_website_hp_map",$sKey = "company_id",$c['id'],$aFormValues = $c,$sFormKeyPrefix = "web_",$sKey2 = "website_id");	

		/* update category mapping */
		Mapping::Update($bAdminRequired = false,$sTbl = "comp_cat_map",$sKey = "company_id",$c['id'],$aFormValues = $c,$sFormKeyPrefix = "cat_",$sKey2 = "category_id");

		/* update activity mapping */
		Mapping::Update($bAdminRequired = false,$sTbl = "comp_act_map",$sKey = "company_id",$c['id'],$aFormValues = $c,$sFormKeyPrefix = "act_",$sKey2 = "activity_id");

		/* update country mapping */
		Mapping::Update($bAdminRequired = false,$sTbl = "comp_country_map",$sKey = "company_id",$c['id'],$aFormValues = $c,$sFormKeyPrefix = "cty_",$sKey2 = "country_id");


		
		if ($bTx) $db->query("COMMIT");


		if ($mode == "update") { 
			
			/* refresh cached pages for each site this profile appears on */
			
			$this->UpdateCache($c['url_name']);
			
		}
		
		return true;
		
	}

	public function SetProfileVersionNoDefault($val) {
		$this->iProfileVersionNoDefault = $val;
	}
	
	
	public function SetProfileVersionIdToFetch($id) { 
		$this->iProfileVersionToFetch = $id;
	}
	
	public function GetProfileVersionIdToFetch() {
		return $this->iProfileVersionToFetch;
	}
	
	
	private function GetFieldByProfileVersion($field) {
		
		// return the default field value
		if (!is_numeric($this->GetProfileVersionIdToFetch())) return $this->$field;
		
		// return a specific version of field 
		if (isset($this->aProfileVersionData[$this->GetProfileVersionIdToFetch()]) &&
			isset($this->aProfileVersionData[$this->GetProfileVersionIdToFetch()][$field]) &&
			strlen($this->aProfileVersionData[$this->GetProfileVersionIdToFetch()][$field]) > 1) return $this->aProfileVersionData[$this->GetProfileVersionIdToFetch()][$field];

		if ($this->iProfileVersionNoDefault) {
			return "";
		} else {
			return $this->$field;
		}
	}
	
	private function GetProfileVersionData() {

		global $db;
		
		$sql = "SELECT * FROM profile_version_data WHERE link_to = ".$this->GetGeneralType()." AND link_id = ".$this->GetId();

		$db->query($sql);
		
		if ($db->getNumRows() >= 1) {
			while ($row = $db->getRow()) {
				$this->aProfileVersionData[$row['version_id']]['title'] = stripslashes($row['title']);
				$this->aProfileVersionData[$row['version_id']]['desc_short'] = stripslashes($row['desc_short']);
				$this->aProfileVersionData[$row['version_id']]['desc_long'] = stripslashes($row['desc_long']);
			}
		}
	}
	
	private function SaveProfileVersionData($aProfileVersionData,$cid) {
		
		if (!is_array($aProfileVersionData) || count($aProfileVersionData) < 1) return FALSE;
		
		global $db;
		
		
		foreach($aProfileVersionData as $version_id => $aFields) {

			if (is_numeric($cid)) {

				$sql = "SELECT version_id  FROM profile_version_data WHERE version_id = ".$version_id." AND link_to = ".$this->GetGeneralType()." AND link_id = ".$cid;
				$db->query($sql);

				if ($db->getNumRows() == 1) {
					// delete any existing version data associated with this profile
					$sql = "DELETE FROM profile_version_data WHERE version_id = ".$version_id." AND link_to = ".$this->GetGeneralType()." AND link_id = ".$cid;
					$db->query($sql);
				}
			}

			if ($aFields['title'] == '' && $aFields['desc_short'] != '' && $aFields['desc_long'] != '') return TRUE;
		
			// now write a version row for each submitted field
			$sql = "INSERT INTO profile_version_data (
		
														version_id,
														link_to,
														link_id,
														title,
														desc_short,
														desc_long
													) VALUES (
														".$version_id.",
														".$this->GetGeneralType().",
														".$cid.",
														'".addslashes($aFields['title'])."',
														'".addslashes($aFields['desc_short'])."',
														'".addslashes($aFields['desc_long'])."'
													);";
			
			$db->query($sql);
			
			if ($db->getAffectedRows() == 1) {
				//Logger::DB(3,get_class($this)."::".__FUNCTION__."()","Affected rows: ".$db->getAffectedRows().", ". $sql);
			} else {
				Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$db->last_error());
			}


			 
		}
		
		
	}
	
	
	private function UpdateCache($url_name) {

		global $db;
		
		// get id's and hostname of each website this page is registered with in cache table
		$sUri = "/company/".$url_name;
		$aSiteId = Cache::GetSiteIdsByUri($sUri);
		
		if (!is_array($aSiteId) || count($aSiteId) < 1) return FALSE;
		
		$oWebsite = new Website($db);
		$aHostname = $oWebsite->GetHostnames(" WHERE id IN (".implode(",",$aSiteId).")");
	
		if (count($aSiteId) >= 1) {
			
			$delay = (count($aSiteId) > 1) ? TRUE : FALSE; // if we are updating multiple cached pages set delay to TRUE
			
			foreach($aSiteId as $website_id) {
				
				$sUrl = $aHostname[$website_id]."/company/".$url_name;
				
				Cache::Generate($sUrl,$sUri,$website_id, $delay);
				
			}
			
		}
		
	}


	public function Add($p) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $db;
		
		if (!is_array($p)) return false;

		/* these should normally be supplied - default them in case they aren't */
		if (!is_numeric($p['profile_type'])) $p['profile_type'] = PROFILE_COMPANY;
		if (!is_numeric($p['prod_type'])) $p['prod_type'] = FREE_LISTING; 
		if (!is_numeric($p['profile_quota'])) $p['profile_quota'] = 0;
		$p['profile_filter_from_search'] = ($p['profile_filter_from_search'] == true) ? 'true' : 'false';

		if (trim($p['video']) > 1) {  
			$video_str = "'".htmlspecialchars_decode(trim($p['video']))."'"; 
		} else {
			$video_str = "NULL";
		}
		
		$sql = "INSERT INTO company (
					 id
					 ,profile_type
					 ,title
					 ,url
					 ,email
					 ,tel
					 ,fax
					 ,desc_short
					 ,desc_long
					 ,job_info
					 ,apply_url
					 ,video
					 ,address
					 ,location
					 ,state_id
					 ,country_id
					 ,costs
					 ,duration
					 ,keywords
					 ,added
					 ,last_updated
					 ,last_indexed
					 ,last_indexed_solr
					 ,prod_type
					 ,profile_quota
					 ,profile_filter_from_search
					 ,url_name
					 ,prof_opt
					 ,enq_opt
					 ,status
					 ,keyword_exclude
					 ) VALUES (
						nextval('company_seq')
						,".$p['profile_type']."
						,'".$p['title']."'
						,'".$p['url']."'
						,'".$p['email']."'
						,'".$p['tel']."'
						,'".$p['fax']."'
						,'".$p['desc_short']."'
						,'".$p['desc_long']."'
						,'".$p['job_info']."'
						,'".$p['apply_url']."'
						,".$video_str."
						,'".$p['address']."'
						,'".$p['location']."'
						,".$p['state_id']."
						,".$p['country_id']."
						,'".$p['costs']."'
						,'".$p['duration']."'
						,'".$p['keywords']."'
						,now()::timestamp
						,now()::timestamp
						,now() - interval '1 hour' 
						,now() - interval '1 hour'
						,'".$p['prod_type']."'
						,'".$p['profile_quota']."'
						,".$p['profile_filter_from_search']."
						,'".$p['url_name']."'
						,'".$p['prof_opt']."'
						,'".$p['enq_opt']."'
						,".$p['status']."
						,'".$p['keyword_exclude']."'
					)";
		
		$db->query($sql);
	
		if ($db->getAffectedRows() == 1) {
			$oid = $db->getLastOid();
			return $db->getFirstCell("SELECT id FROM company WHERE oid = $oid");
		}
	
	}
		
	public function Update($p) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;
		
		if (!is_array($p)) return false;

		$sApproved = ($p['status'] == 1) ? ",status=1" : "status=0";
		
		$p['profile_filter_from_search'] = ($p['profile_filter_from_search'] == true) ? 't' : 'f';
		$p['active'] = ($p['active'] == true) ? 't' : 'f';

		if (trim($p['video']) > 1) {
			$video_str = "'".htmlspecialchars_decode(trim($p['video']))."'";
		} else {
			$video_str = "NULL";
		}

		$sql = "
			UPDATE company SET
				profile_type = ".$p['profile_type']." 
				,title = '".$p['title']."'
				,url_name = '".$p['url_name']."'
				,url = '".$p['url']."'
				,email = '".$p['email']."'
				,tel = '".$p['tel']."'
				,fax = '".$p['fax']."'
				,desc_short = '".$p['desc_short']."'
				,desc_long = '".$p['desc_long']."'
				,job_info = '".$p['job_info']."'
				,apply_url = '".$p['apply_url']."'
				,video = ".$video_str."
				,address = '".$p['address']."'
				,location = '".$p['location']."'
				,state_id = ".$p['state_id']."
				,country_id = ".$p['country_id']."
				,sub_type = '".$p['sub_type']."'
				,costs = '".$p['costs']."'
				,duration = '".$p['duration']."'
				,keywords = '".$p['keywords']."'
				,prod_type = '".$p['prod_type']."'
				,profile_quota = '".$p['profile_quota']."'
				,profile_filter_from_search = '".$p['profile_filter_from_search']."'
				,prof_opt = '".$p['prof_opt']."'
				,enq_opt = '".$p['enq_opt']."'
				,keyword_exclude = '".$p['keyword_exclude']."'
				,last_updated = now()::timestamp
				$sApproved
				WHERE
				 id = ".$p['id']."
		";

		$db->query($sql);
		
		if ($db->getAffectedRows() == 1) {
			return true;
		}	
	}
	
	/* only implemented for extending classes */
	protected function AddSubTypeRecord($c) {
		return TRUE;
	}
	
	/* only implemented for extending classes */
	protected function UpdateSubTypeRecord($c) {
		return TRUE;
	}

	/* only implemented for extending classes */
	public function SetTypeSpecificFormValues() {}

	
	/*
	 * UpdateListing - create / update a paid listing record
	 * 
	 * @param array $a - must contain :
	 * 						$a['listing_type'] (listing type code eg BASIC_6)
	 * 						$a['ListingMonth']
	 * 						$a['ListingYear']
	 * 						$a['id'] (company id)
	 * 
	 */
	public static function  UpdateListing($a) {
		
		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."()");
		
		global $db,$oAuth,$_CONFIG;
		
		/*
		 * Only admin can edit paid listing records
		 */
			
		/* all listing options */
		$aListingOption = ListingOption::GetAll($_CONFIG['site_id'],$currency = 'GBP',$from = 0, $to = 3);

		/* compose new listing start date */
		$sStartDay = (strlen($a['ListingDay']) > 1) ? $a['ListingDay'] : "01";
		$sNewListingStartDate = $sStartDay."-".$a['ListingMonth']."-".$a['ListingYear'];

		/* has an existing listing changed */				
		$oCurrentListing = new Listing();	
		if ($oCurrentListing->GetCurrentByCompanyId($a['id'])) {
			
			if (DEBUG) Logger::Msg("Existing Listing = TRUE");
			if (DEBUG) Logger::Msg("CurrentStartDt : ".$oCurrentListing->GetStartDate());
			if (DEBUG) Logger::Msg("NewStartDt : ".$sNewListingStartDate);
			if (DEBUG) Logger::Msg("CurrentCode : ".$oCurrentListing->GetCode());
			if (DEBUG) Logger::Msg("NewCode : ".$a['listing_type']);
			
			if (($a['listing_type'] != $oCurrentListing->GetCode()) ||
				($oCurrentListing->GetStartDate() != $sNewListingStartDate)) {
					if (DEBUG) Logger::Msg("Listing change = TRUE");
					$oNewListing = new Listing();
					$oNewListing->SetActiveByCompanyId($a['id'],"F"); /* all existing listing records (current?) status = false */
					$oNewListing->SetCompanyId($a['id']);
					$oNewListing->SetFromArray(ListingOption::GetByCode($a['listing_type']));
					$oNewListing->SetStartDate($sNewListingStartDate);
					if (!$oNewListing->Add()) {
						if (DEBUG) Logger::Msg("Add new listing ERROR");
						return false;
					} else {
						if (DEBUG) Logger::Msg("Add new listing OK");	
					}
						
			} else {
				if (DEBUG) Logger::Msg("Listing change = FALSE");
			}
		} else {
			/* insert details of new paid listing */ 
			if (DEBUG) Logger::Msg("Existing Listing = FALSE");
			
			if ($aListingOption[$a['listing_type']]['type'] > FREE_LISTING) {

				if (DEBUG) Logger::Msg("New PAID Listing = TRUE");

				$oNewListing = new Listing();
				$oNewListing->SetCompanyId($a['id']);
				$oNewListing->SetFromArray(ListingOption::GetByCode($a['listing_type']));
				$oNewListing->SetStartDate($sNewListingStartDate);
				if (!$oNewListing->Add()) {
					if (DEBUG) Logger::Msg("Add new listing ERROR");
					return false;
				} else {
					if (DEBUG) Logger::Msg("Add new listing OK");	
				}
				
			}
		}
		
		return true;

	}

	/*
	 * @deprecated
	 */
	public function SetImageProcessStatus($iStatus = 0) {
		global $db,$_CONFIG;
		$db->query("UPDATE ".$_CONFIG['company_table'] ." SET img_status = '".$iStatus."' WHERE id = ".$this->GetId());
	}
	
    /*
     * @deprecated
     */
	public function SetLogoProcessFlag($val = 'F') {
		global $db,$_CONFIG;
		$db->query("UPDATE ".$_CONFIG['company_table'] ." SET logo_refresh_fl = '".$val."' WHERE id = ".$this->GetId());
	}
	
	

	public static function Get($type,$id = null,$limit = 4, $fetchmode = FETCHMODE__FULL) {


		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."()");
		
		global $db,$_CONFIG;

		if ($fetchmode == FETCHMODE__FULL)
		{
		  $select = "SELECT * ";
		} else {
		    $select = "SELECT id,title,desc_short,url,prod_type,location,url_name ";
		}
		switch(strtoupper($type)) {
			case "ALL" :
				$sql = "$select FROM ".$_CONFIG['company_table']." WHERE status = 1 order by title asc;";
				break;
			case "INDEX_LIST_DELTA" :
				$sql = "SELECT id FROM ".$_CONFIG['company_table']." WHERE status = 1 AND last_updated > last_indexed";
				break;
			case "INDEX_LIST_DELTA_SOLR" :
				$sql = "SELECT id FROM ".$_CONFIG['company_table']." WHERE status = 1 AND last_updated > last_indexed_solr";
				break;
			case "INDEX_LIST_ALL" :
				$sql = "SELECT id FROM ".$_CONFIG['company_table'];
				break;				
			case "COUNTRY" :
				$sql = "$select FROM ".$_CONFIG['company_table']." c, comp_country_map m WHERE c.status = 1 AND m.country_id = $id AND m.company_id = c.id order by title asc;";
				break;
			case "ACTIVITY" :
				$sql = "$select FROM ".$_CONFIG['company_table']." c, comp_act_map m WHERE c.status = 1 AND m.activity_id = $id AND m.company_id = c.id order by title asc;";
				break;
			case "CATEGORY" :
				$sql = "$select FROM ".$_CONFIG['company_table']." c, comp_cat_map m WHERE c.status = 1 AND m.category_id = $id AND m.company_id = c.id order by prod_type desc, title asc;";
				break;
			case "NAME" :
				$sql = "$select FROM ".$_CONFIG['company_table']." c WHERE c.status = 1 AND c.id = '".$id."' order by title asc;";
				break;
			case "KEYWORD" :
				$sql = "$select FROM ".$_CONFIG['company_table']." c WHERE c.status = 1 AND c.id in (".implode(",",$id).") order by title asc;";
				break;
			case "ID" :
				$sql = "SELECT id,title,desc_short,url,prod_type,location,url_name FROM ".$_CONFIG['company_table']." c WHERE c.status = 1 AND c.id in (".implode(",",$id).");";
				break;
			case "ID_SORTED" :
				$sql = "$select FROM ".$_CONFIG['company_table']." c WHERE c.status = 1 AND c.id in (".implode(",",$id).") ORDER BY prod_type desc";
				break;
			case "OID_SORTED" :
			    $sql = "$select FROM ".$_CONFIG['company_table']." c WHERE c.status = 1 AND c.oid in (".implode(",",$id).") ORDER BY prod_type desc";
			    break;
			case "RECENT" :
				$sql = "SELECT id,title,desc_short,url_name,status, to_char(added,'DD/MM/YYYY') as added_date, to_char(last_updated,'DD/MM/YYYY') as updated_date FROM ".$_CONFIG['company_table']." ORDER BY last_updated desc LIMIT 20";
				break;
			case "RECENT_PAID_LISTING" :
				$sql = "SELECT id,title,desc_short,url_name,status, to_char(added,'DD/MM/YYYY') as added_date, to_char(last_updated,'DD/MM/YYYY') as updated_date FROM ".$_CONFIG['company_table']." WHERE prod_type >= 1 AND prod_type <= 2 ORDER BY last_updated DESC LIMIT ".$limit;
				break;
			case "HOMEPAGE_FEATURED" :
				$sql = "SELECT c.id,c.title,c.desc_short,c.url_name,c,c.url FROM ".$_CONFIG['company_table']." c, comp_website_hp_map m WHERE m.website_id = ".$_CONFIG['site_id']." AND m.company_id = c.id ORDER BY random() LIMIT ".$limit;
				break;			
			case "SPONSORED" :
				$sql = "SELECT c.id,c.title,c.desc_short, c.url, c.url_name FROM company c, comp_website_hp_map m WHERE c.status = 1 AND c.id = m.company_id AND m.website_id = ".$_CONFIG['site_id']." order by random();";
				break;
				
		}
		$db->query($sql);

		if ($db->getNumRows() < 1) return array();

		$aRes = $db->getObjects();
		$aProfile = array();
				
		foreach($aRes as $o) {

			$oProfile = new CompanyProfile();
			$oProfile->SetFromObject($o);
			$oProfile->GetImages();
			
			if ($fetchmode == FETCHMODE__FULL)
			{
    			$oProfile->GetCategoryInfo();
    			$oProfile->GetCountryInfo();
    			$oProfile->GetActivityInfo();
    			$oProfile->SetProfileCount();
			}
			$aProfile[$oProfile->GetId()] = $oProfile;			
		}
		
		return $aProfile;
		

	}
	
	
	public function LoadTemplate($sFilename) {
		
		$this->oTemplate = new Template(); 
		
		$this->oTemplate->SetFromArray(array(
								"TITLE" => $this->GetTitle(),
								"TITLE_60" => nl2br($this->GetTitle(60)),	
								"PROFILE_LINK" => $this->GetProfileUrl(),
								"DESC_SHORT" => nl2br($this->GetDescShort()),
								"DESC_SHORT_60" => nl2br($this->GetDescShort(60)),
								"DESC_SHORT_120" => nl2br($this->GetDescShort(120)),
								"DESC_SHORT_160" => nl2br($this->GetDescShort(160)),
								"COMPANY_NAME" => $this->GetCompanyName(),
								"WEBSITE_LINK" => $this->GetUrl(),
								"WEBSITE_LINK_TRACKER" => "/outgoing/". $this->GetUrlName() ."/www",
								"PROFILE_OBJECT" => $this,
		                        "oProfile" => $this
									));
									
		if (is_object($this->GetImage(0,LOGO_IMAGE))) {
			$this->oTemplate->Set("IMG_SM_01",$this->GetImage(0,LOGO_IMAGE)->GetHtml("_sm",$this->GetTitle(),'',FALSE));
		} else {
			$this->oTemplate->Set("IMG_SM_01","");
		}
		
									
		$this->oTemplate->LoadTemplate($sFilename);
		
	}
	
	public function Render() {
		return $this->oTemplate->Render();
	}
	

	
	/*
	 * Doesn't call json_encode() to prevent double encoding
	 * if caller also does this downstream
	 */
	public function toJSON() {
	    
	    $aImageDetails = $this->GetImageUrlArray();
	    
	    $fields = array(
	        'id' => $this->GetId(),
	        'profile_type' => 0,
	        'profile_type_label' => $this->GetProfileTypeLabel($this),
	        'title' => $this->GetTitle(),
	        'desc_short' => htmlUtils::convertToPlainText($this->GetDescShort()),
	        'desc_short_160' => htmlUtils::convertToPlainText($this->GetDescShort(160)),
	        'profile_url' => $this->GetProfileUrl(),
	        'profile_uri' => "/company/".$this->GetUrlName(),
	        "company_name" => $this->GetCompanyName(),
	        "company_profile_url" => $this->GetCompanyProfileUrl(),
	        "country_txt" => '',
	        "enquiry_url" => Enquiry::GetRequestUrl('GENERAL',$this->GetId(),PROFILE_COMPANY),
	        "location" => $this->GetLocationLabel(),
	        "price_from" => "",
	        "price_to" => "",
	        "currency_label" => "",
	        "duration_from" => "",
	        "duration_to" => "",
	        "review_count" => $this->GetReviewCount(),
	        "review_rating" => $this->GetRating()
	    );
	    
	    return $fields;
	}

	public function GetJSONLD()
	{

	    $title = $this->GetTitle();
	    $description = trim(htmlUtils::convertToPlainText($this->GetDescShort(160)));
	    $bookingurl = $this->GetApplyUrl();
	    $provider = $this->GetCompanyName();
	    $provider_url = $this->GetCompanyProfileUrl();
	    
	    if (count($this->GetActivityTxtArray()) >= 1) {
	        $i = 0;
	        foreach($this->GetActivityTxtArray() as $strActivity)
	        {
	            $activity .= "\"".$strActivity."\"";
	            if ($i < count($this->GetActivityArray()) -1) $activity = $activity.",";
	            $i++;
	        }
	    }
	    
	    if (count($this->GetCountryTxtArray()) >= 1) {
	        $i = 0;
	        $destination = "";
	        foreach($this->GetCountryTxtArray() as $strCountry)
	        {
	            $destination .= "\"@type\": \"Country\",";
	            $destination .= "\"name\": \"".$strCountry."\"";
	            
	            if ($i < count($this->GetCountryArray()) -1) $destination = $destination.",";
	            $i++;
	        }
	    }
	    
	    $aImageDetails = $this->GetImageUrlArray();
	    
	    if (strlen($aImageDetails['MEDIUM']['URL']) >= 1 || strlen($aImageDetails['LARGE']['URL']) >= 1) {
	        $img_url = strlen($aImageDetails['LARGE']['URL']) >= 1 ? $aImageDetails['LARGE']['URL'] : $aImageDetails['MEDIUM']['URL'];
	        $strIMGJSON = '"image": {';
	        $strIMGJSON .= '"@type": "ImageObject",';
	        $strIMGJSON .= '"url": "'.$img_url.'"';
	        $strIMGJSON .= '},';
	    }
	    
	    $strJSON_LD = <<<EOF
{
    "@context": "https://schema.org",
    "@type": "TravelAgency",
    "name": "$title;",
    "description": "$description",
    "provider": "$provider",
    "touristType": [
      $activity
    ],
    "offers": {
      "@type": "Offer",
      "name": "$title",
      "description": "$description",
      "price": "$price",
      "priceCurrency": "$currency",
      "url": "$bookingurl",
      "offeredBy": {
        "@type": "Organization",
        "name": "$provider",
        "url": "$provider_url"
      }
    },
    $strIMGJSON
    "itinerary": [
      {
        $destination
      }
    ]
    
}
EOF;
	    
	    return $strJSON_LD;
	    
	}
	
}

?>
