<?php



class Session {
	
	private $listing_type; // NEW || EXISTING
	private $company_id; // int id of company profile if editting
	
	private $oMVCController;
	
	private $error_code; // @deprecated - use $aMessage
	private $error_data;
	private $error_msg;

	/*
	 * UI Message can be stored directly in _SESSION
	 * 
	 * MVC routes can also persist messages in _SESSION via controller / MVC controller
	 *
	 */
	private $aMessage;
	
	public function __Construct() {
		/*
		 * assume that all requests are to update an existing listing,
		 * unless new registration route has been followed
		 * 
		 */
		$this->SetListingType(LISTING_REQUEST_UPDATE);
		
		//$this->aMessage = array();
	}

	// Used by AJAX webservice endpoints and standalone scripts without common init routine
	public static function initSession()
	{
	    /* start a new session */
	    if (ini_get('session.use_cookies') && isset($_COOKIE['PHPSESSID'])) {
	        $sessid = $_COOKIE['PHPSESSID'];
	    } elseif (!ini_get('session.use_only_cookies') && isset($_GET['PHPSESSID'])) {
	        $sessid = $_GET['PHPSESSID'];
	    }
	    
	    session_start();
	    
	    $oSession = new Session();
	    
	    if ($oSession->Exists()) {
	        $oSession = $oSession->Get();
	    } else {
	        $oSession = $oSession->Create();
	    }

	    return $oSession;
	}

	public function GetCompanyId() {
		return $this->company_id;
	}
	
	public function SetCompanyId($id) {
		if (is_numeric($id)) $this->company_id = $id;
	} 
	
	
	public function SetListingType($listing_type) {
		$this->listing_type = $listing_type;
	}
	
	public function GetListingType() {
		return $this->listing_type;
	}

	public function SetMessage($msg) 
	{

        if (is_array($msg))
        {
            $this->aMessage = $msg;
        } elseif (is_object($msg))
        {
           $this->aMessage[] = $msg;
        }
	
	    $this->Save();
	}
	
	public function GetMessage() {
	    return $this->aMessage;
	}

	public function UnsetMessage() {
	    unset($this->aMessage);
	    $this->aMessage = array();
	    $this->Save();
	}

	// @deorecated - use $this->aMessage
	public function GetErrorCode() {
	    return $this->error_code;
	}

	// @deorecated - use $this->aMessage
	public function SetErrorCode($error_code) {
		$this->error_code = $error_code;
	}

	// @deorecated - use $this->aMessage
	public function GetErrorData() {
		return $this->error_data;
	}

	// @deorecated - use $this->aMessage
	public function SetErrorData($error_data) {
		$this->error_data = $error_data;
	}

	// @deorecated - use $this->aMessage
	public function GetErrorMsg() {
		return $this->error_msg;
	}

	// @deorecated - use $this->aMessage
	public function SetErrorMsg($error_msg) {
		$this->error_msg = $error_msg;
	}
	
	public function SetMVCController($oMVCController) {
		$this->oMVCController = $oMVCController;
	}
	
	public function GetMVCController() {
		return is_object($this->oMVCController) ? $this->oMVCController : FALSE;
	}

	
	/* Session specific Methods */
	
	public function Exists() {
		return (isset($_SESSION['oSession'])) ? TRUE : FALSE;
	}
	
	public function Create() {
		return new Session;
	}
	
	public function Save() {
		$_SESSION['oSession'] = serialize($this);
	}
	
	public function Destroy() {
		session_destroy();
	}
	
	public function Get() {
			
		$oSession = unserialize($_SESSION['oSession']);

		if (is_object($oSession) && ($oSession instanceof Session)) {
			return $oSession;
		}
	}
	
	public function NewSession() {
		$this->Destroy();
		$oSession = $this->Create();
		$oSession->Save();
		return $oSession;
	}
}