<?php



class ConfirmationStep extends GenericStep {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 
		
		global $oHeader, $oFooter, $oSession;
		
		
		//$oSession->Destroy();
		
		/* messages panel */
		$oMessagesPanel = new Layout();
		$oMessagesPanel->Set('USER_MSG',$this->GetUserMessages());		
		$oMessagesPanel->LoadTemplate("messages_template.php");
		
		
		//$oConfirmation = new Template;
		//$oConfirmation->LoadTemplate("account_request_confirmation.php");

		print $oHeader->Render();
		print $oMessagesPanel->Render();
		print $oFooter->Render();
		
		
	}
	

}


?>