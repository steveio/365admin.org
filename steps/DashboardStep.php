<?php



class DashboardStep extends GenericStep {

	private $iCompanyId; // int id of company being editted
	private $company_url_name; // string validated unique company url token from request eg /dashboard/company/<company-url-name>
	
	private $oCompanyProfile;
	private $placement_title_plural; // string "Placements" | "Camp Jobs" | "Seasonal Jobs"
	private $placement_title_singular; // string "Placement" | "Camp Job" | "Seasonal Job"
	private $aPlacements; 
	
	public function __construct( ){
		
		parent::__construct();
		
		$this->aPlacements = array();
	}

	public function Process() { 
		
		global $oAuth, $oHeader, $oFooter, $oBrand, $_CONFIG;

		if (!$oAuth->ValidSession()) Http::Redirect(ROUTE_LOGIN);
		
		$oDashboard = new Template;
		
		$oDashboard->Set('WEBSITE_URL',$oBrand->GetWebsiteUrl());
				
		if ($oAuth->oUser->isAdmin) { // admin dashboard / company select
			
			
			if ($this->GetCompanyUriFromRequest()) { // are we viewing dashboard for a specific company eg url = /dashboard/company/bunac

				$oCompanyProfile = new CompanyProfile();
				$company_id = $oCompanyProfile->GetIdByUri($this->GetCompanyUrlName());
				if (is_numeric($company_id)) {
					$this->SetCompanyId($company_id);
					$this->SetCompanyProfile();		
					$this->SetPlacementTitle();
					$this->SetPlacements();
					$oDashboard->Set('oCProfile',$this->GetCompanyProfile());
					//$oDashboard->Set('oListing',$oListing);
					$oDashboard->Set('PROFILE_ARRAY',$this->GetPlacements());
					$oDashboard->Set('PROFILE_COUNT',$this->GetPlacementCount());
					$oDashboard->Set('PROFILE_QUOTA',$this->GetCompanyProfile()->GetProfileQuota());
			
					$oDashboard->Set('PLACEMENT_TITLE_PLURAL',$this->GetPlacementTitle("PLURAL"));
					$oDashboard->Set('PLACEMENT_TITLE_SINGULAR',$this->GetPlacementTitle("SINGULAR"));
					
					$oDashboard->LoadTemplate("company_dashboard.php");
					
				}				
			} else {


                if (isset($_POST))
                {
                    $this->DeleteArticle(); // handle recent content delete request
                }
			    
			    $oTemplate = new Template();
			    $oTemplate->Set('WEBSITE_URL', $_CONFIG['url']);
			    $oTemplate->Set('RECENT_ACTIVITY_ARRAY', $this->getRecentActivity());
			    $oTemplate->LoadTemplate('search_result_list_recent.php');
			    
			    $oDashboard->Set('RECENT_ACTIVITY', $oTemplate->Render());
				$oDashboard->LoadTemplate("admin_dashboard_v2.php");
			}
			
		} else { // company user dashboard	

			$this->SetCompanyId($oAuth->oUser->company_id);
			$this->SetCompanyProfile();		
			$this->SetPlacementTitle();
			$this->SetPlacements();
			
			$oDashboard->Set('oCProfile',$this->GetCompanyProfile());
			//$oDashboard->Set('oListing',$oListing);
			$oDashboard->Set('PROFILE_ARRAY',$this->GetPlacements());
			$oDashboard->Set('PROFILE_COUNT',$this->GetPlacementCount());
			$oDashboard->Set('PROFILE_QUOTA',$this->GetCompanyProfile()->GetProfileQuota());
	
			$oDashboard->Set('PLACEMENT_TITLE_PLURAL',$this->GetPlacementTitle("PLURAL"));
			$oDashboard->Set('PLACEMENT_TITLE_SINGULAR',$this->GetPlacementTitle("SINGULAR"));
			
			$oDashboard->LoadTemplate("company_dashboard.php");
		}

		
		/* new admin system notification message */
		//$message = "<h1>New Admin System</h1>";
		//$message .= "<p><b>11-02-2012</b> We've launched an improved version of our listing admin system.  This upgrade allows you to save more information on your profiles and includes a number of other changes.  Hopefully it will all run smoothly but if you do have any problems let us know via the contact link.  Thanks.";  
		//$oMessage = new Message(MESSAGE_TYPE_NOTIFICATION, '365ADMIN_LAUNCH', $message);
		//$this->SetUserMessage($oMessage);
		
		/* messages panel */
		$oMessagesPanel = new Layout();
		$oMessagesPanel->Set('UI_MSG',$this->GetUserMessages());		
		$oMessagesPanel->LoadTemplate("messages_template.php");
		$this->UnsetUserMessages();
		
				
		print $oHeader->Render();
		print $oMessagesPanel->Render();
		print $oDashboard->Render();
		print $oFooter->Render();

		
	}

