<?php

require_once(BASE_PATH."/classes/Mailshot.php");



class MailshotStep extends GenericStep {

	public $oMailshot;
	
	public function __construct( ){

		parent::__construct();

	}
	

	public function Process() {
	
		global $oHeader, $oFooter, $oAuth, $oSession, $oBrand;
	
		$this->oMailshot = new Mailshot;
		
		try {

			// so the mailshot manager know's which site template, profile type to use
			$this->oMailshot->SetSiteId($oBrand->GetSiteId());
			
			$this->oMailshot->LoadCSVCompanyData();
			
			$this->oMailshot->LoadCompanyProfileDetails();
			
			$this->oMailshot->LoadEmailTemplate();
			
			$this->oMailshot->Send();
			
			var_dump($this->oMailshot);
			
		} catch (Exception $e) {

			die(__FILE__."::".__LINE__."<br />".	$e->getMessage());
			
		}
		
		
		
		$oMailshot = new Template();
		//$oMailshot->Set('oSalesEnquiry',$oSalesEnquiry);
		$oMailshot->LoadTemplate("mailshot_template.php");
		

		print $oHeader->Render();
		print $oMailshot->Render();
		print $oFooter->Render();
		
	}
	

	
}