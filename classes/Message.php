<?

/* user messages */
define('MESSAGE_TYPE_SUCCESS','SUCCESS');
define('MESSAGE_TYPE_NOTIFICATION','NOTIFICATION');
define('MESSAGE_TYPE_ERROR','ERROR');
define('MESSAGE_TYPE_WARNING','WARNING');
define('MESSAGE_TYPE_FATAL_ERROR','FATAL_ERROR');
define('MESSAGE_TYPE_VALIDATION_ERROR','VALIDATION_OR_SAVE_ERROR');
define('MESSAGE_TYPE_404_NOTFOUND','404_NOTFOUND_ERROR');

// @deprecated - pass message as text 
define('MESSAGE_ID_ADD_COMPANY',0);
define('MESSAGE_ID_ADD_PLACEMENT',1);
define('MESSAGE_ID_EDIT_PROFILE',2);
define('MESSAGE_ID_ADD_PROFILE_FAILED',3);
define('MESSAGE_ID_DELETE_PLACEMENT',4);
define('MESSAGE_ID_GENERAL_ERROR',5);


define('MESSAGE_TEMPLATE','message_template_01.php');
define('MESSAGE_ICON_SUCESS_URL','/images/icon_green_tick.png');
define('MESSAGE_ICON_NOTIFICATION','/images/icon_notification.png');
define('MESSAGE_ICON_ERROR_URL','/images/icon_red_warning.jpg');
define('MESSAGE_ICON_FATAL_ERROR_URL','/images/red_cross.png');



class Message {

	private $id; // @deprecated - use message type / 
	private $type;	// message (alert) type eg ERROR, SUCCESS, WARNING
	private $msg_array;	// array of text strings
	private $icon_url; // @deprecated
	
	private $oTemplate;
	
	public function __Construct($type, $id, $msg = '') {
		$this->type = $type;
		$this->id = $id;
		$this->msg_array = array();
		if (is_array($msg)) {
			$this->msg_array = $msg;
		} elseif (strlen($msg) > 1) {
			$this->msg_array[] = $msg;
		}
		$this->SetTemplatePath();
	}
	
	public function GetId() {
		return $this->id;
	}
	
	public function GetType() {
		return $this->type;
	}
	
	public function SetMsgFromArray($aMsg) {
		if (is_array($aMsg)) {
			$this->msg_array = $aMsg;
		}
	}
	
	public function GetMsg() {

		$out = '';
		if (in_array($this->GetType(),array(MESSAGE_TYPE_ERROR))) {
			$error_count = count($this->msg_array);
			$plural = ($error_count > 1) ? "have" : "";
			$plural2 = ($error_count > 1) ? "(s)" : "";
			$out .= $error_count." error".$plural2." ".$plural." occured :";

			$out .= "<ul>";
			foreach($this->msg_array as $key => $msg) {
				$out .= "<li>".$msg."</li>";
			}
			$out .= "</ul>";			
			
		} else {
			$out .= $this->msg_array[0];
		}
		

		return $out;
	}
	
	public function GetIconHtml() {
		return "<img src='".$this->GetIconUrl()."' alt='' title='' style='vertical-align:middle;' />";
	}
	
	public function GetIconUrl() {
		return $this->icon_url;
	}
	
	private function SetIconUrl() {
		switch($this->GetType()) {
			case MESSAGE_TYPE_SUCCESS :
				$this->icon_url = MESSAGE_ICON_SUCESS_URL;
				break;
			case MESSAGE_TYPE_NOTIFICATION :
				$this->icon_url = MESSAGE_ICON_NOTIFICATION;
				break;
			case MESSAGE_TYPE_ERROR :
				$this->icon_url = MESSAGE_ICON_ERROR_URL;
				break;
			case MESSAGE_TYPE_FATAL_ERROR :
				$this->icon_url = MESSAGE_ICON_FATAL_ERROR_URL;
				break;		
		}
	}
	
	private function SetTemplatePath() { // could extend in future to use various template patterns
		$this->sTemplatePath = MESSAGE_TEMPLATE;
	}
	
	private function GetTemplatePath() {
		return $this->sTemplatePath;
	}
		
	public function Render() {
				
		$oMessage = new Template();
		$oMessage->Set('ID',$this->GetId());
		$oMessage->Set('TYPE',$this->GetType());
		$oMessage->Set('MSG',$this->GetMsg());
		$oMessage->Set('ICON_HTML',$this->GetIconHtml());
		$oMessage->LoadTemplate($this->GetTemplatePath());	
		
		return $oMessage->Render();

	}
	
}



?>