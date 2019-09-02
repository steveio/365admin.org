<?php



class NewListingStep extends GenericStep {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 
		
		global $oSession;
		
		$oSession = new Session;
		$oSession->SetListingType(LISTING_REQUEST_NEW);
		$oSession->Save();

		
		Http::Redirect(BASE_URL."/".ROUTE_REGISTRATION);
		
	}
	

}


?>