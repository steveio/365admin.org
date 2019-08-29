<?php


class Mailshot {
	
	private $iSiteId; // int website id we are sending messages from
	private $aCompanyId; // array of company id's we are sending msg to
	private $aCompanyProfile;
	
	private $sHtmlTemplatePath;
	private $sTextTemplatePath;
	
	private $sEmailTemplate;  // parameterised email template
	private $oEmailTemplate; // populated email template 
	
	public function __construct() {
		
		$this->aCompanyId = array();
		$this->aCompanyProfile = array();
		
		global $oBrand, $_CONFIG;
		
		// these are required to send emails 
		$_CONFIG['root_path'] = $oBrand->GetWebsitePath();
		$_CONFIG['template_home'] = "templates";
		$_CONFIG['site_title'] = $oBrand->GetSiteTitle();
		
	}
	
	public function SetSiteId($id) {
		$this->iSiteId = $id;
	}
	
	public function GetSiteId() {
		return $this->iSiteId;
	}
	
	public function GetCompanyId() {
		return $this->aCompanyId;
	}
	
	public function GetCompanyProfile() {
		return $this->aCompanyProfile;
	}
	
	public function GetEmailTemplate() {
		return $this->sEmailTemplate;
	}
	
	public function SetEmailTemplate($email_template) {
		$this->sEmailTemplate = $email_template;
	}

	/* hardcoded for v1.0, to be made user selectable in future version */
	public function GetCSVCompanyDataFilePath() {
		
		global $_CONFIG, $oBrand;

		return $filepath = PATH_2_DATA_DIR.$oBrand->GetWebsiteName()."/mailshot_company.csv";
				
	}	
	
	public function LoadCSVCompanyData() {
		
		$file = $this->GetCSVCompanyDataFilePath();
		
		if (($handle = fopen($file, "r")) !== FALSE) {
			$i = 0;
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		
				// expect a company id at index 0
				if (!is_numeric($data[0])) continue;
				
				$this->aCompanyId[] = $data[0];
				
			}
			
			if (count($this->GetCompanyId()) < 1) throw new Exception("0 company id found in CSV file: ".$file);
			
		} else {
			throw new Exception("Unable to open CSV file: ".$file);
		}
		
	}

	public function LoadCompanyProfileDetails() {
		
		foreach($this->aCompanyId as $id) {
			
			try {
				
				$profile_type = CompanyProfile::GetTypeById( $id );
					
				if (!is_numeric($profile_type)) throw new Exception(ERROR_INVALID_PROFILE_TYPE.__FUNCTION__.$id);
			
				$oCompanyProfile = ProfileFactory::Get($profile_type);
				$oCompanyProfile->GetProfileById($id,$return = "PROFILE");
					
				$this->aCompanyProfile[] = $oCompanyProfile;
					
			} catch (Exception $e) {
				throw($e);		
			}
		}
		
	}
	
	public function LoadEmailTemplate() {

		global $_CONFIG;
		
		$this->sHtmlTemplatePath = $_CONFIG['root_path'].$_CONFIG['template_home']."/generic_html.php";
		$this->sTextTemplatePath = $_CONFIG['root_path'].$_CONFIG['template_home']."/generic_txt.php";
		
		
	}
	
	
	public function LoadAndPopulateEmailTemplate($oProfile) {
		
		global $oBrand;
		
		// @todo template -> object attribute mappings should be supplied via a campaign config object
		 
		$this->oTemplate = new Template();

 		$this->oTemplate->SetTemplatePath(PATH_2_DATA_DIR.$oBrand->GetWebsiteName().DIRECTORY_SEPARATOR);
		
		$this->oTemplate->SetFromArray(array(
				"TITLE" => $oProfile->GetTitle(),
				"ADMIN_UPDATE_URL" => $oBrand->GetAdminWebsiteUrl()."/update/".$oProfile->GetUrlName(),
				"PROFILE_URL" => $oBrand->GetWebsiteUrl() . $oBrand->GetCompanyBaseUrl() . DIRECTORY_SEPARATOR . $oProfile->GetUrlName(),
				"ACCOUNT_EXISTS" => $oProfile->GetUserAccountExists(),
				"ACCOUNT_USERNAME" => '',
				"ACCOUNT_PASSWORD" => ''
		));
		
		
		$this->oTemplate->LoadTemplate("campaign_update_listing.php");
		
		
		var_dump($this->oTemplate);
		die(__FILE__."::".__LINE__);
	}
	
	/*
	 * Main Message Send Loop
	 * 
	 */
	public function Send() {
		
		
		/*
		 * 1.  Parse in msg specific parameters
		 * 2.  Wrap in site specific msg template
		 * 3.  Send
		 * 
		 */
		
		foreach($this->GetCompanyProfile() as $oProfile) {
			
			// does this company have any login account associated?
			if ($oProfile->GetUserAccountExists()) {
				// get the details
				
			}
			
			$this->LoadAndPopulateEmailTemplate($oProfile);
		}
		
	}

	
}


?>