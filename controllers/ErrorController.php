<?php



class ErrorController extends GenericController {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 
		
		global $oHeader, $oFooter , $oSession;

		$oMessagesPanel = new Layout();
		
		if (!is_object($oSession)) { // no session or session expired

			$oMessage = new Message(MESSAGE_TYPE_ERROR, 'SESSION_EXPIRED_MSG', "Sorry, no valid session or session expired.  To continue <a href='/".ROUTE_LOGIN."'>click here</a> to login...");
			$oMessagesPanel->Set('UI_MSG', array($oMessage));

		} elseif (count($oSession->GetMessage()) >= 1) {

		    $oMessagesPanel->Set('UI_MSG', $oSession->GetMessage());

		} else { // valid session but no message passed in, display a default error message
			if (count($this->aMessage) == 0) {
				$oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_ID_GENERAL_ERROR, $message = "Sorry, an error has occured and it was not possible to fulfil your request.  We've logged the error and will look into it.  Contact us for assistance.");
				$this->SetMessage($oMessage);
				$oMessagesPanel->Set('UI_MSG',$this->GetMessage());
				// GetUserMsg
			}
		}

		$oMessagesPanel->LoadTemplate("messages_template.php");

		print $oHeader->Render();
		print $oMessagesPanel->Render();
		print $oFooter->Render();
		
		
	}
	

}


?>