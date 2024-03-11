<?php

define("CK_EDITOR_PROFILE_INTRO_DT","2013-01-03");


/*
 * 
 * Base Placement Profile - derived classes (General,Tour,Job) extend this class
 *
 *
 * @note 
 * 
*/


class PlacementProfile extends AbstractProfile {

	protected $type;
	protected $company_id;
  	protected $logo_url;
  	protected $company_name;
  	protected $listing_type;
	protected $tel;
  	protected $comp_url; /* company website link */
  	protected $comp_url_name; /* company profile url identifier */
  	protected $company_email;
  	protected $comp_prof_opt;
  	protected $comp_enq_opt;
  	protected $title;
  	protected $url_name;
  	protected $desc_short;
  	protected $desc_long;
	protected $location;
	protected $url; /* more info url */
	protected $apply_url; /* external apply/booking url, replaces enquiry form */
	protected $email;
	protected $video;
	protected $keyword_exclude; /* word list to exlude from indexer */
	protected $ad_active;
	protected $ad_duration; /* not currently implemented */
	protected $aCompanyLogo; /* array of company logo images 0 = logo, 1 = banner image */
	
	private $sSubTypeFields; /* SQL additional fields to return for profile sub-types */
	private $sSubTypeTable; /* SQL profile sub-type table name */
	
	public function __construct() {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		$this->aImage = array();

		$this->SetType(PROFILE_PLACEMENT);
		$this->SetLinkTo("PLACEMENT");
		
	}
	
	
	/*
	 * 
	 * Get Profile Type (eg Volunteer, Tour, Job...)
	 * 
	 * @param int placement id
	 * @return mixed false / int placement type
	 */
	public static function GetTypeById($iPlacementId) {
	
		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."()");		
		
		global $db,$_CONFIG;
		
		if (!is_numeric($iPlacementId)) return false;
		
