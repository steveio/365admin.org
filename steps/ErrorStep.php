<?php



class ErrorStep extends GenericStep {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 
		
		global $oHeader, $oFooter , $oSession;

		$oMessagesPanel = new Layout();
		
		if (!is_object($oSession->GetMVCController())) { // no session, so don't look for any messages there...			
			$oMessage = new Message(MESSAGE_TYPE_ERROR, 'SESSION_EXPIRED_MSG', "Sorry, no valid session or session expired.  To continue <a href='/".ROUTE_LOGIN."'>click here</a> to login...");
			$aMessage = array();
			$aMessage[] = $oMessage;
			$oMessagesPanel->Set('UI_MSG',$aMessage);			
		} else { // valid session but no message passed in, display a default error message
			if (count($this->aUserMessages) == 0) {
				$oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_ID_GENERAL_ERROR, $message = "Sorry, an error has occured and it was not possible to fulfil your request.  We've logged the error and will look into it.  Contact us for assistance.");
				$this->SetUserMessage($oMessage);
				$oMessagesPanel->Set('UI_MSG',$this->GetUserMsg());			
			}
		}

		$oMessagesPanel->LoadTemplate("messages_template.php");

		print $oHeader->Render();
		print $oMessagesPanel->Render();
		print $oFooter->Render();
		
		
	}
	

}


?>