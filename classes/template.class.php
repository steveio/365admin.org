<?php


/*
 * Simple Template System
 * 
 * A container data object for passing data to HTML templates
 * 
 * Also provides functionality to render common site elements (profiles,articles)  
 * using a set of pre-defined parameterised templates
 * 
 * Data structured as key => value pairs is injected into the template scope
 * via setter methdods Set() SetFromArray() SetFromObject()
 * 
 * Template is loaded by supplying a filename to LoadTemplate()
 * Parameterised template placeholders eg ::KEY:: are parsed and substituted 
 * with corresponding values from data scope
 * 
 * 
 * Useage -
 * 		$template->SetFromArray($aParams);
 * 		$template->LoadTemplate($sFilename = "article01.php");
 * 		$template->Render();
 * 
 * 
 */


class Template {

	const TEMPLATE_HOME = "/templates/";
	
	private $sTemplatePath; /* the HTML template to be used in rendering the article */
	private $sTemplateFile; /* template filename */
	private $sTemplate; /* parameterised template */
	private $sTemplateHTML; /* the finished HTML template */

	private $data; /* a collection of Key => Value data to be displayed */
	
	public function __Construct() {
		
		$this->data = new TemplateData();

		$this->SetTemplatePath(ROOT_PATH .  self::TEMPLATE_HOME); // default path to templates
	}

	public function Get($k) {
		return $this->data->$k;
	}
	

	public function  Set($k,$v) {
		$this->data->$k = $v;	
	}


	public function SetFromArray($a) {
		foreach($a as $k => $v) {
			$this->data->$k = $v;
		}		
	}

	/*
	 * Object must implement GetVisible() method
	 * 
	 */
	public function SetFromObject($o) {
		
		foreach($o->GetVisible() as $k => $v) {
			$this->data->$k = $v;
		}		
	}

	
	public function GetTemplatePath() {
		return $this->sTemplatePath;
	}
	
	public function SetTemplatePath($path) {
		$this->sTemplatePath = $path;
	}
	
	public function SetTemplateFile($sFilename) {
		$this->sTemplateFile = $sFilename;
	}
	
	public function GetTemplateFile() {
		return $this->sTemplateFile;
	}

	
	public function LoadTemplate($sFilename) {

		unset($this->sTemplate);
		unset($this->sTemplateHTML);

		$this->SetTemplateFile($sFilename);
		
		
		$this->sTemplateHTML = $this->LoadTemplateFromFile();
		
	}
	
	
	public function LoadTemplateFromFile() {

		global $_CONFIG, $oAuth, $oBrand; // make these objects available within all template scopes
		
		ob_start();
		include($this->GetTemplatePath().$this->GetTemplateFile());
		return ob_get_clean();
		
	}

	
	
	public function Render() {
		return $this->sTemplateHTML;
	}
	
	
}


/*
 * All components that require rendering via templates should implement this interface
 * 
 * LoadTemplate() and Render() act as wrappers to Template->LoadTemplate() and Template->Render()
 * 
 */

interface TemplateInterface {

	public function LoadTemplate($sFilename);
	
	public function Render();

}



/*
 * A data object used internally by Template
 * 
 */
class TemplateData {

}