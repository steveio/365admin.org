<?php



class SignupController extends GenericController {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 
		
		global $oSession;
		
		$oSession = $oSession->NewSession();
		$oSession->SetListingType("NEW");
		$oSession->Save();
		
		Http::Redirect(BASE_URL."/".ROUTE_REGISTRATION);
		
	}
	

}


?>