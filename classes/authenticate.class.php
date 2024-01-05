<?php
/*
*
* Authenticate.class
*
* Handles secure access, user account and permissions.
*
*/




class Authenticate {

	public function __Construct(&$db,$redirect = false, $redirect_url = '', $cookiename = '') {
		
		global $_CONFIG;
		
		$this->db = $db;
		$this->cookiename = $cookiename;
		$this->redirect = $redirect; // redirect on authenticate failure?
		$this->redirect_url = $redirect_url; // where to redirect a nonauthorised user to
		$this->isError = ""; // error flag
		$this->errorMsg = ""; // error condition
		$this->sessionID = ""; // unique user session ID
		$this->oUser = null; // a valid user object
	}

	public function IsValidUser() {
		if (is_object($this->oUser)) {
			return $this->oUser->isValidUser;
		}
	}

	public function ValidSession() {
		// true represents error condition
		switch (true) {
			case $this->getSessionCookie():
			case $this->getUserInfo():

			// user is not authenticated
			$this->authenticateFailed();
			break;

			default:
				return TRUE; // user is authenticated
		}
	}


	// get client side session cookie
	private function getSessionCookie() {
		if (!isset($_COOKIE[$this->cookiename])) {
			return TRUE;
			
		}
		$this->sessionId =  $_COOKIE[$this->cookiename];
		if ($this->sessionId == "") {
			$this->errorMsg = "Not Authenticated or Session Expired  -  Please Login...";
			return TRUE;
		}
	}


	// get user info
	private function getUserInfo() {

		$this->oUser = new User($this->db);

		$this->oUser->getUserBySessionId($this->sessionId);

		if (!$this->oUser->isValidUser) {
			$this->errorMsg = "Session expired - Please login to continue";
			return true;
		}
	}


	// authentication result handlers ------------------------------------------------

	// authenticate failed : display error msg and redirect 2 login page
	private function authenticateFailed() {
		$this->isError = true;
	}


	public function Redirect() {
		header('Location: '.$this->redirect_url);
		die();
	}



	// -----------------------------------------------------------------------


} // end authentication class -----------------------------------------------
?>
