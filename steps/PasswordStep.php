<?php



class PasswordStep extends GenericStep {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 

		global $oHeader, $oFooter;
		
		//print __CLASS__."->".__FUNCTION__;

		if (isset($_POST['submit'])) {
			
			$oLogin = new Login();

			$email = isset($_POST['email']) ? $_POST['email'] : "";

			$aError = array();
			$sent = FALSE;

        	if ($oLogin->doPasswordReminder($email,$aError)) {
        		
				$oMessage = new Message(MESSAGE_TYPE_SUCCESS, 'password_reset', $message = "We have emailed you a password reminder");
				$this->SetUserMessage($oMessage);

				$sent = TRUE;
        		
     	    } else {
				foreach($aError as $key => $value) {
					$oMessage = new Message(MESSAGE_TYPE_ERROR, $key, $value);
					$this->SetUserMessage($oMessage);		
				}     	    	
     	    }
     	    
		}
			
		$oPasswordForm = new Template;
		
		if ($sent) {
			$oPasswordForm->LoadTemplate("password_reminder_sent.php");
		} else {
			$oPasswordForm->LoadTemplate("password_reminder.php");
		}
		
		$oMessagesPanel = new Layout();
		$oMessagesPanel->Set('USER_MSG',$this->GetUserMsg());		
		$oMessagesPanel->LoadTemplate("messages_template.php");
		
		
		print $oHeader->Render();
		print $oMessagesPanel->Render();
		print $oPasswordForm->Render();
		print $oFooter->Render();
		
	}
	

}


?>