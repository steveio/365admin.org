<?php




class PlacementController extends ProfileController {
	
	const MODE_VIEW  = 0;
	const MODE_ADD  = 1;
	const MODE_EDIT  = 2;
	const MODE_DELETE  = 3;
	
	private $aRequestArray = array(); // Request URI 

	private $company_url_name; // string validated unique company url token from request eg /company/<company-url-name>
	private $company_id; // int id of profile to be editted
	private $oCompanyProfile; 
	
	private $placement_url_name;
	private $placement_id;
	private $oPlacementProfile;
	
	private $mode; // set according to request type 
	
	private $oPlacementForm; // object add/edit placement form
	
	private $oTabbedPanel;
	
	
	public function __construct( ){
		
		parent::__construct();
		
	}
	
	public function Process() { 

		$this->SetInValid();

		$this->SetMode(); // determine request type ( VIEW || ADD || EDIT )

		switch($this->GetMode()) {
			case self::MODE_ADD :
				$this->CheckPermissions();
				$this->AddProfile();
				$this->SetFormElements();
				break;
			case self::MODE_EDIT :
			    $this->CheckDomain();
				$this->GetPlacementDetailsFromUrl();
				$this->CheckPermissions();
				if (isset($_POST['save_video_btn'])) {
					$this->SaveYouTubeVideo(PROFILE_PLACEMENT, $this->GetPlacementId(), $_POST['video']);
				}
				$this->DoImageUpload("PLACEMENT", $this->GetPlacementId());
				$this->EditProfile();
				$this->SetFormElements();
				break;
			case self::MODE_DELETE :
				$this->GetPlacementDetailsFromUrl();
				$this->CheckPermissions();
				$this->DeleteProfile();
				break;
			case self::MODE_VIEW :
			    $this->ViewProfile();
			    break;
			default:
				throw new Exception(ERROR_INVALID_MODE);
				
		}
		
	}

	private function GetPlacementDetailsFromUrl() {
		
		global $db;
		
		$oProfile = new PlacementProfile();
		$aResult = $oProfile->GetDetailsByUri($this->GetPlacementUrlName());

		if (!$aResult) throw new Exception(ERROR_PLACEMENT_PROFILE_NOT_FOUND.$this->GetPlacementUrlName());
		
		$this->SetPlacementId($aResult['id']);
		$this->SetCompanyId($aResult['company_id']);
		
	}

	/*
	 * Set mode -
	 * 
	 * ADD = /placement
	 * VIEW = /company/<comp-url-name>/<placement-url-name>
	 * EDIT = /company/<comp-url-name>/<placement-url-name>/edit 
	 * 
	 */
	private function SetMode() {

		$this->aRequestArray = Request::GetUri("ARRAY");

		$this->SetPlacementUrlName($this->aRequestArray);

		switch(TRUE) {
			case $this->RequestAdd() :
				return $this->mode = self::MODE_ADD;	
			case $this->RequestEdit() :
				return $this->mode = self::MODE_EDIT;	
			case $this->RequestDelete() :
				return $this->mode = self::MODE_DELETE;	
			case $this->RequestView() :
				return $this->mode = self::MODE_VIEW;
				default :
				    throw new Exception(ERROR_404_INVALID_REQUEST.implode("/",$this->aRequestArray));
		}
	}	

	
	private function RequestEdit() {
	    // /placement/<placement-name/>/edit
	    if (($this->aRequestArray[1] == ROUTE_PLACEMENT) &&
	        (strlen($this->aRequestArray[2]) > 1) && 
	        ($this->aRequestArray[3] == ROUTE_EDIT)) 
		{
			return TRUE;			
		}

		// /company/<company-name>/<placement-name/>/edit
		if (($this->aRequestArray[1] == ROUTE_COMPANY) &&
		    (strlen($this->aRequestArray[2]) > 1) &&
		    (strlen($this->aRequestArray[3]) > 1) &&
		    ($this->aRequestArray[4] == ROUTE_EDIT))
		{
		    return TRUE;
		}

	}
	
	private function RequestDelete() {
	    if (($this->aRequestArray[1] == ROUTE_PLACEMENT) &&
	        (strlen($this->aRequestArray[2]) > 1) && 
	        ($this->aRequestArray[3] == ROUTE_DELETE)) 
		{
			return TRUE;			
		}
	}
	
	private function RequestAdd() {
		
	    if (strtoupper($this->aRequestArray[2]) == "ADD" || !isset($this->aRequestArray[2])) {
			return TRUE;
		}
		
	}
	
	private function RequestView() {
	    if (($this->aRequestArray[1] == ROUTE_COMPANY) &&
	        (strlen($this->aRequestArray[2]) > 1) && 
	        (strlen($this->aRequestArray[3]) > 1))
		{
			return TRUE;			
		}
	}
	
	private function GetMode() {
		return $this->mode;
	}
	
