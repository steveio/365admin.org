<?php



class Session {
	
	private $listing_type; // NEW || EXISTING
	private $company_id; // int id of company profile if editting
	
	private $oStepController;
	
	private $error_code;
	private $error_data;
	private $error_msg;

	
	public function __Construct() {
		/*
		 * assume that all requests are to update an existing listing,
		 * unless new registration route has been followed
		 * 
		 */
		$this->SetListingType(LISTING_REQUEST_UPDATE); 
	}
	
	public function GetCompanyId() {
		return $this->company_id;
	}
	
	public function SetCompanyId($id) {
		if (is_numeric($id)) $this->company_id = $id;
	} 
	
	
	public function SetListingType($listing_type) {
		$this->listing_type = $listing_type;
	}
	
	public function GetListingType() {
		return $this->listing_type;
	}

	public function GetErrorCode() {
		return $this->error_code;
	}

	public function SetErrorCode($error_code) {
		$this->error_code = $error_code;
	}

	public function GetErrorData() {
		return $this->error_data;
	}

	public function SetErrorData($error_data) {
		$this->error_data = $error_data;
	}
	
	public function GetErrorMsg() {
		return $this->error_msg;
	}

	public function SetErrorMsg($error_msg) {
		$this->error_msg = $error_msg;
	}
	
	public function SetStepController($oStepController) {
		$this->oStepController = $oStepController;
	}
	
	public function GetStepController() {
		return is_object($this->oStepController) ? $this->oStepController : FALSE;
	}

	
	/* Session specific Methods */
	
	public function Exists() {
		return (isset($_SESSION['oSession'])) ? TRUE : FALSE;
	}
	
	public function Create() {
		return new Session;
	}
	
	public function Save() {
		$_SESSION['oSession'] = serialize($this);
	}
	
	public function Destroy() {
		session_destroy();
	}
	
	public function Get() {
			
		$oSession = unserialize($_SESSION['oSession']);

		if (is_object($oSession) && ($oSession instanceof Session)) {
			return $oSession;
		}
	}
	
	public function NewSession() {
		$this->Destroy();
		$oSession = $this->Create();
		$oSession->Save();
		return $oSession;
	}
}