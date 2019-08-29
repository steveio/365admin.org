<?php

class SearchPanel implements TemplateInterface {

	private $sName;
	private $sAction;	
	private $sMethod;
	private $sIconUrl;
	private $sActivitySelectHTML;
	private $sContinentSelectHTML;
	private $sCountrySelectHTML;


	public function __Construct() {
		
		global $_CONFIG;
	
		$this->SetName("project_search");
		$this->SetAction($_CONFIG['url']."/search_panel_dispatch.php");
		$this->SetMethod("POST");
		$this->SetIconUrl($_CONFIG['url']."/images/search.gif");
	
	}

	public function GetName() {
		return $this->sName;
	}
	
	public function SetName($sName) {
		$this->sName = $sName;
	}

	public function GetAction() {
		return $this->sAction;
	}
	
	public function SetAction($sAction) {
		$this->sAction = $sAction;
	}

	public function GetMethod() {
		return $this->sMethod;
	}
	
	public function SetMethod($sMethod) {
		$this->sMethod = $sMethod;
	}

	public function GetIconUrl() {
		return $this->sIconUrl;
	}
	
	public function SetIconUrl($sIconUrl) {
		$this->sIconUrl = $sIconUrl;
	}

	public function GetActivitySelectHTML() {
		return $this->sActivitySelectHTML;
	}
	
	public function SetActivitySelectHTML($sActivitySelectHTML) {	
		$this->sActivitySelectHTML = $sActivitySelectHTML;
	}

	public function GetCountrySelectHTML() {
		return $this->sCountrySelectHTML;
	}
	
	public function SetCountrySelectHTML($sCountrySelectHTML) {
		$this->sCountrySelectHTML = $sCountrySelectHTML;
	}


	public function GetContinentSelectHTML() {
		return $this->sContinentSelectHTML;
	}
	
	public function SetContinentSelectHTML($sContinentSelectHTML) {
		$this->sContinentSelectHTML = $sContinentSelectHTML;
	}
	
   public function LoadTemplate($sFilename) {
		$this->oTemplate = new Template();
		$this->oTemplate->SetFromArray(array(
										"NAME" => $this->GetName(),
										"ACTION" => $this->GetAction(),
										"METHOD" => $this->GetMethod(),
										"ICON_IMG_URL" => $this->GetIconUrl(),
										"ACTIVITY_SELECT_LIST" => $this->GetActivitySelectHTML(),
										"COUNTRY_SELECT_LIST" => $this->GetCountrySelectHTML(),
										"CONTINENT_SELECT_LIST" => $this->GetContinentSelectHTML(),
		
										));
		$this->oTemplate->LoadTemplate($sFilename);
	}

	public function Render() {
		return $this->oTemplate->Render();
	}
	
	
}


?>