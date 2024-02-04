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
	protected $aUserMessages; // array of messages to display eg add company successful 
	
	protected $bComplete; // is processing complete?
	protected $bValid; // did any errors occur during processing?
	
		
	protected function __construct(){

		$this->bComplete = FALSE;
		$this->bValid = FALSE;
		
		$this->aFormValues = array();
		$this->aValidationErrors = array();
		$this->aUserMessages = array();

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
		
		if ($clear_existing) $this->UnsetUserMessages();
		
		if (is_array($errors)) {
			
			$oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_TYPE_VALIDATION_ERROR);
			$oMessage->SetMsgFromArray($errors);
			$this->SetUserMessage($oMessage);		
		} elseif (is_string($errors)) {
			$oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_TYPE_VALIDATION_ERROR, $errors);
			$this->SetUserMessage($oMessage);
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

	public function SetUserMessage($oMessage) {
		$this->aUserMessages[] = $oMessage;
	}

	public function SetUserMessages($aMessage) {
		$this->aUserMessages = $aMessage;
	}

	/*
	 * Look in session for any User Message eg Add company success 
	 * 
	 */
	protected function GetUserMsg() {
		global $oSession;

		if (!is_object($oSession->GetMVCController())) return FALSE;
		
		$aMessages = $oSession->GetMVCController()->GetCurrentRoute()->GetUserMessages();		
		$oSession->GetMVCController()->GetCurrentRoute()->UnsetUserMessages();
		$oSession->Save();
		return $aMessages;
	}
	
	
	public function GetUserMessages() {
		return $this->aUserMessages;
	} 

	public function GetUserMessageById($id) {
		if (isset($this->aUserMessages[$id])) {
			return $this->aUserMessages[$id];
		}
	} 
	
	public function UnsetUserMessages() {
		$this->aUserMessages = array();
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