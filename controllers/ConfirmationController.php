<?php



class ConfirmationController extends GenericController {

	public function __construct( ){

		parent::__construct();

	}

	public function Process() {

		global $oHeader, $oFooter, $oSession;

		/* messages panel */
		$oMessagesPanel = new Layout();
		$oMessagesPanel->Set('UI_MSG',$this->GetUserMessages());
		$oMessagesPanel->LoadTemplate("messages_template.php");


		//$oConfirmation = new Template;
		//$oConfirmation->LoadTemplate("account_request_confirmation.php");

		print $oHeader->Render();
		print $oMessagesPanel->Render();
		print $oFooter->Render();


	}


}


?>
