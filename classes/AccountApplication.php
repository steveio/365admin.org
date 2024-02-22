<?php 



class AccountApplication {

	private $id; 
	
	
	public function __Construct() {
		
	}
	
	
	public function Validate($a,&$aResponse) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		if (!is_array($aResponse['msg'])) $aResponse['msg'] = array();
		
		if (strlen(trim($a['name'])) < 1) {
			$aResponse['msg']['name'] = "Please enter your name.";
		}

		if (strlen(trim($a['tel'])) < 1) {
			$aResponse['msg']['tel'] = "Please provide a contact number including all dialing codes.";
		}
		
		if (strlen(trim($a['role'])) < 1) {
			$aResponse['msg']['role'] = "Please tell us your position / role eg Marketing Director.";
		}
		
		if (strlen(trim($a['comments'])) > 1990) {
			$aResponse['msg']['comments'] = "Comments should be no more than 2000chars.";
		}
		
		
		if (strlen($a['email']) < 1 )  {
			$aResponse['msg']['email'] = "Please enter a valid email address.";
		}
		
		if (!Validation::IsValidEmail($a['email']) ) {
			$aResponse['msg']['email'] = "Please enter a valid email address.";
		}

		/* check that the email address is unique */
		global $db,$_CONFIG;
		$db->query("SELECT id FROM act_app WHERE email = '".$a['email']."'");
		if ($db->getNumRows() >= 1) {
			$aResponse['msg']['email'] = "An account already exists with this email.  Please <a href='/".ROUTE_LOGIN."'>click here</a> to login, or email ".$_CONFIG['admin_email']." for assistance.";
		}
		/* check the euser table too... */
		$db->query("SELECT id FROM euser WHERE uname = '".$a['email']."'");
		if ($db->getNumRows() >= 1) {
			$aResponse['msg']['email'] = "An account already exists with this email.  Please <a href='/".ROUTE_LOGIN."'>click here</a> to login, or email ".$_CONFIG['admin_email']." for assistance.";
		}
		
		if (!is_numeric($a['country_id'])) {
			$aResponse['msg']['country_applicant'] = "Please specify which country you are in.";
		}

		if (($a['listing_type']) == "null") {
			$aResponse['msg']['listing_type'] = "Please choose a listing type.";
		}

		if ((strlen(trim($a['password'])) < 4) || (strlen(trim($a['password'])) > 20)) {
			$aResponse['msg']['password'] = "Password should be between 4 and 20 characters.";
		}
		
		if (!preg_match("/[a-zA-Z0-9\W]/",$a['password'])) {
			$aResponse['msg']['password'] = "Password should contain only letters, numbers and punctuation (special characters).";
		}

		if (trim($a['password']) !=  trim($a['password_confirm'])) {
			$aResponse['msg']['password_confirm'] = "Password and password confirm must match.";
		}
		
		
		
		if (is_array($aResponse['msg']) && (count($aResponse['msg']))) {
			return false;
		}
		
