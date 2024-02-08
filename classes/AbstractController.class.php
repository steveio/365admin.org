<?php

/*
 * AbstractController.php
 * 
 * Contains core base controller functionality
 * 
 * Should only be instantiated via a derived class 
 * and must not contain any domain specific data
 * 
 */

abstract class AbstractController {
	
	protected $iId;
	protected $sName;
	protected $sClassName;
	protected $sUriMapping;
	protected $sLabel;
	
	protected $aFormValues; // submitted http get/post form vars	
	protected $aValidationErrors; // array of form validation exceptions
	protected $aMessage; // array of Message() class 
	
	protected $bComplete; // is processing complete?
	protected $bValid; // did any errors occur during processing?
	
		
	protected function __construct(){

		$this->bComplete = FALSE;
		$this->bValid = FALSE;
		
		$this->aFormValues = array();
		$this->aValidationErrors = array();
		$this->aMessage = array();

	}
	
	public function SetFromXml($oSimpleXmlElement) {
		
		$this->SetId((int) $oSimpleXmlElement->id);
		$this->SetName((string) $oSimpleXmlElement->name);
		$this->SetClassName((string) $oSimpleXmlElement->classname);
		$this->SetUriMapping((string) $oSimpleXmlElement->uri);
		$this->SetLabel((string) $oSimpleXmlElement->label);
		
	}

	public function SetId($route_id) {
		$this->iId = $route_id;
	}

	public function GetId() {
		return $this->iId;
	}

	public function SetName($sName) {
		$this->sName = $sName;
	}

	public function GetName() {
		return $this->sName;
	}

	public function SetClassName($sClassName) {
		$this->sClassName = $sClassName;
	}

	public function GetClassName() {
		return $this->sClassName;
	}
	
	public function SetUriMapping($uri_mapping) {
		$this->sUriMapping = $uri_mapping;
	}
	
	public function GetUriMapping() {
		return $this->sUriMapping;
	}

	public function SetLabel($sLabel) {
		$this->sLabel = $sLabel;
	}

	public function GetLabel() {
		return $this->sLabel;
	}	
	
	
	public function SetFormValues($aFormValues) {
		$this->aFormValues = $aFormValues;
	}
		
	public function GetFormValues() {
		return $this->aFormValues;	
	}
	
	public function SetFormValue($key, $value) {
		$this->aFormValues[$key] = $value;
	}
		
	public function GetFormValue($key) {
		return isset($this->aFormValues[$key]) ? $this->aFormValues[$key] : FALSE;
	}
	
	public function IssetFormValue($key) {
		return isset($this->aFormValues[$key]) ? TRUE :  FALSE;
	}	

	
	public function ProcessValidationErrors($errors, $clear_existing = FALSE) {
		
		if ($clear_existing) $this->UnsetMessage();
		
		if (is_array($errors)) {
			
			$oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_TYPE_VALIDATION_ERROR);
			$oMessage->SetMsgFromArray($errors);
			$this->SetMessage($oMessage);		
		} elseif (is_string($errors)) {
			$oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_TYPE_VALIDATION_ERROR, $errors);
			$this->SetMessage($oMessage);
		}
	}
	
	
	public function SetValidationErrors($aValidationError) {
		$this->aValidationErrors = $aValidationError;
	}
	
	public function GetValidationErrors() {
		return $this->aValidationErrors;
	} 
	
	public function UnsetValidationErrors() {
		$this->aValidationErrors = array();
	}

	public function SetMessage($oMessage) {
		$this->aMessage[] = $oMessage;
	}

	public function SetMessages($aMessage) {
		$this->aMessage = $aMessage;
	}

	/*
	 * Look in session for any User Message eg Add company success 
	 * 
	 */
	protected function GetMessageFromSession() {
		global $oSession;

		if (!is_object($oSession->GetMVCController())) return FALSE;
		
		$aMessages = $oSession->GetMVCController()->GetCurrentRoute()->GetMessage();		
		$oSession->GetMVCController()->GetCurrentRoute()->UnsetMessage();
		$oSession->Save();
		return $aMessages;
	}
	
	
	public function GetMessage() {
		return $this->aMessage;
	} 

	public function GetUserMessageById($id) {
		if (isset($this->aMessage[$id])) {
			return $this->aMessage[$id];
		}
	} 
	
	public function UnsetMessage() {
	    unset($this->aMessage);
		$this->aMessage = array();
	}
	
	
	public function SetComplete() {
		$this->bComplete = TRUE;
	}

	public function SetInComplete() {
		$this->bComplete = FALSE;
	}
	
	public function Complete() {
		return $this->bComplete;
	}
	
	public function SetValid() {
		$this->bValid = TRUE;
	}
	
	public function SetInvalid() {
		$this->bValid = FALSE;
	}
	
	public function Valid() {
		return $this->bValid;
	}
		
	/* these methods must be available on all routes although a controller may not provide an implementation */
	
	abstract protected function PreProcess();
	
	abstract protected function Process();
	
	abstract protected function PostProcess();

}

?>