	/*
	 * Check user access permissions -
	 * Users must be authenticated to add placements,
	 * admin user(s) can edit any placement
	 * 
	 */
	private function CheckPermissions() {

		global $oAuth,$oSession;

		
		// these should always be set correctly
		if (($oSession->GetListingType() == NULL) || ($oSession->GetMVCController() == NULL)) {
			throw new InvalidSessionException(ERROR_INVALID_SESSION);			
		}
		
		// user must have a valid session
		if (!$oAuth->ValidSession()) {
			$request_array = Request::GetUri("ARRAY");
			Http::Redirect("/".ROUTE_LOGIN.implode("/",$request_array));
		}
		
		switch($this->GetMode()) {
			
			case self::MODE_ADD :
				break;
			
			/* users can only edit/delete placements associated to their organisation */
			case self::MODE_EDIT :
			case self::MODE_VIEW :
			case self::MODE_DELETE :
				
				if ($oAuth->oUser->isAdmin) return TRUE; // admin can edit/delete any profile
				
				// user can only add placements for their organisation
				if ($oAuth->oUser->GetCompanyId() != $this->GetCompanyId()) { // company user can only edit profile owned by their company
					throw new Exception(ERROR_COMPANY_PROFILE_PERMISSIONS_FAIL." user_id: ".$oAuth->oUser->GetId().", requested_comp_id: ".$this->GetCompanyId());
				}					
				break;				
		}
		
		return TRUE;
		
		
	}

	public function ViewProfile()
	{
	    try {

	        $oContentAssembler = new PlacementProfileContentAssembler();
	        $oContentAssembler->GetByPath($this->GetPlacementUrlName());

	        die();

	    } catch (Exception $e) {
	        throw $e;
	    }
	    
	}

	private function DeleteProfile() { 
		
		global $oSession, $db;
		
		$this->GetProfileFromDb();
		$oDashboardController = $oSession->GetMVCController()->GetRouteByUriMapping("/".ROUTE_DASHBOARD);
		
		$oArchiveManager = new ArchiveManager;
		$result = $oArchiveManager->ArchivePlacement($this->GetProfile()->GetId());
				
		if ($result) {
			$message .= "<p>SUCCESS: deleted placement ".$this->GetProfile()->GetTitle()."</p>"; 
			$oMessage = new Message(MESSAGE_TYPE_SUCCESS, MESSAGE_ID_DELETE_PLACEMENT, $message);
		} else {
			$message .= "<p>ERROR: it was not possible to delete placement ".$this->GetProfile()->GetTitle()."</p>"; 
			$oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_ID_DELETE_PLACEMENT, $message);			
		}
		
		$oDashboardController->UnsetMessage();
		$oDashboardController->SetMessage($oMessage);					
		$oSession->Save();		