		return true;
		
	}	
	

	public function Add($a,&$aResponse) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		global $db,$_CONFIG;
		
		if (!is_array($a)) {
			$aResponse['msg']['general'] = "Error (missing_params): Sorry we were unable to process your request at this time";
			return false;
		}
		
		if (!is_numeric($a['company_id'])) {
			$aResponse['msg']['general'] = "Sorry we were unable to process your request at this time";
			return false;
		}
		
		
		$a['apply_date'] = "now()::timestamp";
		$a['approved'] = 'N';
		$a['ip_address'] = IPAddress::GetVisitorIP();
		
		$this->id = $db->getFirstCell("SELECT nextval('act_app_seq')");
		
		foreach($a as $v) $v = (is_string($v)) ? pg_escape_string(htmlentities(trim($v),ENT_NOQUOTES)) : $v;

		$sql = "INSERT INTO act_app (
									id
									,sid
									,company_id
									,name
									,role
									,email
									,password
									,tel
									,country
									,account_type
									,account_code
									,comments
									,approved
									,apply_date
									,ip_address
								) VALUES (
									".$this->GetId()."
									,".$_CONFIG['site_id']."
									,".$a['company_id']."
									,'".addslashes($a['name'])."'
									,'".addslashes($a['role'])."'
									,'".$a['email']."'
									,'".$a['password']."'
									,'".$a['tel']."'
									,".$a['country_id']."
									,'".$a['account_type']."'
									,'".$a['listing_type']."'
									,'".addslashes($a['comments'])."'
									,'".$a['approved']."'
									,".$a['apply_date']."
									,'".$a['ip_address']."');";
		
		
		if (!$db->query($sql)) {
			$aResponse['msg']['general'] = "Error: There was a problem processing your application, we will look into it.";
			return false;
		} else {
	 		return true;
		}
	
	}
	
	public function SetId($id) {
		$this->id = $id;
	}
	
	public function GetId() {
		return $this->id;
	}

	function GetPendingList() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		global $db,$_CONFIG;
		
		$sql = "SELECT 
					 a.*
					 ,w.name as website_name
					,c.title as company_name
					,c.url_name as comp_url_name
					,c.status as comp_status
					,cty.name as country_name 
				FROM 
					 act_app a
					,website w
					,company c
					,country cty 
				WHERE 
					a.sid = w.id
				AND a.company_id = c.id 
				AND a.country = cty.id 
				AND a.approved = 'f' 
				AND a.rejected != 'f' 
				ORDER BY 
					a.apply_date DESC";
		
		$db->query($sql);
		
		$aAccount = $db->getObjects();
		
		if (!is_array($aAccount) || count($aAccount) < 1) return array();

		for($i=0;$i<count($aAccount);$i++) {
			$aAccount[$i]->name = stripslashes($aAccount[$i]->name);
			$aAccount[$i]->role = stripslashes($aAccount[$i]->role);			
			$aAccount[$i]->comments = stripslashes($aAccount[$i]->comments);
			
			$aAccount[$i]->company_type = ($aAccount[$i]->comp_status == 0) ? "NEW" : "EXISTING";
			$aAccount[$i]->company_profile_link = $_CONFIG['url'] . "/company/" .$aAccount[$i]->comp_url_name;
			
			if ($aAccount[$i]->account_type == 0) $aAccount[$i]->account_name = "Free";
			if ($aAccount[$i]->account_type == 1) $aAccount[$i]->account_name = "Enhanced";
			if ($aAccount[$i]->account_type == 2) $aAccount[$i]->account_name = "Sponsored";
						
		}

		return $aAccount;
		
	}

	
	function GetRecentList() {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		global $db,$_CONFIG;
		
		$sql = "SELECT 
					 a.*
					,to_char(a.apply_date,'DD-MM-YYYY') as receieved
					,c.title as company_name
					,c.url_name as comp_url_name
					,c.status as comp_status
					,cty.name as country_name
					,w.name as website_name 
				FROM 
					 act_app a
					,website w
					,company c
					,country cty 
				WHERE 
					a.sid = w.id
				AND a.company_id = c.id 
				AND a.country = cty.id  
				ORDER BY 
					a.apply_date DESC
				LIMIT 30
					";
		
		$db->query($sql);
		
		$aAccount = $db->getObjects();		
		
		for($i=0;$i<count($aAccount);$i++) {
			$aAccount[$i]->name = stripslashes($aAccount[$i]->name);
			$aAccount[$i]->role = stripslashes($aAccount[$i]->role);			
			$aAccount[$i]->comments = stripslashes($aAccount[$i]->comments);
			
			$aAccount[$i]->company_type = ($aAccount[$i]->comp_status == 0) ? "NEW" : "EXISTING";
			$aAccount[$i]->company_profile_link = $_CONFIG['url'] . "/company/" .$aAccount[$i]->comp_url_name;
			
			if ($aAccount[$i]->account_type == 0) $aAccount[$i]->account_name = "Free";
			if ($aAccount[$i]->account_type == 1) $aAccount[$i]->account_name = "Enhanced";
			if ($aAccount[$i]->account_type == 2) $aAccount[$i]->account_name = "Sponsored";
			
			if ($aAccount[$i]->rejected == "t") {
				$aAccount[$i]->status = "Rejected";
			} elseif ($aAccount[$i]->approved  == "t") {
				$aAccount[$i]->status = "Approved";
			} else {
				$aAccount[$i]->status = "Pending";
			}
			
		}

		return $aAccount;
		
	}
	
	
	function GetById($id) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		global $db,$_CONFIG;
		
		$sql = "SELECT 
					 a.*
					,c.title as company_name
					,c.url_name as comp_url_name
					,c.status as comp_status
					,cty.name as country_name 
				FROM 
					 act_app a
					,company c
					,country cty 
				WHERE 
					a.company_id = c.id
					AND a.country = cty.id 
					AND a.id = ".$id." 
				ORDER BY a.apply_date DESC";
		
		$db->query($sql);
		
		if ($db->getNumRows() != 1) return false;
		
		$oAccount = $db->getObject();
		
		$oAccount->name = stripslashes($oAccount->name);
		$oAccount->role = stripslashes($oAccount->role);			
		$oAccount->comments = stripslashes($oAccount->comments);
		$oAccount->status = ($oAccount->comp_status == 0) ? "PENDING" : "APPROVED";
		$oAccount->company_profile_link = $_CONFIG['url'] . "/company/" .$oAccount->comp_url_name;
		
		
		if ($oAccount->account_type == 0) $oAccount->account_name = "Free";
		if ($oAccount->account_type == 1) $oAccount->account_name = "Enhanced";
		if ($oAccount->account_type == 2) $oAccount->account_name = "Sponsored";

		return $oAccount;

	}
	
	
	function Approve($oAccount,$username,$password,&$response) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		global $db,$_CONFIG;
		
		/* validate username / password */ 
		if (strlen($username) < 1) {
			$response['msg'] = "Error: Please enter a username";
			return false;
		}
			
		if (strlen($password) < 1) {
			$response['msg'] = "Error: Please enter a password";
			return false;
		}
		
		/* sanitise username / password */
		$username = preg_replace("[^a-zA-Z0-9]","",$username);
		$password = preg_replace("[^a-zA-Z0-9]","",$password);
		
		/* setup a user record for the new account */

		if (!is_numeric($oAccount->company_id)) {
			$response['msg'] = "Account::Approve() ERROR: invalid company_id";
			return false;
		}
		
		/*
		 * Set Comp Profile Listing Type & Insert a Row in Listing Table 
		 *
		 */
		if (($oAccount->account_type == BASIC_LISTING) || ($oAccount->account_type == ENHANCED_LISTING)) {

			/*
			 * Set listing type (prod_type) field and default profile quota on company record   
			 */
			switch($oAccount->account_type) {
				case BASIC_LISTING :
					$iProfileQuota = BASIC_PQUOTA;
					break;
				case ENHANCED_LISTING :
					$iProfileQuota = ENHANCED_PQUOTA;
					break;
				default :
					$iProfileQuota = FREE_PQUOTA;
					break;
			}
			
			$db->query("UPDATE COMPANY set status = 1, prod_type = ".$oAccount->account_type.", job_credits = ".$iProfileQuota." WHERE id = ".$oAccount->company_id);
			if ($db->getAffectedRows() != 1) {
				$response['msg'] = "Account::Approve() ERROR: set company status approved db write error";
				return false;
			}

			$oListing = new Listing();
			$oListing->SetActiveByCompanyId($oAccount->company_id,"F");
			$oListing->SetFromArray(ListingOption::GetByCode($oAccount->account_code));
			$oListing->SetCompanyId($oAccount->company_id);

			if (!$oListing->Add()) {
				return false;
			}	
		}
		
		/* map feilds so they can be processed by $oUser->addUser(); */
		$_POST['p_name'] = addslashes($oAccount->name);
		$_POST['p_email'] = $oAccount->email;
		$_POST['p_uname'] = $username;
		$_POST['p_pass'] = $password;		
		$_POST['p_company'] = $oAccount->company_id;
		$_POST['p_access_level'] = 0;
		
		$oUser = new User($db);
		if ($oUser->addUser()) {

			
			$sMsg = "An account on ".$_CONFIG['site_title']." for ".$oAccount->company_name." has been setup.\n\n";
			$sMsg .= "The login details are :\n\n";
			$sMsg .= "Username : ".$oAccount->email."\n";
			$sMsg .= "Password : ".$oAccount->password."\n\n";
			$sMsg .= "You can login here :\n";
			$sMsg .= $_CONFIG['url']."/login/ \n\n";
			$sMsg .= "Follow link \"my account\" (in the top right hand menu) to edit your profile.\n\n";
			
			if ($oAccount->account_type < 1) {
				$sMsg .= "Enhanced and Premium listings allow you to improve your organisation's presentation, gain increased exposure and drive more traffic to your website.\n\n";
				$sMsg .= "Contact us for details on upgrading your listing \n\n";
				$sMsg .= "We would also appreciate if you could provide a link to our websites. This could be in the form of a text link, adding our logo's and saying you are listed with us, or just putting a link to us on your social network pages.\n\n";
		
			}
			
			$sMsg .= "If you need help at any time please email ".$_CONFIG['admin_email']." and we will be happy to assist.\n\n";
			$sMsg .= "With Thanks,\n\n";
			$sMsg .= $_CONFIG['site_title']."\n\n";
			
			$sTo = $oAccount->email;
			$sSubject = $_CONFIG['site_title']. " : Login Details";
			$sFromAddr = $_CONFIG['website_email'];
			$sReturnPath = $_CONFIG['admin_email'];
			
			$aMsgParams = array("MSG_TXT" => $sMsg,
									  "MSG_HTML" => nl2br($sMsg));
			
			EmailSender::SendMail($_CONFIG['root_path']."/".$_CONFIG['template_home']."/generic_html.php"
										,$_CONFIG['root_path'].$_CONFIG['template_home']."/generic_txt.php"
										,$aMsgParams
										,$sTo
										,$sSubject
										,$sFromAddr
										,$sReturnPath);
										

			/* mark the account pending row as approved */
			$db->query("UPDATE act_app SET approved = 'Y' WHERE id = ".$oAccount->id);
			
			/* if we are processing a paid listing, set company listing_type */
						
			return true;
		} else {
			Logger::DB(1,"Account::Approve()","ERROR: Add User : ".serialize($oUser));
			$response['msg'] = $oUser->msg;
			return false;
		}
		
	}	

	/*
	 *  Reject an application for an account
	 * 
	 * 
	 */
	public function Reject($oAccount,&$response) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		global $db;
		
		if ( (!is_object($oAccount)) || (!is_numeric($oAccount->company_id)) ||  (!is_numeric($oAccount->id)) ) {
			$response['msg'] = "ERROR : Missing parameter to reject company"; 
			return false;
		}
					
		/* if application includes new company details - delete company row */
		$oCompany = new Company($db);

		/* only delete company if it has been added during a rejected new application */
		if ($oAccount->comp_status == 0) {
			if (!$oCompany->Delete($oAccount->company_id)) {
				$response['msg'] = "ERROR : a problem occured deleting the company.";
				return false; 
			}
		}
		
		/* set the account application row status to rejected */
		$db->query("UPDATE act_app SET rejected = 'T' WHERE id = ".$oAccount->id);
		
		if ($db->getAffectedRows() != 1) {
			$response['msg'] = "ERROR : a problem occured rejecting user account.";
			return false;
		}
				
		return true;
	}

	public function NotifyAdmin($aMessageParams) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
		global $_CONFIG;

		if (!is_array($aMessageParams)) return FALSE;
				
		/* notify admin by email */
		$to = $aMessageParams['to'];
		$subject = $aMessageParams['website_name']. " : New Listing Request";
		$headers = "From: ".$aMessageParams['from']."\r\nReply-To: ".$aMessageParams['from'];
				
		$message .= "A new listing on ".$aMessageParams['website_name']." was requested : \n\n\r";
		$message .= "Name: ".$aMessageParams['name']."\n\r";
		$message .= "Role: ".$aMessageParams['role']."\n\r";
		$message .= "Email: ".$aMessageParams['email']."\n\r";
		$message .= "Telephone: ".$aMessageParams['telephone']."\n\r";
		$message .= "Company Name: ".$aMessageParams['company_name']."\n\r";
		$message .= "Comments: ".$aMessageParams['comments']."\n\r\n\r";
		 
		if ($aMessageParams['company_status'] == 0) { /* new company */
			$message .= $aMessageParams['company_name'] . " is a new company.\n\r";
			$message .= "View Company : ".$aMessageParams['website_url']."/company/".$aMessageParams['company_url_name']." \n\n";
		}
		$message .= "The new profile will not be available on the site until approved.\n\n";
		$message .= "Please login to view and approve/reject the request.";

		if (mail($to,$subject,$message,$headers)) {
			return TRUE;
		}

	}
	
}
?>
