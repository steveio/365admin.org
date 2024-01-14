<?




class User {

	var $id;
	var $name;
	var $email;
	var $uname;
	var $access_level;
	var $pass;
	var $sess_id;
	var $added;
	var $last_login;
	var $logins;
	var $company_id;
	var $isValidUser;
	var $isAdmin;

	public function __construct(&$db) {

		$this->User($db);
    }

	function User(&$db) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		$this->db = $db;

		$this->isValidUser = false; // is the user authenticated?
		$this->isAdmin = false; // is this a Super User?
	}
	
	public function GetId() {
		return $this->id;
	}
	
	public function GetCompanyId() {
		return $this->company_id;	
	}

	function getUserBySessionId($sessId) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");

		$this->db->query ("SELECT 
								u.*, 
								c.id as company_id,
								c.title as company, 
								c.url_name as comp_url_name,
								c.prod_type,
								c.enq_opt,
								c.prof_opt
							FROM 
								euser u, 
								company c 
							WHERE 
								u.company_id = c.id 
							AND u.sess_id = '".$sessId."';");

		if ($this->db->getNumRows() == 1) {

			$this->isValidUser = true;

			$oResult = $this->db->getObject();

			foreach($oResult as $k => $v) {
				$this->$k = $v;
			}

			if ($this->access_level == 3) {
				$this->isAdmin = true;
			}
		}
	}



	function getUserPermissions() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		/*
			*
			* Not currently implemented - may use later...
			*
			$query = "SELECT A.action_code FROM access_action A, access_user_action U WHERE A.oid = U.action_oid AND U.user_oid = ".$this->user_oid.";";			
			$fsdb->query($query);
			while($row = $fsdb->getRow()) $this->permissions[] = $row['action_code'];
			*/
	}

	function sanitize()
	{


		// sanitize username
	        $this->uname = preg_replace("/[^a-zA-Z0-9]/", "", $this->uname);


       		// sanitize password
	        $this->pass = preg_replace("/[^A-Za-z0-9#?!@$%^&*-]/", "", $this->pass);  


	}




	/**
	 * New user applications are pending as account applications
	 * they are reviewed and approved by "admin" user, so strict validation is not performed here 
	 *
	 **/
	function addUser() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		// check for required feilds

		foreach($_POST as $k => $v) {
			if (preg_match("/^p_/",$k)) {
				$k = preg_replace("/p_/","",$k);
				$this->$k = $v;
			}
		}


		if (($this->name == "")
			|| ($this->uname == "")
			|| ($this->pass == "")
			|| ($this->email == "")
			|| (!is_numeric($this->company))
		) {
			$this->msg = "Error : One or more fields missing.";
			return false;
		}


		$encrypted_password = Login::generatePassHash($this->pass,$salt = '');


		// check uniqueness of username
		$this->db->query("SELECT id FROM euser WHERE uname = '".$this->uname."'");
		if ($this->db->getNumRows() >= 1) {
			$this->msg = "Error : Username is in use already, please choose a unique username.";
			return;
		}

		// perform the update
		$sql = "INSERT INTO euser (
		   id
		   ,name
		   ,email
		   ,uname
		   ,access_level
		   ,pass_hash
		   ,company_id
		   ,added
		) VALUES (
		   nextval('euser_seq')
		   ,'".$this->name."'
		   ,'".$this->email."'
		   ,'".$this->uname."'
		   ,1
		   ,'".$encrypted_password."'
		   ,'".$this->company."'
		   ,now()::timestamp
		);";

		$this->db->query($sql);
		if ($this->db->getAffectedRows() == 1) {
			$this->msg = "Added new user account for : ".$this->name.".";
			return true;
		} else {
			$this->msg = "There was a problem adding the user.";
			return;
		}
	}

	function getUserEditList($company_id) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		if (!is_numeric($company_id)) return false;

		global $oAuth;

		$this->db->query("SELECT * FROM euser WHERE company_id = $company_id ORDER by uname asc");

		$s = "<table id='report' class='table table-striped' cellpadding='2' cellspacing='4' border='0'>";
		$s .= "<tr><td colspan='3'>User Count : ".$this->db->getNumRows()."</td></tr>"; 
		$s .= "<tr><td>&nbsp;</td><td>Name</td><td>User</td><td>Email</td>";
		if ($oAuth->oUser->isAdmin) {
			$s .= "<td>Admin?</td>";
		}
		$s .= "<!--<td>Edit</td>--><td>Delete</td></tr>";
		$i = 1;
		if ($this->db->getNumRows() >= 1) {
			$arr = $this->db->getObjects();

			foreach($arr as $oUser) {
				$s .= "<tr>";
				$s .= "<td>".$i."</td>";
				$s .= "<td>".$oUser->name."</td>";
				$s .= "<td>".$oUser->uname."</td>";
				$s .= "<td>".$oUser->email."</td>";
				if ($oAuth->oUser->isAdmin) {
					$super = ($oUser->access_level == 3) ? "Admin" : "no";
					$s .= "<td>".$super."</td>";
				}
				$s .= "<td><a onclick=\"javascript: return confirm('Are you sure you want to delete this user?');\" href=\"".$_CONFIG['url']."/user.php?m=del&id=".$oUser->id."&p_company=".$company_id."\">delete</a></td>";
				$s .= "</tr>";
				$i++;
			}
		} else {
			$s .= "<tr><td colspan='4'>There are 0 users posted for this company.</td></tr>";
		}
		$s .= "</table>";
		return $s;
	}

	
	function deleteUser($id,$company_id) {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		$this->db->query("SELECT id FROM euser WHERE id = $id AND company_id = $company_id");
		if ($this->db->getNumRows() == 1) {
			return $this->db->query("DELETE FROM euser WHERE id = $id");
		}
	}


}
?>
