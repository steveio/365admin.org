<?php



class RegistrationController extends GenericController {
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 
		
		global $oSession, $oHeader, $oFooter, $oSession, $oAuth;

		$this->SetInValid();
		$this->SetInComplete();			
		$this->UnsetValidationErrors();

		// in case /registration was called directly, ie without visiting /new or /update
		if (($oSession->GetListingType() != LISTING_REQUEST_UPDATE) || (!is_numeric($oSession->GetCompanyId()))) {
			$oSession->SetListingType(LISTING_REQUEST_NEW);
			$oSession->Save();
		}
		
		
		$aNewListingOption = ListingOption::GetByCode($sCode = 'NEW',$currency = 'GBP');		
		$oAccount = new AccountApplication();
		
		if (isset($_POST['submit'])) {
			
			
			$_POST['listing_code'] = $aNewListingOption['code'];
			$_POST['account_type'] = $aNewListingOption['type'];
			
			$aValidationErrors = array();
			
			if ($oAccount->Validate($_POST,$aValidationErrors)) {
				
				
				unset($_POST['submit']);
				/* store the submitted form values in session,
				 * these will be written to DB once
				 * new company step is completed 
				 */
				$this->SetFormValues($_POST); 
				$this->SetValid();
				$this->SetComplete();

				$oSession->Save();

				/*
				 * If this registration is part of a new listing request,
				 * redirect to add company form
				 * 
				 */
				if ($oSession->GetListingType() == LISTING_REQUEST_NEW) {
					// redirect to add company form
					Http::Redirect("/".ROUTE_COMPANY."/add");
					die();
				}

				/*
				 * If this registration is part of a request to update an existing listing
				 * admin needs to approve the user account request
				 * store the user account details and redirect to confirmation page
				 * 
				 */
				$this->ProcessUpdateExistingRequest();
				
				
			} else {
				$this->SetValidationErrors($aValidationErrors);
			}
		} else {
			if ($this->Complete()) $_POST = $this->GetFormValues();
		}
		
		/* get a drop down list of countries */
		$oCountry = new Country($db);
		$sCountryListHTML = $oCountry->GetCountryDropDown($_POST['country_id'],'country_id');		
		
		$oRegistrationForm = new Template;

		$step_no = ($oSession->GetListingType() == "NEW") ? " - Step 1 of 3" : "";
		$oRegistrationForm->Set('STEP_NO',$step_no);
		
		$oRegistrationForm->Set('VALIDATION_ERRORS',$this->GetValidationErrors());
		$oRegistrationForm->Set('COUNTRY_LIST',$sCountryListHTML);
		$oRegistrationForm->Set('ACCOUNT_ID',$account_id);
		$oRegistrationForm->LoadTemplate("registration_form.php");
				
		print $oHeader->Render();
		print $oRegistrationForm->Render();
		print $oFooter->Render();
				
	}
	
	
	private function ProcessUpdateExistingRequest() {
		
		global $oSession;

		if ($this->AddAccount()) {
					
			$this->SetComplete();
			
			$message = "<h1>Update Listing Request Confirmation</h1>";
			$message .= "<p>Thanks for registering, one of our team will contact you shortly.</p>"; 
			$oMessage = new Message(MESSAGE_TYPE_SUCCESS, 'update_account_request', $message);
			
			$oConfirmationStep = $oSession->GetMVCController()->GetRouteByUriMapping("/".ROUTE_CONFIRMATION);
			$oConfirmationStep->UnsetUserMessages();
			$oConfirmationStep->SetUserMessage($oMessage);
			
			$oSession->Save();
			
			Http::Redirect("/".ROUTE_CONFIRMATION);
					
		} else {
			throw new Exception(ERROR_ADD_ACCOUNT_FAILED);
		}
		
	}

	/*
	 * Write account details (from registration step) to DB
	 * 
	 */
	protected function AddAccount() {
		
		global $oSession;
		
		$oAccount = new AccountApplication();

		$aValidationErrors = array();
		$aFormValues = $this->GetFormValues();
		$aFormValues['company_id'] = $oSession->GetCompanyID();
		
		
		if ($oAccount->Add($aFormValues,$aValidationErrors)) {
				
			$this->SetComplete();
			
			return TRUE;
		} else {
			
			Logger::DB(1,get_class($this)."::".__FUNCTION__."()","ADD_ACT FAIL: ".serialize($aValidationErrors)."\nFormValue::\n".serialize($aFormValues));
			
			$this->ProcessValidationErrors($aValidationErrors['msg'], $clear_existing = TRUE);
			return FALSE;
		}

	}
	
}


?>