		Http::Redirect("/".ROUTE_DASHBOARD);
	}
	 
	private function AddProfile() {
		
		global $oSession;
		
		$this->SetInValid();
		$this->SetInComplete();
		$this->UnsetValidationErrors();
		
		$this->GetNewPlacementProfile();
		
		$response = array(); // container for validation/save errors
		
		if (isset($_POST['submit'])) {
			
			/* add / update company db record */
			if ($this->AddUpdateDB($_POST, $response)) {

				Logger::DB(2,get_class($this)."::".__FUNCTION__."()","OK: ".$response['id']);

				// setup user notification success msg				
				$msg = "SUCCESS - Added Profile <br /><a href='/".ROUTE_DASHBOARD."' title='Return to Dashboard'>Click here</a> to return to dashboard";
				$oMessage = new Message(MESSAGE_TYPE_SUCCESS, MESSAGE_ID_ADD_PLACEMENT, $msg);
				$oSession->GetMVCController()->GetCurrentRoute()->SetMessage($oMessage);
				
				// clear all attributes on this step instance, as we want to rebuild the form
				$this->Clear();
				
				$oSession->Save();
				
				Http::Redirect("/".ROUTE_PLACEMENT."/".$response['url_name']."/edit");
				die();
				
			} else {

				Logger::DB(1,get_class($this)."::".__FUNCTION__."()","FAIL: ".serialize($response));

                                $this->ProcessValidationErrors($response['msg']);
                                $this->SetValidationErrors($response);

                                return FALSE;

				//$oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_ID_ADD_PLACEMENT, $msg = "ERROR: Sorry, an error has occured and it was not possible to add the profile");
				//$this->SetUserMessage($oMessage);
												
				return FALSE;
			}	
		}
	}

	
	/*
	 * Main method for an edit profile request -
	 * retrieves profile and handles update if form submitted 
	 * 
	 */
	private function EditProfile() {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
	
        $this->GetProfileFromDb();

        $response = array();

		// handle update if form submitted
		if (isset($_POST['submit'])) {

			$_POST['id'] = $this->GetPlacementId();
			$_POST['url_name'] = $this->GetPlacementUrlName();
			
			/* add / update company db record */
			if ($this->AddUpdateDB($_POST, $response)) {

				Logger::DB(2,get_class($this)."::".__FUNCTION__."()","OK: ".$this->GetPlacementId());

				// setup user notification success msg				
				$msg = "<button class=\"btn btn-success rounded-pill px-3\" type=\"button\" onclick=\"javascript: window.open('".$this->GetProfile()->GetProfileUrl()."');\" name=\"new\">SUCCESS : Updated profile</button>";

				$oMessage = new Message(MESSAGE_TYPE_SUCCESS, MESSAGE_ID_EDIT_PROFILE, $msg);
				$this->SetMessage($oMessage);
				
				if ($response['url_change'] == TRUE) {

					Http::Redirect($response['edit_url']);
					
				}
								

			} else {
				Logger::DB(1,get_class($this)."::".__FUNCTION__."()","FAIL: ".serialize($response));		
				
				$this->ProcessValidationErrors($response['msg']);
				$this->SetValidationErrors($response);

				return FALSE;
			}	

		}

		// get placement profile
		$this->GetProfileFromDb();		
		$this->SetFormValuesFromProfile();  

		// put ID in session for image_upload.php
		$_SESSION['id'] = $this->GetPlacementId();
		$_SESSION['link_to'] = "PLACEMENT";

	}
	
	
	/*
	 * INSERT/UPDATE placement DB Record
	 * 
	 * @param array form values
	 * @return bool mixed, TRUE on success, FALSE on failure
	 * 
	 */
	private function AddUpdateDB($aRawFormValues,&$aResponse) {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		$aFormValues = array();
		
		/* basic sanitization */
		foreach($aRawFormValues as $k => $v) {
			if (is_string($v)) {
				//$v = htmlspecialchars($v,ENT_NOQUOTES,"UTF-8");
			}
			$aFormValues[$k] = $v;
		}
		
		if ($this->GetProfile()->DoAddUpdate($aFormValues,$aResponse)) {			
			return TRUE;
		} else {
			return FALSE;
		}
		
		
	}
	
	
	// attempt to get placement corresponding to $this->GetPlacementId()
	private function GetProfileFromDb() {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		try {
			$profile_type = PlacementProfile::GetTypeById( $this->GetPlacementId() );
			
			if (!is_numeric($profile_type)) throw new Exception(ERROR_INVALID_PROFILE_TYPE.__FUNCTION__.$this->GetPlacementId());

			$oProfile = ProfileFactory::Get($profile_type);
			
			$oProfile->GetById($this->GetPlacementId());
			
			$this->SetPlacementProfile($oProfile);
			
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
	} 
	
	// set _POST form values from $this->oPlacementProfile
	private function SetFormValuesFromProfile() {
		
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		// Common placement fields
		$_POST[PROFILE_FIELD_PLACEMENT_TITLE] = $this->GetProfile()->GetTitle();
		$_POST[PROFILE_FIELD_PLACEMENT_COMP_ID] = $this->GetProfile()->GetCompanyId();
		$_POST[PROFILE_FIELD_PLACEMENT_DESC_SHORT] = $this->GetProfile()->GetDescShort();
		//$_POST[PROFILE_FIELD_PLACEMENT_PROFILE_TYPE_ID] = $this->GetProfile()->;
		$_POST[PROFILE_FIELD_PLACEMENT_LOCATION] = $this->GetProfile()->GetLocation();
		$_POST[PROFILE_FIELD_PLACEMENT_DESC_LONG] = $this->GetProfile()->GetDescLong();
		$_POST[PROFILE_FIELD_PLACEMENT_URL] = $this->GetProfile()->GetUrl();
		$_POST[PROFILE_FIELD_PLACEMENT_EMAIL] = $this->GetProfile()->GetEmail();
		$_POST[PROFILE_FIELD_PLACEMENT_APPLY_URL] = $this->GetProfile()->GetApplyUrl();
		$_POST[PROFILE_FIELD_PLACEMENT_KEYWORD_EXCLUDE] = $this->GetProfile()->GetKeywordExclude();
		//$_POST[PROFILE_FIELD_PLACEMENT_ACTIVE] = $this->GetProfile()->;
				
		// general placement fields
		if ($this->GetProfile() instanceof GeneralProfile) {

			$_POST[PROFILE_FIELD_PLACEMENT_DURATION_FROM] = $this->GetProfile()->GetDurationFromId();
			$_POST[PROFILE_FIELD_PLACEMENT_DURATION_TO] = $this->GetProfile()->GetDurationToId();
			$_POST[PROFILE_FIELD_PLACEMENT_PRICE_FROM] = $this->GetProfile()->GetPriceFromId();
			$_POST[PROFILE_FIELD_PLACEMENT_PRICE_TO] = $this->GetProfile()->GetPriceToId();
			$_POST[PROFILE_FIELD_PLACEMENT_CURRENCY] = $this->GetProfile()->GetCurrencyId();
			$_POST[PROFILE_FIELD_PLACEMENT_START_DATES_TXT] = $this->GetProfile()->GetStartDates();
			$_POST[PROFILE_FIELD_PLACEMENT_BENEFITS] = $this->GetProfile()->GetBenefits();
			$_POST[PROFILE_FIELD_PLACEMENT_REQUIREMENTS] = $this->GetProfile()->GetRequirements();

		}
		
		if ($this->GetProfile() instanceof TourProfile) {
			
			$_POST[PROFILE_FIELD_PLACEMENT_TOUR_DURATION_FROM] = $this->GetProfile()->GetDurationFromId();
			$_POST[PROFILE_FIELD_PLACEMENT_TOUR_DURATION_TO] = $this->GetProfile()->GetDurationToId();
			$_POST[PROFILE_FIELD_PLACEMENT_TOUR_PRICE_FROM] = $this->GetProfile()->GetPriceFromId();
			$_POST[PROFILE_FIELD_PLACEMENT_TOUR_PRICE_TO] = $this->GetProfile()->GetPriceToId();
			$_POST[PROFILE_FIELD_PLACEMENT_TOUR_CURRENCY] = $this->GetProfile()->GetCurrencyId();
			$_POST[PROFILE_FIELD_PLACEMENT_GROUP_SIZE] = $this->GetProfile()->GetGroupSizeId();
			$_POST[PROFILE_FIELD_PLACEMENT_TOUR_CODE] = $this->GetProfile()->GetCode();
			$_POST[PROFILE_FIELD_PLACEMENT_START_DATES] = $this->GetProfile()->GetDates();
			$_POST[PROFILE_FIELD_PLACEMENT_ITINERY] = $this->GetProfile()->GetItinery();
			$_POST[PROFILE_FIELD_PLACEMENT_TOUR_PRICE] = $this->GetProfile()->GetPrice();
			$_POST[PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS] = $this->GetProfile()->GetRequirements();
			
		}
		
		if  ($this->GetProfile() instanceof JobProfile) {
			
			$_POST[PROFILE_FIELD_PLACEMENT_JOB_REFERENCE] = $this->GetProfile()->GetReference();
			$_POST[PROFILE_FIELD_PLACEMENT_JOB_DURATION_FROM] = $this->GetProfile()->GetDurationFromId();
			$_POST[PROFILE_FIELD_PLACEMENT_JOB_DURATION_TO] = $this->GetProfile()->GetDurationToId();
			$_POST[PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE] = $this->GetProfile()->GetStartDateMultiple();
			$_POST[PROFILE_FIELD_PLACEMENT_JOB_CONTRACT_TYPE] = $this->GetProfile()->GetContractType();
			$_POST[PROFILE_FIELD_PLACEMENT_JOB_SALARY] = $this->GetProfile()->GetSalary();
			$_POST[PROFILE_FIELD_PLACEMENT_JOB_BENEFITS] = $this->GetProfile()->GetBenefits();
			$_POST[PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE] = $this->GetProfile()->GetExperience();
			
			/*
			 * @todo - preset dates
				define('PROFILE_FIELD_PLACEMENT_JOB_START_DT','job_start_date'); // actually these 2date fields are divided into month / year
				define('PROFILE_FIELD_PLACEMENT_JOB_CLOSING_DATE','close_date');

			 */			
		}
		
	}
	
	/*
	 * Setup fields/values required by add/edit placement form  
	 * 
	 */
	private function SetFormElements() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $oAuth, $oHeader, $oFooter, $oBrand, $oSession, $db;


$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/tinymce/js/tinymce/tinymce.min.js");
$oJsInclude->SetReferrerPolicy("origin");
$oHeader->SetJsInclude($oJsInclude);


$ckeditor_js_admin = <<<EOT

tinymce.init({
        selector: '#desc_short',
        menubar : false,
        images_upload_url: '/image_upload.php',
        height:"291"
});


tinymce.init({
        selector: '#desc_long',
        menubar: false,
        toolbar: "undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | image link | table | numlist bullist | code",
        plugins: "image link lists table code",
        images_upload_url : '/image_upload.php'
});

EOT;

$ckeditor_js_comp = <<<EOT

tinymce.init({
        selector: '#desc_short',
        menubar : false,
        height:"291"
});


tinymce.init({
        selector: '#desc_long',
        menubar: false,
        toolbar: "undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | image | table | numlist bullist",
        plugins: "image link lists table code",
        images_upload_url : '/image_upload.php'
});

EOT;

        if ($oAuth->oUser->isAdmin)
        {
            $ckeditor_js = $ckeditor_js_admin;
        } else {
            $ckeditor_js = $ckeditor_js_comp;
        }


		$oHeader->SetJsOnload($ckeditor_js);
		$oHeader->Reload();
				
		// instantiate templates
		$this->SetPlacementForm(new Template);		

		// company name select list
		$oCompany = new Company($db);
		if ($oAuth->oUser->isAdmin) {
			$this->GetForm()->Set("COMPANY_NAME_LIST",$oCompany->getCompanyNameDropDown($this->GetProfile()->GetCompanyId(),null,PROFILE_FIELD_PLACEMENT_COMP_ID));
		} else {
			$this->GetForm()->Set("COMPANY_NAME_LIST",$oCompany->getCompanyNameDropDown($oAuth->oUser->company_id,$oAuth->oUser->company_id,PROFILE_FIELD_PLACEMENT_COMP_ID,FALSE));
		}
		
		/* category, activity, country lists */
		$this->GetForm()->Set('ACTIVITY_LIST',$this->GetActivityList());
		$this->GetForm()->Set('CATEGORY_LIST',$this->GetCategoryList());
		$this->GetForm()->Set('COUNTRY_LIST',$this->GetCountryList());
				
		/* set profile type and enquiry options */
		$this->SetProfileOptions();
		$this->SetDefaultProfileType();

		/* set elements for specific profile types eg general, tour, job */
		$this->SetGeneralProfileElements();
		$this->SetTourProfileFormElements();
		$this->SetJobProfileFormElements();
		
		
		$prefix = ($this->GetMode() == self::MODE_ADD) ? "Add " : "Edit ";
		$title = $prefix . $oBrand->GetPlacementTitle();
		$this->GetForm()->Set('STEP_TITLE',$title);
		$this->GetForm()->Set('PLACEMENT_TITLE',$title);
		$this->GetForm()->Set('VALID',$this->Valid());
		$this->GetForm()->Set('VALIDATION_ERRORS',$this->GetValidationErrors());		
		$this->GetForm()->Set('COMPANY_PROFILE',$this->GetProfile());
		
		
		$this->GetForm()->LoadTemplate("profile_placement.php");
				
		$this->SetTabbedPanel();
		
		$oMessageProcessor = new MessageProcessor();
		$oMessagePanel = $oMessageProcessor->GetMessagePanel();
		
		print $oHeader->Render();
		print $oMessagePanel->Render();
		print $this->GetTabbedPanel()->Render();
		print $oFooter->Render();
		
	}
	
	
	
	private function SetGeneralProfileElements() {

		//($this->Get('oCProfile')->GetProfileTypeCount() > 1) || ($oAuth->oUser->isAdmin))
		
		// set placement active status
		if (isset($_POST['submit'])) {
			$checked = ($_POST[PROFILE_FIELD_PLACEMENT_ACTIVE] == "true") ? "checked" : "";
			$this->GetForm()->Set('PLACEMENT_ACTIVE_CHECKED',$checked);
		} elseif ($this->GetMode() == self::MODE_ADD) { // new placements always active by default
			$this->GetForm()->Set('PLACEMENT_ACTIVE_CHECKED',"checked");	
		} elseif ($this->GetMode() == self::MODE_EDIT) { 			
			$checked = ($this->GetProfile()->GetActive() == "t") ? "checked" : "";
			$this->GetForm()->Set('PLACEMENT_ACTIVE_CHECKED',$checked);
		}
		
		// duration_from / duration_to
		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_PLACEMENT_DURATION_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_DURATION_FROM];
		} elseif ($this->GetProfile() instanceof GeneralProfile) {
			$selected = $this->GetProfile()->GetDurationFromId();
		}
		$this->GetForm()->Set('DURATION_FROM',$oDuration->GetDDlist($selected));

		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_PLACEMENT_DURATION_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_DURATION_TO];
		} elseif ($this->GetProfile() instanceof GeneralProfile) {
			$selected = $this->GetProfile()->GetDurationToId();
		}
		$this->GetForm()->Set('DURATION_TO',$oDuration->GetDDlist($selected));
		
		
		// price_from, price_to
		$oPriceFrom = new Refdata(REFDATA_APPROX_COST);
		$oPriceFrom->SetOrderBySql(' sort_order ASC');
		$oPriceFrom->SetElementName(PROFILE_FIELD_PLACEMENT_PRICE_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_PRICE_FROM];
		} elseif ($this->GetProfile() instanceof GeneralProfile) {
			$selected = $this->GetProfile()->GetPriceFromId();
		}
		$this->GetForm()->Set('PRICE_FROM',$oPriceFrom->GetDDlist($selected));

		$oPriceTo = new Refdata(REFDATA_APPROX_COST);
		$oPriceTo->SetOrderBySql(' sort_order ASC');
		$oPriceTo->SetElementName(PROFILE_FIELD_PLACEMENT_PRICE_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_PRICE_TO];
		} elseif ($this->GetProfile() instanceof GeneralProfile) {
			$selected = $this->GetProfile()->GetPriceToId();
		}
		$this->GetForm()->Set('PRICE_TO',$oPriceTo->GetDDlist($selected));
		
		// currency
		$oCurrency = new Refdata(REFDATA_CURRENCY);
		$oCurrency->SetOrderBySql(' id ASC');
		$oCurrency->SetElementName(PROFILE_FIELD_PLACEMENT_CURRENCY);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_CURRENCY];
		} elseif ($this->GetProfile() instanceof GeneralProfile) {
			$selected = $this->GetProfile()->GetCurrencyId();
		}
		$this->GetForm()->Set('CURRENCY',$oCurrency->GetDDlist($selected, $no_default = TRUE));
		
	}

	
	
	private function SetTourProfileFormElements() {
		
		// duration_from / duration_to
		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_PLACEMENT_TOUR_DURATION_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_TOUR_DURATION_FROM];
		} elseif ($this->GetProfile() instanceof TourProfile) {
			$selected = $this->GetProfile()->GetDurationFromId();
		}
		$this->GetForm()->Set('TOUR_DURATION_FROM',$oDuration->GetDDlist($selected));

		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_PLACEMENT_TOUR_DURATION_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_TOUR_DURATION_TO];
		} elseif ($this->GetProfile() instanceof TourProfile) {
			$selected = $this->GetProfile()->GetDurationToId();
		}
		$this->GetForm()->Set('TOUR_DURATION_TO',$oDuration->GetDDlist($selected));
		
		
		// price_from, price_to
		$oPriceFrom = new Refdata(REFDATA_APPROX_COST);
		$oPriceFrom->SetOrderBySql(' sort_order ASC');
		$oPriceFrom->SetElementName(PROFILE_FIELD_PLACEMENT_TOUR_PRICE_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_TOUR_PRICE_FROM];
		} elseif ($this->GetProfile() instanceof TourProfile) {
			$selected = $this->GetProfile()->GetPriceFromId();
		}
		$this->GetForm()->Set('TOUR_PRICE_FROM',$oPriceFrom->GetDDlist($selected));

		$oPriceTo = new Refdata(REFDATA_APPROX_COST);
		$oPriceTo->SetOrderBySql(' sort_order ASC');
		$oPriceTo->SetElementName(PROFILE_FIELD_PLACEMENT_TOUR_PRICE_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_TOUR_PRICE_TO];
		} elseif ($this->GetProfile() instanceof TourProfile) {
			$selected = $this->GetProfile()->GetPriceToId();
		}
		$this->GetForm()->Set('TOUR_PRICE_TO',$oPriceTo->GetDDlist($selected));
		
		
		// currency
		$oCurrency = new Refdata(REFDATA_CURRENCY);
		$oCurrency->SetOrderBySql(' id ASC');
		$oCurrency->SetElementName(PROFILE_FIELD_PLACEMENT_TOUR_CURRENCY);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_TOUR_CURRENCY];
		} elseif ($this->GetProfile() instanceof TourProfile) {
			$selected = $this->GetProfile()->GetCurrencyId();
		}
		$this->GetForm()->Set('TOUR_CURRENCY',$oCurrency->GetDDlist($selected, $no_default = TRUE));
		
		// group size 
		$oGrpSize = new Refdata(REFDATA_INT_SMALL_RANGE);
		$oGrpSize->SetOrderBySql(' id ASC');
		$oGrpSize->SetElementName(PROFILE_FIELD_PLACEMENT_GROUP_SIZE);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_GROUP_SIZE];
		} elseif ($this->GetProfile() instanceof TourProfile) {
			$selected = $this->GetProfile()->GetGroupSizeId();
		}
		$this->GetForm()->Set('GROUP_SIZE',$oGrpSize->GetDDlist($selected, $no_default = FALSE));
		
		
		// travel / transport
		$oTravel = new Refdata(REFDATA_TRAVEL_TRANSPORT);
		$aSelected = array();
		if (isset($_POST['submit'])) {
			$aSelected = Mapping::GetIdByKey($_REQUEST,REFDATA_TRAVEL_TRANSPORT_PREFIX);
		} elseif ($this->GetProfile() instanceof TourProfile) {
			$aSelected = $this->GetProfile()->GetTransportIdList();
		}
		$this->GetForm()->Set('TRAVEL_TOUR_LIST_SELECTED_COUNT',count($aSelected));
		$this->GetForm()->Set('TRAVEL_TOUR_LIST',$oTravel->GetCheckboxList(REFDATA_TRAVEL_TRANSPORT_PREFIX,$aSelected));
		
		
		// accomodation 
		$oAccom = new Refdata(REFDATA_ACCOMODATION);
		$aSelected = array();
		if (isset($_POST['submit'])) {
			$aSelected = Mapping::GetIdByKey($_REQUEST,REFDATA_ACCOMODATION_PREFIX);
		} elseif ($this->GetProfile() instanceof TourProfile) {
			$aSelected = $this->GetProfile()->GetAccomodationIdList();
		}
		$this->GetForm()->Set('ACCOMODATION_LIST_SELECTED_COUNT',count($aSelected));
		$this->GetForm()->Set('ACCOMODATION_LIST',$oAccom->GetCheckboxList(REFDATA_ACCOMODATION_PREFIX,$aSelected));
		
		
		// meals
		$oMeals = new Refdata(REFDATA_MEALS);
		$aSelected = array();
		if (isset($_POST['submit'])) {
			$aSelected = Mapping::GetIdByKey($_REQUEST,REFDATA_MEALS_PREFIX);
		} elseif ($this->GetProfile() instanceof TourProfile) {
			$aSelected = $this->GetProfile()->GetMealsIdList();
		}
		$this->GetForm()->Set('MEALS_LIST_SELECTED_COUNT',count($aSelected));
		$this->GetForm()->Set('MEALS_LIST',$oMeals->GetCheckboxList(REFDATA_MEALS_PREFIX,$aSelected));

	}
	

	
	private function SetJobProfileFormElements() {

		
		$this->GetForm()->Set('JOB_START_DATE',Date::GetDateInput('StartDate',true,true,true));

		// duration_from / duration_to
		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_PLACEMENT_JOB_DURATION_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_JOB_DURATION_FROM];
		} elseif ($this->GetProfile() instanceof JobProfile) {
			$selected = $this->GetProfile()->GetDurationFromId();
		}
		$this->GetForm()->Set('JOB_DURATION_FROM',$oDuration->GetDDlist($selected));

		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_PLACEMENT_JOB_DURATION_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_JOB_DURATION_TO];
		} elseif ($this->GetProfile() instanceof JobProfile) {
			$selected = $this->GetProfile()->GetDurationToId();
		}
		$this->GetForm()->Set('JOB_DURATION_TO',$oDuration->GetDDlist($selected));
		
		// contract type
		$oContractType = new Refdata(REFDATA_JOB_CONTRACT_TYPE);
		$oContractType->SetOrderBySql(' id ASC');
		$oContractType->SetElementName(PROFILE_FIELD_PLACEMENT_JOB_CONTRACT_TYPE);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_PLACEMENT_JOB_CONTRACT_TYPE];
		} elseif ($this->GetProfile() instanceof JobProfile) {
			$selected = $this->GetProfile()->GetContractType();
		}
		$this->GetForm()->Set('JOB_CONTRACT_TYPE',$oContractType->GetDDlist($selected));
		

		// job option checkboxes (accom, meals, ski passes etc)
		$oJobOptions = new Refdata(REFDATA_JOB_OPTIONS);
		$aSelected = array();
		if (isset($_POST['submit'])) {
			$aSelected = Mapping::GetIdByKey($_REQUEST,REFDATA_JOB_OPTIONS_PREFIX);
		} elseif ($this->GetProfile() instanceof JobProfile) {
			$aSelected = $this->GetProfile()->GetJobOptions();
		}
		$this->GetForm()->Set('JOB_OPTIONS',$oJobOptions->GetCheckboxList(REFDATA_JOB_OPTIONS_PREFIX,$aSelected));
		

		// closing / application date
		$this->GetForm()->Set('JOB_CLOSING_DATE',Date::GetDateInput('CloseDate',true,true,true));
		
			
		
	}
	
	
	private function SetProfileOptions() {

		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		global $oAuth;
		
		/* ADD / EDIT -> get profile type and enquiry options */ 	
		$oCProfile = new CompanyProfile();
		if ($oAuth->oUser->isAdmin) {
			
			if ($this->GetMode() == self::MODE_EDIT) {
				/* the company defaults */
				$oCProfile->SetProfileOptionBitmap($this->GetProfile()->GetCompProfOpt());
				$oCProfile->SetEnquiryOptionBitmap($this->GetProfile()->GetCompEnqOpt());
			} elseif ($this->GetMode() == self::MODE_ADD) {
				/* the website defaults */
				$oCProfile->SetProfileOptionBitmap(DEFAULT_PROFILE_OPT);
				$oCProfile->SetEnquiryOptionBitmap(DEFAULT_ENQUIRY_OPT);
			} else { /* we are viewing the profile */
				$oCProfile->SetEnquiryOptionBitmap($this->GetProfile()->GetCompEnqOpt());
			}
		} elseif ($oAuth->oUser->isValidUser) { 
			/* get comp users prof/enq options from session (setup on user object) 
			 * not sure why we don't look at company profile here eg $oCProfile->GetCompProfOpt() 
			*/
			$oCProfile->SetProfileOptionBitmap($oAuth->oUser->prof_opt);
			$oCProfile->SetEnquiryOptionBitmap($oAuth->oUser->enq_opt);		
		} else {
			/* setup enquiry types based on defaults for the placement's company */
			$oCProfile->SetEnquiryOptionBitmap($this->GetProfile()->GetCompEnqOpt());
		}
		
		$this->SetCompanyProfile($oCProfile);
		
		$this->GetForm()->Set('oCProfile',$oCProfile);
	}
	
	/* Set profile type : Volunteer, Tour, Job etc */
	private function SetDefaultProfileType() {
			
		if ($this->GetMode() == self::MODE_ADD) {
			
			if (isset($_POST['submit'])) {
				$sView = $_REQUEST[PROFILE_FIELD_PLACEMENT_PROFILE_TYPE_ID];
			} else {
				if ($this->GetCompanyProfile()->HasProfileOption(PROFILE_VOLUNTEER)) {
					$sView = PROFILE_VOLUNTEER;
				} elseif ($this->GetCompanyProfile()->HasProfileOption(PROFILE_TOUR)) {
					$sView = PROFILE_TOUR;
				} elseif ($this->GetCompanyProfile()->HasProfileOption(PROFILE_JOB)) {
					$sView = PROFILE_JOB;
				}
			}
		} elseif ($this->GetMode() == self::MODE_EDIT) {
			if (isset($_POST['submit'])) {
				$sView = $_REQUEST[PROFILE_FIELD_PLACEMENT_PROFILE_TYPE_ID];
			} else {
				$sView = $this->GetProfile()->GetType();
			}
		}
			
		$this->GetForm()->Set('PROFILE_TYPE_ID',$sView);
	}
	
		
	private function GetTabbedPanel() {
		return $this->oTabbedPanel;
	}
	
	private function SetTabbedPanel() {

		global $oHeader;

		$oProfile = $this->GetProfile();

		ob_start();
			$contents_edit = $this->GetForm()->Render();
	    ob_end_clean();
				
		if (($this->GetMode() == self::MODE_EDIT)) {
			ob_start();
		       require_once("./templates/profile_images.php");
		   	   $contents_images = ob_get_contents();
		    ob_end_clean();
			ob_start();
		       require_once("./templates/profile_video.php");
		   	   $contents_video = ob_get_contents();
		    ob_end_clean();
		}
	
	    
	    $tabbed_panel_id = ($this->GetMode() == self::MODE_ADD) ? "TP09" : "TP06";
	   
		$cols = 5;
		$oTabbedPanel = new TabbedPanel();
		$oTabbedPanel->SetId($tabbed_panel_id);
		$oTabbedPanel->SetCols($cols);
		$oTabbedPanel->SetCookieName(COOKIE_TAB_NAME);
		$oTabbedPanel->LoadFromXmlFile(ROOT_PATH."/conf/tabbed_panels.xml");
		
	
		if ($this->GetMode() == self::MODE_ADD) {
			$oTabbedPanel->SetTitle($this->GetForm()->Get('STEP_TITLE'));
		} else {
			$oTabbedPanel->SetTitle($oProfile->GetTitle()." : Edit Profile");	
		}
		

		/* messages panel */
		$oMessagesPanel = new Layout();
		$oMessagesPanel->Set('UI_MSG',$this->GetMessageFromSession());
		$oMessagesPanel->Set('VALIDATION_ERRORS',$this->GetValidationErrors());		
		$oMessagesPanel->LoadTemplate("messages_template.php");
		$oTabbedPanel->SetContentFromObject($oMessagesPanel);
		
	
		/* edit general info */ 
		$oLayout = new Layout();
		$oLayout->SetId("TC01");
		$oLayout->SetCols(5);
		$oLayout->SetContent($this->GetForm()->Render());
		$oLayout->LoadTemplate("column.php");	
		$oTabbedPanel->SetContentFromObject($oLayout);
	
		
		if ($this->GetMode() == self::MODE_EDIT) {
		
			/* edit images */
			$oLayout = new Layout();
			$oLayout->SetId("TC02");
			$oLayout->SetCols(5);
			$oLayout->SetContent($contents_images);
			$oLayout->LoadTemplate("column.php");	
			$oTabbedPanel->SetContentFromObject($oLayout);
		
			/* edit youtube video */
			$oLayout = new Layout();
			$oLayout->SetId("TC03");
			$oLayout->SetCols(5);
			$oLayout->SetContent($contents_video);
			$oLayout->LoadTemplate("column.php");	
			$oTabbedPanel->SetContentFromObject($oLayout);
			
		}	
		
		
		
		/* if the page was submitted, set images tab active if this was being viewed */
		if (isset($_COOKIE[COOKIE_TAB_NAME]) && ($_COOKIE[COOKIE_TAB_NAME] == "TAB02")) {
		 	$oTabbedPanel->SetActiveTabById(strtoupper($_COOKIE[COOKIE_TAB_NAME]));
		}
		
		$oTabbedPanel->LoadTemplate("tabbed_panel.php");
		$oHeader->SetJsOnload($oTabbedPanel->GetEventJQuery());
		$oHeader->Reload();
		
		
		$this->oTabbedPanel = $oTabbedPanel;				
		
	}
	
	protected function GetForm() {
		return $this->oPlacementForm;
	}
	
	protected function SetPlacementForm($oTemplate) {
		
		$this->oPlacementForm = $oTemplate;

	}
	
	protected function SetPlacementProfile($oProfile) {
		$this->oPlacementProfile = $oProfile;
	}
	
	protected function GetProfile() {
		return $this->oPlacementProfile;
	}
	
	protected function GetNewPlacementProfile() {
		
		global $oBrand;
		
		if (isset($_REQUEST[PROFILE_FIELD_PLACEMENT_PROFILE_TYPE_ID])) {
			$profile_type = $_REQUEST[PROFILE_FIELD_PLACEMENT_PROFILE_TYPE_ID];
		} else {
			$profile_type = $oBrand->GetDefaultPlacementProfileTypeId();
		}
		
		$oPlacementProfile = ProfileFactory::Get($profile_type);
		
		$this->SetPlacementProfile($oPlacementProfile);
	}
	
	
	protected function SetCompanyProfile($oProfile) {
		if (DEBUG) Logger::Msg(get_class($this)."::".__FUNCTION__."()");
		
		$this->oCompanyProfile = $oProfile;
	}
	
	protected function GetCompanyProfile() {
		return $this->oCompanyProfile;
	}

	private function SetCompanyId($id) {
		$this->company_id = $id;
	}
	
	private function GetCompanyId() {
		return $this->company_id;
	}
	
	private function SetPlacementId($id) {
		$this->placement_id = $id;
	}
	
	private function GetPlacementId() {
		return $this->placement_id;
	}

	private function SetPlacementUrlName($request_array) {
	    
	    if (($request_array[1] == ROUTE_PLACEMENT) &&
	        (strlen($request_array[2]) > 1) &&
	        ($request_array[3] == ROUTE_EDIT))
	    {
	        $this->placement_url_name = $request_array[2];
	    }
	    
	    // /company/<company-name>/<placement-name/>/edit
	    if (($request_array[1] == ROUTE_COMPANY) &&
	        (strlen($request_array[2]) > 1) &&
	        (strlen($request_array[3]) > 1))
	    {
	        $this->placement_url_name = $request_array[3];
	    }
	}
	
	public function GetPlacementUrlName() {
		return $this->placement_url_name;
	}

	public function GetCompanyUrlName() {
	    return $this->company_url_name;
	}
	
	/* 
	 * oMVCController routes and are stored in session -
	 * clear all data attributes from this instance
	 * to prevent data being saved/passed via session
	 * 
	 */
	private function Clear() {
		unset($this->company_url_name);
		unset($this->company_id);
		unset($this->oCompanyProfile); 
		unset($this->placement_url_name);
		unset($this->placement_id);
		unset($this->oPlacementProfile);
		unset($this->mode);
		unset($this->mode);  
		unset($this->oPlacementForm);
		unset($this->oTabbedPanel);
		
	}
}


?>
