<?php



class LoginStep extends GenericStep {

	private $forward_uri;
	
	public function __construct( ){
		
		parent::__construct();

		$this->forward_uri = '';
	}
	
	private function SetForwardUri($uri) {
		$this->forward_uri = $uri;
	}
	
	private function GetForwardUri() {
		return $this->forward_uri;
	}
	
	public function Process() { 

		global $oHeader, $oFooter, $oBrand, $oAuth;

		if ($oAuth->oUser->isValidUser)
		{
		    Http::Redirect("/".ROUTE_DASHBOARD);
		    die();
		}
		
		/* look for a referer url ie anything after /login */
		$request_array = Request::GetUri("ARRAY");
		$request_array = array_slice($request_array, 2);
		$this->SetForwardUri(implode("/",$request_array));
		
		
		/* Handle Login Request */
		if (isset($_POST["login"])) {
			if (isset($_POST['FORWARD_URI']) && strlen($_POST['FORWARD_URI']) > 1) {
				$this->SetForwardUri($_POST['FORWARD_URI']);
			}
			$this->UnsetValidationErrors();
			$this->Login();
		}
		
		
		$oHeader->SetTitle("Login");		
		$oHeader->Reload();
		
		$oLoginForm = new Template;
		$oLoginForm->Set('BRAND_NAME',$oBrand->GetBrandName());
		
		$oLoginForm->Set('FORWARD_URI',$this->GetForwardUri());
		$oLoginForm->Set('aError',$this->GetValidationErrors());
		$oLoginForm->LoadTemplate("LoginTemplate.php");
		
		print $oHeader->Render();
		print $oLoginForm->Render();
		print $oFooter->Render();
		
	}
	
	private function Login() {

		$oLogin = new Login();
	
		$aParams['cookiename'] = COOKIE_NAME;
		$aParams['cookie_domain'] = COOKIE_DOMAIN;
		$aParams['cookie_path'] = COOKIE_PATH;
		$aParams['cookie_expiry'] = COOKIE_EXPIRES;
		
		$oLogin->SetCookie($aParams);
	
		$uname = isset($_POST['uname']) ? $_POST['uname'] : ""; 
		$pass = isset($_POST['pass']) ? $_POST['pass'] : "";
		
		$oLogin->doLogin($uname,$pass,$recaptcha_challenge,$recaptcha_response);
	
		if ($oLogin->Valid()) {
			if (strlen($this->GetForwardUri()) > 1) { // a specific uri was requested, forward here
				Http::Redirect("/".$this->GetForwardUri());
			} else { // forward to dashboard
				global $oSession;
				$oSession->SetListingType("EXISTING");
				$oSession->SetCompanyId($oLogin->GetCompanyId());
				$oSession->Save();
				Http::Redirect("/".ROUTE_DASHBOARD);
			}
		} else {
			$this->SetValidationErrors( $oLogin->GetErrors() );			
		}

	}
	

	public function KillSession() {
		
		global $oAuth,$oSession;
		
		// invaldate any existing session data
		$oLoginService = new Login;
		$aParams = array();
		$aParams['cookiename'] = COOKIE_NAME;
		$aParams['cookie_domain'] = COOKIE_DOMAIN;
		$aParams['cookie_path'] = COOKIE_PATH;
		$aParams['cookie_expiry'] = COOKIE_EXPIRES;
		$oLoginService->SetCookie($aParams);
		$oLoginService->doLogout('',FALSE);
		unset($oAuth);
		$oSession->NewSession();
		
	}
}


?>
