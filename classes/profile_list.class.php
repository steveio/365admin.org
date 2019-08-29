<?


/*
 * A container object to represent a list of profiles
 *
 */


class ProfileList implements TemplateInterface {

   private $sId;
   private $sTitle;
   private $iCols;
   private $aProfile;
   private $sProfileTemplate;
   private $oPager; /* an optional paginator instance */

   public function __Construct() {
      $this->aProfile = array();
	  $this->SetCols(4);
   }

	public function SetId($sId) {
		$this->sId = $sId;
	}

	public function GetId() {
		return $this->sId;
	}

	public function SetTitle($sTitle) {
		$this->sTitle = $sTitle;
	}

	public function GetTitle() {
		return $this->sTitle;
	}	

   public function GetCols() {
		$a = array(1=>"one",2=>"two",3=>"three",4=>"four","five");
		return $a[$this->iCols];
   }

   public function SetCols($iCols) {
      $this->iCols = $iCols;
   }


   public function SetProfile($oProfile) {
      $this->aProfile[] = $oProfile;
   }

   public function GetProfile() {
      return $this->aProfile;
   }

   public function SetPager($oPager) {
      $this->oPager = $oPager;
   }

   public function GetPager() {
      return is_object($this->oPager) ? $this->oPager->Render() : "";
   }

	public function LoadTemplate($sFilename) {
		$this->oTemplate = new Template();
		$this->oTemplate->SetFromArray(array(
										"ID" => $this->GetId(),
										"COLS" => $this->GetCols(),
										"TITLE" => $this->GetTitle(),
										"PROFILE_LIST" => $this->GetProfile(),
										"PAGER" => $this->GetPager(),
										));
		$this->oTemplate->LoadTemplate($sFilename);
	}

	public function Render() {
		return $this->oTemplate->Render();
	}


}






?>