<?




class Footer extends Layout {

   private $sBrand;
   private $sDescription;
   private $sCopyright;
   private $aLinkGroup;

   public function __Construct() {
      parent::__Construct();
   }

   public function SetBrand($sBrand) {
		$this->sBrand = $sBrand;
   }

   public function GetBrand() {
		return $this->sBrand;
   }

   public function SetDesc($sDesc) {
		$this->sDesc = $sDesc;
   }

   public function GetDesc() {
		return $this->sDesc;
   }

   public function SetCopyright($sCopyright) {
		$this->sCopyright = $sCopyright;
   }

   public function GetCopyright() {
		return $this->sCopyright;
   }

   public function GetLinkGroup($idx) {
      return $this->aLinkGroup[$idx];
   }

   public function SetLinkGroup($oLinkGroup) {
      $this->aLinkGroup[] = $oLinkGroup;
   }


   public function LoadTemplate($sFilename) {

		$this->oTemplate->SetFromArray(array(
										"BRAND" => $this->GetBrand(),
										"DESCRIPTION" => $this->GetDesc(),
										"COPYRIGHT" => $this->GetCopyright(),
										));
		$this->oTemplate->LoadTemplate($sFilename);
	}

}



?>