	public function DeleteArticle()
	{
	    global $aResponse;

	    $aDelete = array();
	    
	    foreach($_REQUEST as $k => $v) {
	        if (preg_match("/art_/",$k)) {
	            $id = preg_replace("/art_/","",$k);
	            if ($v == "delete") $aDelete[] = $id;
	        }
	    }

	    if (count($aDelete) >= 1) {
	        foreach($aDelete as $id) {
	            $oArticle = new Article();
	            $oArticle->SetId($id);
	            if ($oArticle->Delete()) {
	                $aResponse['msg'] = "SUCCESS : Deleted article.";
	                $aResponse['status'] = "success";
	            }
	        }
	    }

	}
	
	public function SetCompanyProfile() {
		
		global $oAuth;
		
		/* company dashboard */
		$this->oCompanyProfile = ProfileFactory::Get(PROFILE_COMPANY);
		$this->oCompanyProfile->SetFromArray($this->oCompanyProfile->GetProfileById($this->GetCompanyId()));
		$this->oCompanyProfile->GetImages();

	}
	
	public function GetCompanyProfile() {
		return $this->oCompanyProfile;
	}
	
	public function GetPlacementTitle($form = "PLURAL") {
		if ($form == "PLURAL") return $this->placement_title_plural;
		if ($form == "SINGULAR") return $this->placement_title_singular;
		
	}
	
	public function SetPlacementTitle() {
		
		global $oBrand; 
		
		$this->placement_title_singular = $oBrand->GetPlacementTitle();
		$this->placement_title_plural = $oBrand->GetPlacementTitle()."s";

	}
	
	public function SetPlacements() {
		
		/* Get details of placements */
		$oPlacementProfile = new PlacementProfile();
		$aResult = $oPlacementProfile->GetProfileById($this->GetCompanyProfile()->GetId(),"COMPANY_ID",$return = "ARRAY");
		
		$this->aPlacements = array();
		
		if (is_array($aResult)) {
			foreach($aResult as $oResult) {
				$oProfile = new PlacementProfile();
				$oProfile->SetFromObject($oResult);
				$oProfile->GetImages();
				$this->aPlacements[] = $oProfile;
			}
		}		
		
	}
	
	public function GetPlacements() {
		return $this->aPlacements;
	}
	
	public function GetPlacementCount() {
		return count($this->aPlacements);
	}
	
	private function SetCompanyId($id) {
		$this->id = $id;
	}
	
	private function GetCompanyId() {
		return $this->id;
	}

	protected function GetCompanyUrlName() {
		return $this->company_url_name;
	}
	
	protected function SetCompanyUrlName($company_url_name) {
		$this->company_url_name = $company_url_name;
	}
	
	/* look for /dashboard/company/<company-name> in request uri, return false if no valid company url found */
	private function GetCompanyUriFromRequest() {
		$request_array = Request::GetUri("ARRAY");

		if ($request_array[2] != ROUTE_COMPANY) return FALSE;
		
		if (isset($request_array[3]) && (strlen(trim($request_array[3])) >= 1)) {
			if (!Validation::ValidUriNamespaceIdentifier($request_array[3])) {
				return FALSE;
			}
			$this->SetCompanyUrlName($request_array[3]);
			return TRUE;
		}
		
	}

    public function getRecentActivity()
    {
        global $db;
        
        
        $sql = "
                (select 
                c.id,
                'COMPANY' as type,
                c.title,
                '/company/'||c.url_name as url,
                c.last_updated
                from 
                company c
                order by last_updated desc limit 20 )
                union 
                (select 
                p.id,
                'PLACEMENT' as type,
                p.title,
                '/company/'||c.url_name||'/'||p.url_name as url,
                p.last_updated
                from
                profile_hdr p, 
                company c
                where p.company_id = c.id
                order by last_updated desc limit 20 )
                union 
                (select 
                a.id,
                'ARTICLE' as type,
                a.title,
                (select m.section_uri from article_map m where m.article_id = a.id order by m.oid desc limit 1) as url,
                a.last_updated
                from
                article a
                order by last_updated desc limit 20 )";
   
            $db->query($sql);

            return $db->getObjects();
    }

}


?>
