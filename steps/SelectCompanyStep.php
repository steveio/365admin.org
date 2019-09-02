<?php



class SelectCompanyStep extends GenericStep {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 
		
		global $oHeader, $oFooter, $oSession;
		
		$oSelectCompany = new Template;

		$step_no = ($oSession->GetListingType() == "NEW") ? " - Step 2 of 4" : "";
		$oSelectCompany->Set('STEP_NO',$step_no);
		
		$oSelectCompany->LoadTemplate("select_company_template.php");
		
		
		print $oHeader->Render();
		print $oSelectCompany->Render();
		print $oFooter->Render();
		
	}
	

}


?>