		return $db->getFirstCell("SELECT type FROM ".$_CONFIG['profile_hdr_table']." WHERE id = ".$iPlacementId);

	}

	public function GetDetailsByUri($url_name) {
		
		global $db,$_CONFIG;
		
		$sql = "SELECT id,company_id FROM ".$_CONFIG['profile_hdr_table']." WHERE url_name = '".$url_name."'";
        $db->query($sql);
        if ($db->getNumRows() == 1) {
			return $db->getRow();
        }
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

	public function GetById($id)
	{
	    $oStd = $this->GetProfileById($id, $key = "PLACEMENT_ID",$return = "OBJECT");
	    if (is_object($oStd))
	    {
	        $this->SetFromObject($oStd);
	        $this->GetImages();
	        $this->SetCompanyLogo();
	        $this->GetCategoryInfo();
	        $this->GetActivityInfo();
	        $this->GetCountryInfo();
	        $this->GetReviewRating();
	    }
	}

	/*
	 * called directly (ie by instantiating this object) and by a derived class
	 *
	 */
	public function GetProfileById($id,$key = "PLACEMENT_ID",$return = "OBJECT", $limit = NULL, $order_by_sql = "") {

	    global $db,$_CONFIG;
	    
	    $limit_sql = (is_numeric($limit)) ? " LIMIT ".$limit : "";

	    // build the where clause
	    switch($key) {
	        case  "PLACEMENT_ID" :
	            if (!is_numeric($id)) return false;
	            $where = "p.id = $id AND p.company_id = c.id ";
	            $order_by = " ORDER BY p.title asc ";
	            break;
	        case "COMPANY_ID" :
	            if (!is_numeric($id)) return false;
	            $where = "p.company_id = $id AND p.company_id = c.id ";
	            $order_by = " ORDER BY p.title asc ";
	            break;
	        case "ALL" :
	            $where = "p.company_id = c.id ";
	            $order_by = " ORDER BY p.title asc ";
	            break;
	        case "INDEXER" :
	            $where = "p.last_updated > p.last_indexed AND p.company_id = c.id ";
	            $order_by = " ORDER BY p.title asc ";
	            break;
	        case "INDEXER_SOLR" :
	            $where = "p.last_updated > p.last_indexed_solr AND p.company_id = c.id ";
	            $order_by = " ORDER BY p.title asc ";
	            break;	            
	        case "ID_LIST" :
	            if (!is_array($id) || count($id) < 1) return false;
	            $where = "p.id IN (".implode(",",$id).") AND p.company_id = c.id ";
	            $order_by = "ORDER BY RANDOM() ";
	            break;
	        case "RECENT" :
	            $where = " p.company_id = c.id ";
	            $order_by = " ORDER BY p.last_updated DESC LIMIT 20";
	            break;
	    }

	    if (strlen($order_by_sql) > 1) {
	        $order_by = $order_by_sql;
	    }

	    if ($_CONFIG['placement_table'] == "profile_hdr") {
	        $with_oid = "p.oid 
                         ,p.id";
	    } else {
	        $with_oid = "p.id";
	    }

	    $sSql = "SELECT
						".$with_oid."
						,p.type as profile_type
						,p.company_id
						,c.title as company_name
						,c.email as company_email
						,c.tel
						,c.url as comp_url
						,c.url_name as comp_url_name
						,c.prod_type as listing_type
						,c.prof_opt as comp_prof_opt
						,c.enq_opt as comp_enq_opt
                        			,c.active
                        			,c.profile_filter_from_search
						,p.title
						,p.url_name
						,p.desc_short
						,p.desc_long
						,p.location
						,p.url
						,p.apply_url
						,p.email
						,p.keyword_exclude
						,p.img_url1
						,p.img_url2
						,p.img_url3
						,p.img_url4
						,p.video1 as video
						,p.ad_active
						,p.added_by
						,to_char(p.added,'DD/MM/YYYY') as added_date
				   		,to_char(p.last_updated,'DD/MM/YYYY') as last_updated
						,p.last_indexed
						,p.last_indexed_solr
						$this->sSubTypeFields
					FROM
						".$_CONFIG['placement_table']." p
						LEFT JOIN ".$_CONFIG['company_table']." c ON p.company_id = c.id
						$this->sSubTypeTable
					WHERE
					$where
					$order_by
					$limit_sql
				";

			$db->query($sSql);
			
			if ($return == "OBJECT") {
			    if ($db->getNumRows() == 1) return $oResult = $db->getObject();
			}
			
			if ($return == "ARRAY") {
			    if ($db->getNumRows() >= 1) return $db->getObjects();
			}
			
			if ($return == "PROFILE") {
			    if ($db->getNumRows() < 1) return array();
			    
			    $aRes = $db->getObjects();
			    $aProfile = array();
			    foreach($aRes as $o) {
			        $oProfile = new PlacementProfile();
			        $oProfile->SetFromObject($o);
			        $oProfile->GetImages();
			        $oProfile->SetCompanyLogo();
			        $oProfile->GetCategoryInfo();
			        $oProfile->GetActivityInfo();
			        $oProfile->GetCountryInfo();
			        $oProfile->GetReviewRating();
			        
			        $aProfile[$oProfile->GetId()] = $oProfile;
			    }
			    return $aProfile;
			}
	}

	public function GetOid() {
	    global $db;
	    if (!is_numeric($this->oid)) {
	        return $this->oid = $db->getFirstCell("SELECT oid FROM profile_hdr WHERE id = ".$this->GetId());
	    } else {
	        return $this->oid;
	    }
	}

	public function GetId() {
	    return $this->id;
	}

	public function SetId($id) {
	    $this->id = $id;
	}

	public function GetCompanyId() {
		return $this->company_id;
	}

  	public function SetCompanyId($iId) {
  		$this->company_id = $iId;
  	}
  	
  	public function GetCompanyName() {
  		return $this->company_name;
  	}

  	public function SetCompanyName($sCompanyName) {
  		$this->company_name = $sCompanyName;
  	}
  	
  	
  	public function GetListingType() {
  		return $this->listing_type;
  	}
  
	public function GetTel() {
		return $this->tel;
	}	
	
	/* returns the company website url */
  	public function GetCompUrl() {
  		return $this->comp_url;
  	}
  	
  	public function GetCompUrlName() {
  		return $this->comp_url_name;
  	}
  	
  	/* returns the url to the company profile */
  	public function GetCompanyProfileUrl() {
  		global $_CONFIG;  		
  		return $_CONFIG['url']."/company/".$this->GetCompUrlName();
  	}
  	
  	public function GetProfileUrl() {
  		global $_CONFIG;  		
  		return $_CONFIG['url']."/company/".$this->GetCompUrlName()."/".$this->GetUrlName();
  	}

  	public function GetUri()
  	{
  	    global $_CONFIG;

  	    return "/company/".$this->GetCompUrlName()."/".$this->GetUrlName();
  	}

  	public function SetCompUrlName($sCompUrlName) {
  		$this->comp_url_name = $sCompUrlName;
  	}

  	public function GetTitle($trunc = 0) {
  		
  		if ($trunc >= 1) {
  			$s = $this->title;
			if (strlen($s) > $trunc) {
				$s = $s." ";
				$s = substr($s,0,$trunc);
				$s = $s."...";
			}
			return $s;	
  		} else {
 			return $this->title;
  		}
  	}

  	public function SetTitle($sTitle) {
 		$this->title = $sTitle;
  	}

  	
  	/*
  	 * A generic method so we can call $oProfile->GetFullyQualifiedTitle() on comp or placement objects
  	 *  comp method returns <company-name>
  	 *  placement method return <company-name> : <placement-name>
  	 * 
  	 */
  	public function GetFullyQualifiedTitle() {
  		return $this->GetCompanyName() . " : " .$this->title;
  	}
  	
  	public function GetUrlName() {
  		return $this->url_name;
  	}

	public function SetUrlName($sUrlName) {
  		$this->url_name = $sUrlName;
  	}

  	public function GetDescShort($trunc = 0) {
  		
  		if ($trunc >= 1) {
  			$s = $this->desc_short;
			if (strlen($s) > $trunc) {
				$s = $s." ";
				$s = substr($s,0,$trunc);
				$s = substr($s,0,strrpos($s,' '));
				$s = $s."...";
				$s = strip_tags($s); // in case we left an open <b> tag
			}
			return $s;	
  		} else {
 			return $this->desc_short;
  		}
  	}

  	public function SetDescShort($sDescShort) {
  		$this->desc_short = $sDescShort;
  	}
  	
  	public function GetDescLong() {
  		if ($this->GetLastUpdatedAsTs() < strtotime(CK_EDITOR_PROFILE_INTRO_DT) ) {
  			return  nl2br($this->desc_long);
  		}
  		return $this->desc_long;

  	}

  	public function GetLastUpdatedAsTs() {
  		$date = str_replace('/', '-', $this->last_updated);
  		return strtotime($date);
  	}

  	public function GetLocation() {
  		return $this->location;
  	}
  	
  	public function GetFilterFromSearch()
  	{
  	    return ($this->profile_filter_from_search == "t") ? true : false;
  	}

  	public function GetCompanyActive()
  	{
  	    return ($this->active == "f") ? true : false;
  	}

	public function GetUrl() {
		
		if (!preg_match("/http/",$this->url)) {
			return "http://".$this->url;
		} else {	
			return $this->url;
		}
	}
	
	public function GetApplyUrl() {
		if ((strlen($this->apply_url) > 1) && (!preg_match("/http/",$this->apply_url))) {
			return "http://".$this->apply_url;
		} else {	
			return $this->apply_url;
		}
	}
	
	public function HasApplyUrl() {
		if (strlen($this->apply_url) > 1) return true;
	}
	
	
	public function GetEmail() {
		
		if (strlen($this->email) > 1) {
			return $this->email;
		} else {
			return $this->company_email;
		}
	}
	
	public function GetKeywordExclude() {
		return $this->keyword_exclude;
	}
	
	public function GetVideo() {
		return $this->video;
	}
	
	public function GetCompProfOpt() {
		return $this->comp_prof_opt;
	}

	public function GetCompEnqOpt() {
		return $this->comp_enq_opt;
	}
	
	public function GetActive() {
		return $this->ad_active;
	}
	
	public function GetAdActive() {
		return $this->ad_active;
	}
	
	public function GetAdDuration() {
		return $this->ad_duration;
	}

	public function IsAdActiveDisplayInSearch()
	{
	    global $db;
	    
	    // is profile inactive / filtered from search?
	    $sql = "select
                1
                from
                profile_hdr p
                , company c
                where
                p.id = ".$this->GetId()."
                and p.company_id = c.id
                and (p.ad_active = 'f'
                or c.profile_filter_from_search = 't')
                ";
	    
	    $db->query($sql);
	    
	    return ($db->getNumRows() == 1) ? false : true;
	}


	public function SetImageProcessStatus($iStatus = 0) {
		
		global $db,$_CONFIG;
		
		$db->query("UPDATE ".$_CONFIG['profile_hdr_table'] ." SET img_status = '".$iStatus."' WHERE id = ".$this->GetId());
		 
	}
	
	public function SetLogoProcessFlag($val = 'F') {
		return true;
	}
	

	public function DoAddUpdate($p,&$aResponse) {

		global $db, $oAuth,$_CONFIG;
		
		/* validate the submitted params */
		if (!Validation::ValidatePlacement($p,$aResponse)) return false;
	
		/* Escape submitted params */
		Validation::AddSlashes($p);
	
		$db->query("BEGIN");

		$p['ad_active'] = ($p['ad_active'] == "true") ? "t" : "f";   
		
		if (!is_numeric($p['id'])) { /* ADD a new placement */

			if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."() ADD");
			
			$mode = "add";

			// non admin can only add placements under their company
			if ((!$oAuth->oUser->isAdmin) && ($oAuth->oUser->company_id != $p['company_id'])) {
				$aResponse['msg'] = "ERROR: We don't think you are authorised to do that";
				$db->query("ROLLBACK");
				return false;
			}

			/* generate unique url namespace identifier */
			$oNs = new NameService();
			$p['url_name'] = $oNs->GetUrlName($p['title'],$_CONFIG['profile_hdr_table'],'url_name');
				
			/* add the placement */
			$p['id'] = $this->Add($p);

			if (is_numeric($p['id'])) {
				
				Logger::DB(2,get_class($this)."::".__FUNCTION__."()","ADD_PLACEMENT OK : ".$p['id']);
				
				$aResponse['msg'] = "SUCCESS: Added new placement : ".$p['title']."";
				$aResponse['id'] = $p['id'];
				$aResponse['url_name'] = $p['url_name'];
				$aResponse['retVal'] = 1;
			} else {
				
				Logger::DB(1,get_class($this)."::".__FUNCTION__."()","ADD_PLACEMENT FAIL : ".serialize($p));
				
				$aResponse['msg'] = "ERROR: Sorry, there was a problem adding placement. <br />Email ".$_CONFIG['admin_email']. " for assistance.";
				$db->query("ROLLBACK");
				return false;
			}

		} else { /* UPDATE placement */		    
		    
			$bUrlChanged = FALSE;
			
			// non admin can only edit their own placements
			if ((!$oAuth->oUser->isAdmin) && ($oAuth->oUser->company_id != $p['company_id'])) {
				$aResponse['msg'] = "ERROR: We don't think you are authorised to edit this placement";
				$db->query("ROLLBACK");
				return false;
			}

			/* update url_name */
			$sExistingTitle = $db->getFirstCell("SELECT title FROM ".$_CONFIG['profile_hdr_table']." WHERE id = ".$p['id'].";");
			if ($p['title'] != stripslashes($sExistingTitle)) { /* generate a new unique url namespace identifier */
				$oNs = new NameService();
				$p['url_name'] = $oNs->GetUrlName($p['title'],$_CONFIG['profile_hdr_table'],'url_name');
				$bUrlChanged = true;
			}
			
			
			if ($p['img_refresh_fl'] == "Y") {
				$p['img_status'] = 1; /* force image refresh */
			} else {
				/* set image thumbnail refresh - img batch job should only refresh changed images */
				$db->query("SELECT img_url1, img_url2, img_url3 FROM ".$_CONFIG['profile_hdr_table']." WHERE id = ".$p['id']);
				$aExistingImg = $db->getRows();
				$sExistingImgHash = md5(serialize($aExistingImg[0]));
				$aNewImgUrl = array("img_url1" => $p['img_url1'],"img_url2" => $p['img_url2'],"img_url3" => $p['img_url3']);
				$sNewImgHash = md5(serialize($aNewImgUrl));			
				$p['img_status'] = ($sExistingImgHash == $sNewImgHash) ? 0 : 1;
			}
			
			if ($this->Update($p)) { /* update the placement */

				
				Logger::DB(2,get_class($this)."::".__FUNCTION__."()","EDIT_PLACEMENT OK : ".$p['id']);
				
				$aResponse['msg'] = "SUCCESS: Updated placement : ".$p['title']."";
				$aResponse['id'] = $p['id'];
				$aResponse['retVal'] = 1;
				
				/* 
				 * refresh cache  
				 * 
				 * this is done on demand, ie with each request to save a profile
				 * maybe we should migrate this to use a queue table and batch job
				 * 
				 */

				$this->UpdateCache($p['url_name'], $p['company_id']);
								
			} else {
				
				Logger::DB(1,get_class($this)."::".__FUNCTION__."()","EDIT_PLACEMENT FAIL : ".serialize($p));
				
				$aResponse['msg'] = "ERROR: There was a problem updating placement. <br />Email ".$_CONFIG['admin_email']. " for support.";
				$db->query("ROLLBACK");
				return FALSE;
			}
		}


		/* update mappings */

		if (!is_numeric($p['id'])) { /* check that we have a valid id */
			$db->query("ROLLBACK");
			return FALSE; 
		}

		/* update category mapping */
		Mapping::Update($bAdminRequired = false,$sTbl = "prod_cat_map",$sKey = "prod_id",$p['id'],$aFormValues = $p,$sFormKeyPrefix = "cat_",$sKey2 = "category_id");

		/* if a placement is put in a category - ensure that the company is also in the category */
		$db->query("SELECT category_id FROM prod_cat_map WHERE prod_id = ".$p['id']);
		if ($db->getNumRows() >= 1) {
			$aCat = $db->getRows();
			if ((is_array($aCat)) && (count($aCat) >= 1)) {
				foreach($aCat as $k => $v) {
					$db->query("SELECT 1 FROM comp_cat_map WHERE company_id = ".$p['company_id'] ." AND category_id = ".$v['category_id']);
					if ($db->getNumRows() < 1) {
						$db->query("INSERT INTO comp_cat_map (company_id, category_id) VALUES (".$p['company_id'].",".$v['category_id'].")");					
					}
				}
			}
		}
		
		
		/* update activity mapping */
		Mapping::Update($bAdminRequired = false,$sTbl = "prod_act_map",$sKey = "prod_id",$p['id'],$aFormValues = $p,$sFormKeyPrefix = "act_",$sKey2 = "activity_id");

		/* update country mapping */
		Mapping::Update($bAdminRequired = false,$sTbl = "prod_country_map",$sKey = "prod_id",$p['id'],$aFormValues = $p,$sFormKeyPrefix = "cty_",$sKey2 = "country_id");

		/* @depreciated for refdata system 
		 * update options mapping 
		 */
		//Mapping::Update($bAdminRequired = false,$sTbl = "prod_opt_map",$sKey = "prod_id",$p['id'],$aFormValues = $p,$sFormKeyPrefix = "opt_",$sKey2 = "option_id");
		

		$db->query("COMMIT"); /* everything went OK */
	
		
		/* if the title / url changes pass redirect details back to the caller */
		if ($bUrlChanged) {
			
			$edit_url = BASE_URL."/".ROUTE_PLACEMENT."/".$p['url_name']."/edit/";
			
			$aResponse['url_change'] = TRUE;
			$aResponse['edit_url'] = $edit_url; 
			
		}

		// put ID in session for image_upload.php
		$_SESSION['id'] = $p['id'];
		$_SESSION['link_to'] = "PLACEMENT";
		
		return TRUE;


	}
	
	
	private function UpdateCache($url_name, $company_id) {
		
		global $db;
	
		$oCompanyProfile = new CompanyProfile;	
		$sCompUrlName = $oCompanyProfile->GetUrlNameById($company_id);
		$sUri = "/company/".$sCompUrlName."/".$url_name;
		
		// get id's and hostname of each website this page is registered with in cache table
		$aSiteId = Cache::GetSiteIdsByUri($sUri);
		
		if (!is_array($aSiteId) || count($aSiteId) < 1) return FALSE;
		
		$oWebsite = new Website($db);
		$aHostname = $oWebsite->GetHostnames(" WHERE id IN (".implode(",",$aSiteId).")");
		
		if (count($aSiteId) >= 1) {
			
			$delay = (count($aSiteId) > 1) ? TRUE : FALSE; // if we are updating multiple cached pages set delay to TRUE
			
			foreach($aSiteId as $website_id) {
				
				$sUrl = $aHostname[$website_id]."/company/".$sCompUrlName."/".$url_name;
				
				Cache::Generate($sUrl,$sUri,$website_id, $delay);
				
			}
			
		}
		
	}

	private function Add($p) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db,$oAuth,$_CONFIG;
		
		
		$p['img_status'] = 1; 
		
		if (trim($p['video']) > 1) {  
			$video_str = "'".htmlspecialchars_decode(trim($p['video']))."'"; 
		} else {
			$video_str = "NULL";
		}		

		$sql = "INSERT INTO ".$_CONFIG['profile_hdr_table']." (
						id
						,company_id
						,type
						,title
						,url_name
						,desc_short
						,desc_long
						,location
						,url
						,apply_url
						,email
						,keyword_exclude
						,img_url1
						,img_url2
						,img_url3
						,img_url4
						,img_status
						,video1
						,ad_active
						,ad_duration
						,added_by
						,added
						,last_updated
						,last_indexed
						,last_indexed_solr
					) VALUES (
						nextval('placement_seq')
						,".$p['company_id']."
						,".$p['profile_type']."
						,'".$p['title']."'
						,'".$p['url_name']."'
						,'".$p['desc_short']."'
						,'".$p['desc_long']."'
						,'".$p['location']."'
						,'".$p['url']."'         
						,'".$p['apply_url']."'
						,'".$p['email']."'
						,'".$p['keyword_exclude']."'
						,'".$p['img_url1']."'
						,'".$p['img_url2']."'
						,'".$p['img_url3']."'
						,'".$p['img_url4']."'
						,".$p['img_status']."
						,".$video_str."
						,'".$p['ad_active']."'
						,0
						,'".$oAuth->oUser->id."'						
						,now()::timestamp
						,now()::timestamp
						,now()::timestamp
						,now() - interval '1 hour'
					);";

		
		$db->query($sql);

		if ($db->getAffectedRows() == 1) {

			$oid = $db->getLastOid();
			
			$p['id'] = $db->getFirstCell("SELECT id FROM ".$_CONFIG['profile_hdr_table']." WHERE oid = $oid");
			
			/* add profile subtype record (volunteer, job, tour) */
			$oProfile = ProfileFactory::Get($p['profile_type']);
			if (!$oProfile->AddSubTypeRecord($p)) return false;

			return $p['id'];
		} else {
			Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$sql);
		}
	}

	/*
	 * Update placement
	 * 
	 * 
	 */

	private function Update($p) {

		global $db,$_CONFIG;
		
		if (!is_numeric($p['id'])) return false;
		
		/* update generic placement fields */
		
		if (trim($p['video']) > 1) {  
			$video_str = "'".htmlspecialchars_decode(trim($p['video']))."'"; 
		} else {
			$video_str = "NULL";
		}				
		
		$sql = "UPDATE ".$_CONFIG['profile_hdr_table']."
				SET 
					company_id   = ".$p['company_id']."
					,type   	  = ".$p['profile_type']."
					,title        = '".$p['title']."'
					,url_name	  = '".$p['url_name']."'
					,desc_short   = '".$p['desc_short']."'
					,desc_long    = '".$p['desc_long']."'
					,location       = '".$p['location']."'										
					,url          = '".$p['url']."'
					,apply_url          = '".$p['apply_url']."'
					,email        = '".$p['email']."'
					,keyword_exclude        = '".$p['keyword_exclude']."'
					,img_url1        = '".$p['img_url1']."'
					,img_url2        = '".$p['img_url2']."'
					,img_url3        = '".$p['img_url3']."'
					,img_url4        = '".$p['img_url4']."'
					,video1        = ".$video_str."
					,img_status       = '".$p['img_status']."'
					,ad_active       = '".$p['ad_active']."'
					,last_updated = now()::timestamp
				WHERE
					id= ".$p['id'].";
				";

		$db->query($sql);
		
		if ($db->getAffectedRows() != 1) {
			Logger::DB(1,get_class($this)."::".__FUNCTION__."()",$sql);
			return false;
		}
		
		/* update profile subtype fields (volunteer, job, tour) */
		$oProfile = ProfileFactory::Get($p['profile_type']);
		if (!$oProfile->UpdateSubTypeRecord($p)) return false;
		
		return true;
		
	}

	
	public function GetProfileCount($type = 'ALL',$id = null) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db,$_CONFIG;

		switch($type) {
			case "ALL" :
				return $db->getFirstCell("SELECT count(*) from ".$_CONFIG['profile_hdr_table'].";");
				break;
			case "BY_COMPANY" :
				return $db->getFirstCell("SELECT count(*) as placement_count FROM ".$_CONFIG['profile_hdr_table']." p, ".$_CONFIG['company_table']." c WHERE p.company_id = $id AND p.company_id = c.id");
				break;
			case "BY_CATEGORY" :
				$db->query("select cat.name,count(p.id) as count from category cat, comp_cat_map cc, ".$_CONFIG['company_table']." c, ".$_CONFIG['profile_hdr_table']." p where cat.id = cc.category_id and cc.company_id = c.id and c.id = p.company_id group by cat.name order by cat.name asc;");
				return $db->getObjects();
				break;
			case "BY_CATEGORY_COMPANY" :
				$db->query("select c.title, count(distinct(p.id)) from ".$_CONFIG['company_table']." c, ".$_CONFIG['profile_hdr_table']." p where p.company_id = c.id group by c.title order by c.title asc;");
				return $db->getObjects();
				break;
		}
	}
		
	
	function Delete() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		global $db,$_CONFIG,$oAuth;
		
		if (($oAuth->oUser->GetCompanyId() != $this->GetCompanyId()) &&
			(!$oAuth->oUser->isAdmin)) {
			return false;		
		}
		
		if (!is_numeric($this->GetId())) return false;

		/* 1.  delete any placement images */
		$aImage = $this->GetImages(PROFILE_IMAGE);
		if (is_array($aImage)) {		
			foreach($aImage as $oImage) {
				$oImage->Delete();
			}
			unset($aImage);
		}

		/*
		 * 2.  Delete relational dependecies and placement row
		 * 
		 * @todo - migrate to a stored proc
		 */
		$db->query("DELETE FROM prod_cat_map WHERE prod_id = ".$this->GetId()."");
		$db->query("DELETE FROM prod_act_map WHERE prod_id = ".$this->GetId()."");
		$db->query("DELETE FROM prod_country_map WHERE prod_id = ".$this->GetId()."");
		$db->query("DELETE FROM prod_opt_map WHERE prod_id = ".$this->GetId()."");
		$db->query("DELETE FROM keyword_idx_2 WHERE type = 2 AND id = ".$this->GetId()."");

		switch($this->GetType()) {
			case PROFILE_VOLUNTEER :
				$db->query("DELETE FROM profile_general WHERE p_hdr_id = ".$this->GetId()."");
				break;
			case PROFILE_TOUR :
				$db->query("DELETE FROM profile_tour WHERE p_hdr_id = ".$this->GetId()."");
				break;
			case PROFILE_JOB :
				$db->query("DELETE FROM profile_job WHERE p_hdr_id = ".$this->GetId()."");
				break;
				
		}
				
		$db->query("DELETE FROM ".$_CONFIG['profile_hdr_table']." WHERE id = ".$this->GetId()."");

		return true;
	}

	public static function GetPlacementDDList($iCompanyId) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db,$_CONFIG;

		if (!is_numeric($iCompanyId)) return false;

		$db->query("SELECT p.id,p.title FROM ".$_CONFIG['profile_hdr_table']." p WHERE p.company_id = $iCompanyId ORDER BY p.title ASC;");

		$aResult = $db->getRows();

		$s = "<select id='placement_id' name='placement_id' class='form-select'>";
		$s .= "<option value='NULL'></option>";
		if (is_array($aResult)) {
			foreach ($aResult as $aRow) {
				$s .= "<option value='".$aRow['id']."'>";
				$s .= $aRow['title'];
				$s .= "</option>";
			}
		}
		$s .= "</select>";

		return $s;
		
	}

	// used by request router to validate request and setup page header
	public function GetByUrlName($url_name)
	{
	    global $db ,$_CONFIG;
	    
	    $sql = "SELECT 
                    p.id,
                    p.title, 
                    p.desc_short,
                    p.type,
                    c.id as company_id, 
                    c.title as company_title,
                    c.url_name as company_url_name
                FROM 
                    ".$_CONFIG['placement_table']." p, 
                    ".$_CONFIG['company_table']." c 
                WHERE 
                    p.url_name = '".$url_name."' 
                AND p.company_id = c.id";

	    $db->query($sql);
	    if ($db->getNumRows() == 1) {
	        return $aRes = $db->getRow();
	    } else {
	        throw new NotFoundException("Placement Profile url: '".$url_name."' not found");
	    }
	}
	
	public static function Get($key,$id, $fetchmode = FETCHMODE__FULL, $bFilterFromSearch = false) {

		global $_CONFIG, $db;
		
		if ($fetchmode == FETCHMODE__FULL)
		{
    		$select = "p.id
                       ,p.type
                       ,p.type as profile_type
    				   ,p.url_name
    				   ,p.title        
    				   ,p.desc_short   
    				   ,p.desc_long  
    				   ,p.company_id
                                   ,c.id as company_id   
    				   ,c.title as company_name
    				   ,c.tel
    				   ,c.url as comp_url
    				   ,c.url_name as comp_url_name   
				   ,c.profile_filter_from_search
    				   ,p.location
    				   ,p.ad_active
    				   ,p.url
    				   ,p.email
    				   ,to_char(p.added,'DD/MM/YYYY') as added_date
    				   ,to_char(p.last_updated,'DD/MM/YYYY') as updated_date";
		} elseif ($fetchmode == FETCHMODE__SUMMARY) {

		    $select = "
                    p.id
                    ,p.type
                    ,p.type as profile_type
                    ,p.url_name
                    ,p.title
                    ,p.desc_short
                    ,c.id as company_id
                    ,c.title as company_name
                    ,c.url_name as comp_url_name
		    ,c.profile_filter_from_search
                    ,p.location
                    ,p.ad_active
                    ,(SELECT count(*) from review r WHERE r.status = 1 AND r.link_to = 'PLACEMENT' and r.link_id = p.id) as num_review
                    ,(SELECT ROUND(sum(r2.rating) / (SELECT count(*) from review r1 WHERE r1.status = 1 AND r1.link_to = 'PLACEMENT' and r1.link_id = p.id)) from review r2 WHERE r2.status = 1 AND r2.link_to = 'PLACEMENT' and r2.link_id = p.id) as avg_rating 
                    ";
		}

		// build the where clause
		switch($key) {
			case  "PLACEMENT_ID" :
				$where = "p.id = $id AND p.company_id = c.id ";
				$order_by = " ORDER BY p.title asc ";
				break;
			case "COMPANY_ID" :
				$where = "p.company_id = $id AND p.company_id = c.id ";
				$order_by = " ORDER BY p.title asc ";
				break;
			case "ALL" :
				$where = "p.company_id = c.id ";
				$order_by = " ORDER BY p.title asc ";
				break;
			case "INDEX_LIST_ALL" :
				$select = "p.id,p.type";
				$where = "p.company_id = c.id ";
				$order_by = " ORDER BY p.title asc ";
				break;
			case "INDEX_LIST_DELTA" :
				$select = "p.id,p.type";
				$where = "p.last_updated > p.last_indexed AND p.company_id = c.id ";
				$order_by = " ORDER BY p.title asc ";
				break;
			case "INDEX_LIST_DELTA_SOLR" :
				$select = "p.id,p.type";
				$where = "p.last_updated > p.last_indexed_solr AND p.company_id = c.id ";
				$order_by = " ORDER BY p.title asc ";
				break;
			case "ID_LIST_SEARCH_RESULT" :
			    if (!is_array($id) || count($id) < 1) return FALSE;
			    $where = "p.id IN (".implode(",",$id).") AND p.company_id = c.id ";
			    $where .= " AND c.profile_filter_from_search != 't' ";
		        break;
			case "ID_LIST" :
				$where = "p.id IN (".implode(",",$id).") AND p.company_id = c.id ";
				$order_by = "ORDER BY RANDOM() ";
				break;
			case "RECENT" :
				$where = " p.company_id = c.id ";
				$order_by = " ORDER BY p.last_updated DESC LIMIT 20";
				break;
				
		}

		$sql = "SELECT
				   $select
				FROM 
				   ".$_CONFIG['placement_table']." p,
				   ".$_CONFIG['company_table']." c
					WHERE
					$where
					$order_by
					;";

		$db->query($sql);
					
		if ($db->getNumRows() < 1) return array();
		
		
		$aRes = $db->getObjects();
		
		$aProfile = array();
				
		foreach($aRes as $o) {

		    $oProfile = ProfileFactory::Get($o->type);

			$oProfile->SetFromObject($o);

			$oProfile->GetImages();
			$oProfile->SetCompanyLogo();
		    $oProfile->GetActivityInfo();
		    $oProfile->GetCountryInfo();
		    $oProfile->GetReviewRating();
		    
			$aProfile[$oProfile->GetId()] = $oProfile;
		}

		return $aProfile;
	}	

	public static function GetPlacementById($id, $key = "id")
	{

	    global $db; 

	    if (($key == "related_id") && (is_array($id))) 
	    {
	       $keySql = "p.id in (".implode(",", $id).")";
	    } elseif ($key == "company_id")
	    {
	        $keySql = "p.company_id = ".$id;
	    }
	    
	    $sql = "
        	    SELECT
        	    p.id
        	    ,p.type
                ,p.type as profile_type
        	    ,p.url_name
        	    ,p.title
        	    ,p.desc_short
        	    ,c.id as company_id
        	    ,c.title as company_name
        	    ,c.url_name as comp_url_name
        	    ,p.location
        	    ,p.ad_active
        	    ,(SELECT count(*) from review r WHERE r.status = 1 AND r.link_to = 'PLACEMENT' and r.link_id = p.id) as review_count
        	    ,(SELECT sum(r2.rating) / (SELECT count(*) from review r1 WHERE r1.status = 1 AND r1.link_to = 'PLACEMENT' and r1.link_id = p.id) from review r2 WHERE r2.status = 1 AND r2.link_to = 'PLACEMENT' and r2.link_id = p.id) as review_rating
                ,CASE
                	WHEN p.type = 2  THEN p1.price_from_id
                	WHEN p.type = 3  THEN p2.price_from_id                  
                	WHEN p.type = 4  THEN NULL
                END as price_from_id
                ,CASE
                	WHEN p.type = 2  THEN p1.price_to_id
                	WHEN p.type = 3  THEN p2.price_to_id                  
                	WHEN p.type = 4  THEN NULL
                END as price_to_id
                ,CASE
                	WHEN p.type = 2  THEN p1.duration_from_id
                	WHEN p.type = 3  THEN p2.duration_from_id                  
                	WHEN p.type = 4  THEN p3.duration_from_id
                END as duration_from_id
                ,CASE
                	WHEN p.type = 2  THEN p1.duration_to_id
                	WHEN p.type = 3  THEN p2.duration_to_id                  
                	WHEN p.type = 4  THEN p3.duration_to_id
                END as duration_to_id
                ,CASE
                	WHEN p.type = 2  THEN p1.currency_id
                	WHEN p.type = 3  THEN p2.currency_id                  
                	WHEN p.type = 4  THEN NULL
                END as currency_id
        	    FROM
        	    profile_hdr p
        	    LEFT JOIN company c on p.company_id = c.id
        	    LEFT OUTER JOIN profile_general p1 on p.id = p1.p_hdr_id
        	    LEFT OUTER JOIN profile_tour p2 on p.id = p2.p_hdr_id
        	    LEFT OUTER JOIN profile_tour p3 on p.id = p3.p_hdr_id
        	    WHERE
        	    ".$keySql."
        	    ORDER BY p.title asc";
	        
	    $db->query($sql);
	    
	    if ($db->getNumRows() < 1) return array();

	    $aRes = $db->getObjects();
	    
	    $aProfile = array();
	    
	    foreach($aRes as $o) {
	        
	        $oProfile = ProfileFactory::Get($o->type);
	        $oProfile->SetFromObject($o);

	        $oProfile->GetImages();
	        $oProfile->SetCompanyLogo();	        
            $oProfile->GetCategoryInfo();
            $oProfile->GetActivityInfo();	        
	        $oProfile->GetCountryInfo();

	        $aProfile[$oProfile->GetId()] = $oProfile;
	    }

	    return $aProfile;
	}
	
	public function GetRelatedPlacementsByCountry($country_id, $limit = 6) {
	
		global $db, $_CONFIG;
	
		// try to get related placements in a similar location
		if (is_numeric($oProfile->country_array[0])) {
			$related_placement_sql = "SELECT
											p.id 
										FROM 
											".$_CONFIG['placement_table']." p
											,prod_country_map m
											,country c
											,country c2 
										WHERE 
											c.id = ".$country_id." 
											AND c.continent_id = c2.continent_id 
											AND m.country_id = c2.id
											AND m.prod_id = p.id 
										ORDER by random() limit ".$limit.";";
		} else {
			$related_placement_sql = "SELECT p.id FROM ".$_CONFIG['placement_table']." p ORDER BY random() limit ".$limit.";";
		}

		$db->query($related_placement_sql);
		$arr = $db->getRows();
				
		if (is_array($arr)) {
			foreach($arr as $r) {
				$aId[] = (int) $r['id'];
			}
			return $this->Get("ID_LIST",$aId);
		}
		return array();
	}
	
	
	public function LoadTemplate($sFilename) {
		
		$this->oTemplate = new Template(); 
		
		$this->oTemplate->SetFromArray(array(
								"TITLE" => $this->GetTitle(),
								"TITLE_60" => $this->GetTitle(60),
								"TITLE_120" => $this->GetTitle(120),
								"PROFILE_LINK" => $this->GetProfileUrl(),
								"DESC_SHORT" => nl2br($this->GetDescShort()),
								"DESC_SHORT_60" => nl2br($this->GetDescShort(60)),
								"COMPANY_NAME" => $this->GetCompanyName(),
								"COUNTRY_TXT" => $this->country_txt,
		                        "oProfile" => $this
							));
									
									
		if (is_object($this->GetImage(0))) {
			$this->oTemplate->Set("IMG_SM_01",$this->GetImage(0)->GetHtml("_sf",""));
			if (!$this->GetImage(0)->GetHtml("_mf","")) {
				$this->oTemplate->Set("IMG_M_01",$this->GetImage(0)->GetHtml("_m",""));
			} else {
				$this->oTemplate->Set("IMG_M_01",$this->GetImage(0)->GetHtml("_mf",""));
			}			
		} elseif (is_object($this->GetCompanyLogo())) {
			$this->oTemplate->Set("IMG_SM_01",$this->GetCompanyLogo()->GetHtml("_sm",$this->GetTitle(),'',FALSE));
			$this->oTemplate->Set("IMG_M_01",$this->GetCompanyLogo()->GetHtml("",$this->GetTitle(),'',FALSE));
		} else {
			$this->oTemplate->Set("IMG_SM_01","");
			$this->oTemplate->Set("IMG_M_01","");
		}

		
									
		$this->oTemplate->LoadTemplate($sFilename);
		
	}
	
	public function Render() {
		return $this->oTemplate->Render();
	}
	

	public function toJSON() {
	    
	    $aImageDetails = $this->GetImageUrlArray();
	    
	    $fields = array(
	        "id" => $this->GetId(),
	        "profile_type" => 1,
	        "profile_type_label" => $this->GetProfileTypeLabel($this),
	        "title" => $this->GetTitle(64),
	        'desc_short' => htmlUtils::convertToPlainText($this->GetDescShort()),
	        'desc_short_160' => htmlUtils::convertToPlainText($this->GetDescShort(160)),
	        "profile_url" => "/company/".$this->GetCompUrlName()."/".$this->GetUrlName(),
	        "profile_uri" => "/placement/".$this->GetUrlName(),
	        "company_name" => $this->GetCompanyName(),
	        "company_profile_url" => $this->GetCompanyProfileUrl(),
	        "company_profile_edit_url" => "/company/".$this->GetCompUrlName()."/edit",
	        "image_url_small" => $aImageDetails['SMALL']['URL'],
	        "image_url_medium" => $aImageDetails['MEDIUM']['URL'],
	        "image_url_large" => $aImageDetails['LARGE']['URL'],
	        "country_txt" => "",
	        "booking_url" => "",
	        "location" => $this->GetLocationLabel(),
	        "price_from" => $this->GetPriceFromLabel(),
	        "price_to" => $this->GetPriceToLabel(),
	        "currency_label" => $this->GetCurrencyLabel(),
	        "duration_from" => $this->GetDurationFromLabel(),
	        "duration_to" => $this->GetDurationToLabel(),
	        "review_count" => $this->GetReviewCount(),
	        "review_rating" => $this->GetRating()
	    );
	    
	    if (strlen($this->GetCountryTxt()) > 1) {
	        $fields['country_txt'] = $this->GetCountryTxt();
	    }
	    
	    if (is_numeric($this->GetDurationFrom())) {
	        $fields['duration'] = $this->GetDurationFromLabel() ." - " .$this->GetDurationToLabel();
	    } else {
	        $fields['duration'] = '';
	    }
	    
	    // booking / enquiry url
	    
	    if (in_array($this->GetProfileType(), array(PROFILE_VOLUNTEER,PROFILE_TOUR))) {
	        if (strlen($this->GetApplyUrl()) > 1 && $this->GetApplyUrl() != "http://") {
	            $fields['booking_url'] = $this->GetApplyUrl();
	        } else {
	            $fields['booking_url'] = Enquiry::GetRequestUrl('BOOKING',$this->GetId(),PROFILE_PLACEMENT);
	        }
	    } else {
	        $fields['booking_url'] = Enquiry::GetRequestUrl('GENERAL',$this->GetId(),PROFILE_PLACEMENT);
	    }
	    
	    return $fields;
	}
	
};


?>
