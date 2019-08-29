<?php

/*
 * StepController.php
 * 
 * A simple front controller implementation
 * 
 * A collection of step class definitions corresponding to known 
 * application request routes are loaded from steps.xml file.
 * Each step must have a corresponding class implementation defined in /steps
 * 
 * The first uri segment of a request after the host specifier
 * is used to map a request onto a defined step eg 
 * http://www.domain.com/login  would map to LoginStep where LoginStep->uri = '/login'    
 *
 * On successfuly mapping a request methods are called to fullfill the request - 
 * StepClass->PreProcess()
 * StepClass->Process() 
 * StepClass->PostProcess()
 * Generally a step class will only provide an implementation for ->Process()
 *   
 */

class StepController{
	
	protected $sBasePath; // path to project http root
	protected $sRequestUri;  // string request uri eg /step1, maps to $oStep->uri-mapping if matched 
	protected $nCurrentStepId;  // int id of step to process, a pointer into $aSteps
	protected $aSteps; // array of step objects

	
	public function __construct($sBasePath){

		$this->sBasePath = $sBasePath;
		$this->aSteps = array();
		$this->aStepsProcessed = array();
		
	}
	
	public function Process() {
		
		try {

			$this->GetStepById($this->GetCurrentStepId())->Process();
			
		} catch (InvalidSessionException $e) {
			throw new InvalidSessionException($e->getMessage());
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
	}
	
	public function MapRequest() {
		
		try {
			$this->SetCurrentStepId( $this->GetStepByUriMapping($this->GetRequestUri())->GetId() );
			
		} catch (Exception $e) {
			throw new NotFoundException($e->getMessage());
		}
		
	}
	
	
	public function SetRequestUri($sRequestUri) {
		$this->sRequestUri = $sRequestUri;
	}
	
	public function GetRequestUri() {
		return $this->sRequestUri;
	}
	
	public function SetCurrentStepId($id) {
		$this->nCurrentStepId = $id;
	}
	
	public function GetCurrentStepId() {
		return $this->nCurrentStepId;
	}	
	
	public function GetCurrentStep() {
		return $this->GetStepById($this->GetCurrentStepId());
	}

	public function SetStepsFromXmlFile($xml_file_path, $brand_id) {
		
		if (!file_exists($xml_file_path)) {
			throw new Exception(ERROR_INVALID_XML_FILE_PATH . $xml_file_path);
		}
		
		$oXml = simplexml_load_file($xml_file_path);
		
		if (!is_object($oXml) || count($oXml->step) < 1) throw new Exception(ERROR_INVALID_XML_STEP_DEFS);
		
		foreach($oXml->step as $oXmlElement) {
			
			try {

					$class_ext_found = FALSE;
					
					if (isset($oXmlElement->brandextension)) { // look for a brand specific step class 
						
						foreach($oXmlElement->brandextension->brand as $oBrandXmlNode) {
							if ($brand_id == (int)$oBrandXmlNode->attributes()->id[0]) {
								$classname = (string) $oBrandXmlNode->classname;
								$class_ext_found = TRUE;
							}
						}
						
					}
						
					if (!$class_ext_found) { // use generic step class
						$classname = (string) $oXmlElement->classname;
					}

					$oStep = new $classname();
					$oStep->SetFromXml($oXmlElement);
														
					$this->aSteps[$oStep->GetId()] = $oStep;
					
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			
			} // end foreach step
		
	}
	
	public function SetSteps($aSteps) {
		if (is_array($aSteps)) $this->aSteps = $aSteps;
	}
	
	public function GetSteps() {
		return $this->aSteps;
	}
	
	public function GetStepById($step_id) {
		foreach($this->GetSteps() as $oStep) {
			if ($oStep->GetId() == $step_id) return $oStep;
		}
		
		throw new NotFoundException(ERROR_404_STEP_NOT_FOUND." id: ".$step_id);
	}
	
	public function GetStepByName($step_name) {
		foreach($this->GetSteps() as $oStep) {
			if ($oStep->GetName() == $step_name) return $oStep;
		}
		
		throw new NotFoundException(ERROR_404_STEP_NOT_FOUND." name: ".$step_name);
	}

	public function GetStepByUriMapping($uri) {
		foreach($this->GetSteps() as $oStep) {
			if ($oStep->GetUriMapping() == $uri) return $oStep;
		}
		
		throw new NotFoundException(ERROR_404_STEP_NOT_FOUND." request_uri: ".$uri);
	}
	
		
}

?>