<?php



class UpdateListingController extends GenericController {
	
	private $company_url_name; // string validated unique company url token from request eg /company/<company-url-name>
	private $company_id; // int id of profile to be editted 
	private $oCompanyProfile; // object instance of company profile being editted (if listing = EXISTING)
	
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 
		
		//print __CLASS__."->".__FUNCTION__;
		
		global $oSession;
				
		
		try {
			// 1.  Attempt to retrieve company url name from request eg.  /update/<comp-url-name>
			$this->GetCompanyUrlNameFromRequest();
	
			//  Ensure the company url /update/<company-url-name>  exists
			$this->GetCompanyIdFromUrl();
			
			// get company profile details
			$this->GetCompanyProfileFromDb();
					
			// Setup session data for an update session
			$oSession = new Session;
			$oSession->SetListingType(LISTING_REQUEST_UPDATE);
			$oSession->SetCompanyId($this->GetCompanyId());
			$oSession->Save();
						
			//display login or register template
			$this->DisplayTemplate();
			
		} catch (Exception $e) {
			die($e->getMessage());
		}
		
	}
	
	private function GetCompanyUrlNameFromRequest() {
		
		$request_array = Request::GetUri("ARRAY");
		
		if ($request_array[1] != ROUTE_UPDATE) throw new Exception(ERROR_404_INVALID_REQUEST.implode("/",$request_array));
		
		$this->SetCompanyUrlName($request_array[2]);
		
		if (!Validation::ValidUriNamespaceIdentifier($request_array[2])) {
			throw new Exception(ERROR_COMPANY_PROFILE_INVALID_URL.$request_array[2]);
		}
		
	}
	
	
	protected function GetCompanyUrlName() {
		return $this->company_url_name;
	}
	
	protected function SetCompanyUrlName($company_url_name) {
		$this->company_url_name = $company_url_name;
	}
	
	private function GetCompanyIdFromUrl() {
		
		global $db;
		
		$oCompanyProfile = new CompanyProfile();
		$company_id = $oCompanyProfile->GetIdByUri($this->GetCompanyUrlName());
		if (!is_numeric($company_id)) throw new Exception(ERROR_COMPANY_PROFILE_NOT_FOUND.$this->GetCompanyUrlName());
		
		$this->SetCompanyId($company_id);
		
	}
	
	private function DisplayTemplate() {
		
		global $oHeader, $oFooter;
		
		$oRegistrationForm = new Template;

		$oRegistrationForm->Set('STEP_TITLE',"Login or Register");
		$oRegistrationForm->Set('COMPANY_NAME',$this->GetCompanyProfile()->GetTitle());
		
		//$oRegistrationForm->Set('VALIDATION_ERRORS',$this->GetValidationErrors());
		$oRegistrationForm->LoadTemplate("login_or_register.php");
				
		print $oHeader->Render();
		print $oRegistrationForm->Render();
		print $oFooter->Render();
		
	}
	
	protected function SetCompanyId($id) {
		if (is_numeric($id)) {
			$this->company_id = $id;
		}
	}
	
	protected function GetCompanyId() {
		return $this->company_id;
	}
	
	
	protected function GetCompanyProfileFromDb() {
		
		$oCompanyProfile = ProfileFactory::Get(PROFILE_COMPANY);
		$oCompanyProfile->GetProfileById($this->GetCompanyId(),$return = "PROFILE");

		$this->SetCompanyProfile($oCompanyProfile);
			
	}
	
	protected function SetCompanyProfile($oProfile) {
		$this->oCompanyProfile = $oProfile;
	}
	
	protected function GetCompanyProfile() {
		return $this->oCompanyProfile;
	}
	
}


?>