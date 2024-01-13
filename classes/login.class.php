<?

/*
 * Login Class
 *
 * - Handles requests to login
 * - authenticates supplied credentials against an identity repositary
 * - upon successful authentication sets up a valid user session
 * - sets up user permissions
 * - anti-spam captcha functionality
 * - optionally handles secondary authentication/session setup against a phpBB database
 *
 * Use -
 *
 *
 *
 * @created 25/02/2009
 */


class Login {

	private $id;
	private $company_id;
	private $uname;
	private $pass;
	private $cryptedpass;
	private $user_id;
	private $userIP;
	private $cookiename;
	private $cookie_domain;
	private $cookie_path;
	private $session_expires;
	private $recaptchCheckFl;
	private $passHashSalt;
	private $validCharRegex;
	private $aError;
	private $redirectUrl;
	
	private $bValid; // TRUE on valid login attempt

	const VALID_CHAR_REGEX = "[a-zA-Z0-9\W]";
	const IP_ADDRESS_ACCESS_CHECK = FALSE;
	
	public function __construct() {

		$this->bValid = FALSE;
		
		/* user details */
		$this->uname = "";
		$this->pass = "";
		$this->cryptedpass = "";
		$this->user_id = "";

		$this->recaptchCheckFl = FALSE; /* use recaptcha anti-spam verification? */
		$this->passHashSalt = ''; /* salt used to harden password hashing function */
		$this->validCharRegex = self::VALID_CHAR_REGEX; /* valid chars for username, password */

		//$this->redirectUrl = $_CONFIG['url'].$_CONFIG['login_redirect_url']; /* where to redirect to upon sucessful login */

		$this->aError = array();
	}
	
	public function GetCompanyId() {
		return $this->company_id;
	}
	
	public function GetErrors() {
		return $this->aError;
	}
	
	public function Valid() {
		return $this->bValid;
	}
	
	public function SetCookie($aParams) {
		/* cookie params */
		$this->cookiename = $aParams['cookiename'];
		$this->cookie_domain = $aParams['cookie_domain'];
		$this->cookie_path =  $aParams['cookie_path'];
		$this->session_expires = $aParams['cookie_expiry'];
		
	}
	
	public function SetRedirectUrl($url) {
		$this->redirectUrl = $url;
	}

	
	public function doPasswordReminder($sEmail,&$aError) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db;
		
		if ((strlen(trim($sEmail)) < 1) || (!Validation::IsValidEmail($sEmail))) {
			$aError['EMAIL_INVALID'] = "Please enter a valid email address.";
			return false;	
		}
		
		$res = $this->GetAccountByEmail($sEmail);
		if (!$res) {
			Logger::DB(1,get_class($this)."::".__FUNCTION__."()",'PASSWORD : EMAIL_NOT_FOUND : '.$sEmail . ' : '.IPAddress::GetVisitorIP());
			$aError['EMAIL_NOT_FOUND'] = "Email address not found.";
			return false;	
		}

		$new_password = $this->randomPassword();
		$encrypted_password = Login::generatePassHash($new_password,$salt = '');

		if (strlen($encrypted_password) > 1)
		{
			$sql = "UPDATE euser SET pass_hash = '".$encrypted_password."' WHERE id = ".$res['id'];
			$db->query($sql);
			if ($db->getAffectedRows() != 1)
			{
	                        Logger::DB(1,get_class($this)."::".__FUNCTION__."()",'PASSWORD : Password encryption error : '.$sEmail . ' : '.IPAddress::GetVisitorIP());
        	                $aError['PASSWORD_ENCRYPT'] = "Error generating new password.  Please contact us.";
                	        return false;   
			}
		} else {
                        Logger::DB(1,get_class($this)."::".__FUNCTION__."()",'PASSWORD : Password encryption error : '.$sEmail . ' : '.IPAddress::GetVisitorIP());
                        $aError['PASSWORD_ENCRYPT'] = "Error generating new password.  Please contact us.";
                        return false;
		}		

		$aMsgParams = array(
						'site_title' => 'One World 365',
						'login_url' => 'http://admin.oneworld365.org/login',
						'admin_email' => 'admin@oneworld365.org',
						'website_email' => 'website@oneworld365.org',
						'root_path'  => ROOT_PATH,
						'template_path'  => ROOT_PATH.'/templates'
						);

		$this->SendPasswordReminderEmail($res['email'],$res['uname'],$new_password,$aMsgParams);
		
		Logger::DB(2,get_class($this)."::".__FUNCTION__."()",'PASSWORD : REMINDER_SENT : '.$sEmail . ' : '.IPAddress::GetVisitorIP());
				
		return true;
				
	}

	
	/*
	 * $aMsgParams = array(
	 * 					'site_title' => ,
	 * 					'login_url' => ,
	 * 					'admin_email' => ,
	 *					'website_email' => ,
	 *					'root_path'  => ,
	 * 					'template_home'  =>
	 * 					);
	 */
	private function SendPasswordReminderEmail($sEmail,$sUname,$sPass, $aMsgParams) {
	
	
			$sMsg = "Login Details for ".$aMsgParams['site_title'].".\n\n";
			
			$sMsg .= "Url : ". $aMsgParams['login_url']." \n";
			$sMsg .= "Username : ". $sUname." \n";
			$sMsg .= "New Password : ". $sPass." \n\n";
			
			$sMsg .= "For further assistance please email ".$aMsgParams['admin_email'].".\n\n";
			$sMsg .= "With Thanks,\n\n";
			$sMsg .= $aMsgParams['site_title']."\n\n";
			
			$sTo = $sEmail;
			$sSubject = $aMsgParams['site_title']. " : Login Details";
			$sFromAddr = $aMsgParams['website_email'];
			$sReturnPath = $aMsgParams['admin_email'];
			
			$aParams = array( "MSG_TXT" => $sMsg,
							  "MSG_HTML" => nl2br($sMsg)
							);
			
			EmailSender::SendMail(		$aMsgParams['template_path']."/generic_html.php"
										,$aMsgParams['template_path']."/generic_txt.php"
										,$aParams
										,$sTo
										,$sSubject
										,$sFromAddr
										,$sReturnPath);
		
	}
	
	private function GetAccountByEmail($sEmail) {
		
		global $db;
		
		$db->query("SELECT id, uname, pass, email FROM euser WHERE email = '".$sEmail."'");
		
		if ($db->getNumRows() != 1) return false;
		
		return $aRow = $db->getRow(PGSQL_ASSOC,0);

	}

	private function GetAccountByCompanyId($id) {
	
		global $db;
	
		if (!is_numeric($id)) return array();
		
		$db->query("SELECT uname, pass, email FROM euser WHERE company_id = '".$id."'");
	
		if ($db->getNumRows() != 1) return false;
	
		return $aRow = $db->getRow(PGSQL_ASSOC,0);
	
	}
	

	/*
	 * Main login processing entry point
	 *
	 * @param string username
	 * @param string password
	 * @param string captcha challenge hash
	 * @param string captcha reponse hash
	 *
	 */
	public function doLogin($uname,$pass,$recaptcha_challenge = '',$recaptcha_response = '') {

		$this->uname = $uname;
		$this->pass = $pass;
		$this->recaptcha_challenge = isset($recaptcha_challenge) ? $recaptcha_challenge : "";
		$this->recaptcha_response = isset($recaptcha_response) ? $recaptcha_response : "";

		/* true represents error condition */
		switch (true) {

			case $this->isLoginComplete():
			case $this->validateStr($this->uname, $key = 'CREDENTIAL_UNAME', $msg = 'Please enter a valid username.'):
			case $this->validateStr($this->pass, $key = 'CREDENTIAL_PASSWD', $msg = "Please enter a valid password."):
			case $this->checkAccountStatus():
			case $this->ipAddressCheck():
			case $this->recaptchaCheck($this->recaptcha_challenge, $this->recaptcha_response):
			case $this->verifyEncryptedPassword();
			case $this->createSessionID():
			case $this->authenticate():
			case $this->setSessionCookieHeader():
				break;

				/* login OK : redirect to secure homepage */
			default:
				$this->bValid = TRUE;
		}


	}


	/* check uname & password are not empty */
	private function isLoginComplete() {

		if (!isset($this->uname) || $this->uname == "") {
			$this->aError['CREDENTIAL_UNAME'] = "Please enter a valid username.";
		}

		if (!isset($this->pass) || $this->pass == "") {
			$this->aError['CREDENTIAL_PASSWD'] = "Please enter a valid password.";
		}

		if (count($this->aError) >= 1) return true;
	}


	/* syntax check (uname & pass) */
	private function validateStr($str,$key, $errorMsg) {
		if (preg_match("/".$this->validCharRegex."/", $str)) {
			return false;
		} else {
			$this->aError[$key] = $errorMsg;
			return true;
		}
	}

	/*
	 *  Check that supplied username exists, that account is not locked and retrieve password hash salt
	 *
	 */
	private function checkAccountStatus() {

		global $db;

		$db->query("SELECT id,company_id, locked,pass_salt,ip FROM euser WHERE uname = '".$this->uname."'");

		if ($db->getNumRows() == 1) {
			$aRes = $db->getRow();
			$this->id = $aRes['id'];
			$this->company_id = $aRes['company_id'];
			$this->locked = $aRes['locked'];
			if ($this->locked == 1) {
				$this->aError['CREDENTIAL_ACCOUNT_LOCKED'] = "Your account has been suspended.  Please contact support";
				return true;
			}
			$this->passHashSalt = $aRes['pass_salt'];
			$this->userIP = $aRes['ip'];


		} else {
			$this->aError['CREDENTIAL_UNAME'] = "Please enter a valid username.";
			return true;
		}

	}


	private function ipAddressCheck() {

		if (!self::IP_ADDRESS_ACCESS_CHECK) return false; /* is IP address check turned on? (defined in config) */


		if ($this->userIP == "") return false; /* No IP restriction applied for this user */

		/* get the users IP address from the request (including if they arrived via a proxy) */
		$requestIP = "";
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$requestIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['REMOTE_IP'])) {
			$requestIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."() UserIP: ".$this->userIP);
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."() RemoteIp: ".$requestIP);

		if ($this->userIP != $requestIP) {
			$this->aError['IP_ADDR_CHECK_FAILED'] = "Access to your account is not permitted from this location.";
			return true;
		}

	}
 
	
	private function encryptPass() {

		$this->encryptedPass = password_hash($this->pass,PASSWORD_DEFAULT);

	}


	/* generate secure password hash - called by add user */
	public static function generatePassHash($pass,$salt) {

		return password_hash($pass,PASSWORD_DEFAULT);

	}


	/* random salt to harden password hashing function, called by add user */
	public static function generatePassHashSalt() {

		return substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);

	}


	/*
	 * Recaptcha Anti-Spam Check (http://recaptcha.net/)
	 *
	 */
	private function recaptchaCheck($challenge_field, $response_field) {

		global $_CONFIG;

		if (!$this->recaptchCheckFl) return false; /* is captcha checking enabled? */

		$resp = null; /* the response from reCAPTCHA */

		if (isset($_POST["recaptcha_response_field"]) && (strlen($_POST["recaptcha_response_field"]) < 1)) {
			$this->aError['CAPTCHA'] = "Please enter captcha text matching displayed word.";
			return true;
		}

		$resp = recaptcha_check_answer ($_CONFIG['CAPTCHA_PRIVATE_KEY'],
		$_SERVER["REMOTE_ADDR"],
		$challenge_field,
		$response_field);

		if (!$resp->is_valid) {
			$this->aError['CAPTCHA'] = "The anti-spam captcha text was not correct.";
			return true;
		}
	}

	private function randomPassword() {

    		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    		$pass = array(); //remember to declare $pass as an array
    		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    		for ($i = 0; $i < 8; $i++) {
	        	$n = rand(0, $alphaLength);
        		$pass[] = $alphabet[$n];
    		}
    		return implode($pass); //turn the array into a string
	}

	private function verifyEncryptedPassword()
	{

		global $db;

		$db->query("SELECT pass_hash FROM euser WHERE uname = '".$this->uname."'");

		if ($db->getNumRows() == 1) {

			$aRow = $db->getRow(PGSQL_ASSOC,0);
			$db_password_hash = $aRow['pass_hash'];

			if(password_verify($this->pass, $db_password_hash))
			{
				return false; 
			} else {
			    $this->aError['INVALID_PASSWORD'] = "Please enter a valid password";
			    return true;
			}
		} else {
		    $this->aError['INVALID_PASSWORD'] = "Please enter a valid password";
		    return true;
		}
	}

	/* authenticate user against database - password must be checked by verifyEncryptedPassword() before calling this func */
	private function authenticate() {

		global $db;

		if ($db->query("SELECT id FROM euser WHERE uname = '".$this->uname."'")) {

			if ($db->getNumRows() == 1) {
				$aRow = $db->getRow(PGSQL_ASSOC,0);
				$this->user_id = $aRow['id'];
				
				$db->query("UPDATE euser SET logins = logins+1, failed_logins = 0, last_login = CURRENT_TIMESTAMP, sess_id = '".$this->sess_id."' WHERE id = '".$this->user_id."';");
				
				$ip = (getenv('HTTP_X_FORWARDED_FOR')) ?  getenv('HTTP_X_FORWARDED_FOR') :  getenv('REMOTE_ADDR');
								
				Logger::DB(2,get_class($this)."::".__FUNCTION__."()",'LOGIN'.' Username: '.$this->uname." IP: ".$ip);
				return false;
			} else {
				$this->aError['CREDENTIAL_PASSWD'] = "Please enter a valid password.";
				$this->handleFailedLoginAttempt(); /* disable account if failed_logins > login attempts */
				return true;
			}
		} else {
			$this->aError['CREDENTIAL_TABLE_NOFOUND'] = "An error occured.  We are not able to process your login at this time.";
			Logger::DB(1,get_class($this)."::".__FUNCTION__."()",'ERROR : CREDENTIAL_TBL_WRITE_ERR ');
			return true;
		}
	}


	/* update failed logins counter, lock account if failed_logins > max_login_attempts */
	private function handleFailedLoginAttempt() {

		global $db;

		$iFailedLogins = $db->getFirstCell("SELECT failed_logins FROM euser WHERE id = ".$this->id);
		if (!is_numeric($iFailedLogins)) $iFailedLogins = 0;

		if ($iFailedLogins >= MAX_LOGIN_ATTEMPTS) {
			if (!is_numeric($id))
			{	
				$db->query("UPDATE euser SET locked = '1' WHERE id = ".$this->id );
			}
			Logger::DB(1,get_class($this)."::".__FUNCTION__."()",'LOGIN : ACCOUNT_LOCKED : '.'Username: '.$this->uname);
		} else {
			$iFailedLogins++;
			$db->query("UPDATE euser SET failed_logins = ".$iFailedLogins." WHERE id = ".$this->id );
		}
	}



	/* create unique md5 encrypted session token */
	private function createSessionID() {
		srand((double)microtime()*1000000);
		$this->sess_id = md5(uniqid(rand()));
	}


	private function setSessionCookieHeader() {

		$sess_id = $this->sess_id;
		$expires = time()+$this->session_expires;
		$cookiename =$this->cookiename;
		$domain = $this->cookie_domain;
		$path = $this->cookie_path;

		$COOKIE = "Set-Cookie: $cookiename=$sess_id";
		if (isset($expires) && ($expires > 0)) {
			$COOKIE .= "; EXPIRES=".gmdate("D, d M Y H:i:s",$expires) . " GMT";
		}
		if (isset($domain)) {
			$COOKIE .= "; DOMAIN=$domain";
		}
		if (isset($path)) {
			$COOKIE .= "; PATH=$path";
		}
		if (isset($secure) && $secure>0) {
			$COOKIE .= "; SECURE";
		}
		
		header($COOKIE,false);
	}




	public function doLogout($redirectUrl, $redirect = TRUE) {
		$this->sess_id = "";
		$this->setSessionCookieHeader();
		if ($redirect) {
			$this->doRedirect($redirectUrl);
		}
	}



	private function doRedirect($redirectUrl) {

		if (strlen($redirectUrl) < 1) die();

		?>
		<script type="text/javascript">
			window.location="<?= $redirectUrl; ?>";
		</script>
		<?
		die;
	}


	// ---------------------------------------------------------------------


} // end login class ----------------------------------------------



?>
