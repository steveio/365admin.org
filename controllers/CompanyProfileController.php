<?php

/*
 * MVC Controller for Company Profile Requests
 * 
 * Handles requests to VIEW, EDIT, ADD, DELETE
 * 
 * Fetches, processes, persists assembles profile data 
 * 
 * Handles UI template rendering 
 * 
 */


class CompanyProfileController extends ProfileController {

	const MODE_VIEW  = 0;
	const MODE_ADD  = 1;
	const MODE_EDIT  = 2;
	const MODE_DELETE  = 3;

	private $mode; // set according to request type

	private $aRequestArray = array(); // Request URI

	private $company_url_name; // string validated unique company url token from request eg /company/<company-url-name>
	private $company_id; // int id of profile to be editted
	private $oCompanyProfile; // object instance of company profile being editted (if listing = EXISTING)

	private $oCompanyForm; // generic company form elements common to all profiles
	private $oGeneralProfileForm; // @todo - merge into comany form?
	private $oSummerCampForm; // summer camp specific form elements
	private $oSeasonalJobsForm; // seasonal jobs employer specific form elements
	private $oVolunteerProjectForm; // volunteer travel project form elements
	private $oTeachingProjectForm; // teaching project travel project form elements

	private $oTabbedPanel;


	public function __construct( ){

		parent::__construct();

	}


	/*
	 * The main method called during step execution
	 *
	 */
	public function Process() {

		global $oSession;

		if (!$this->SetMode()) // determine request type ( VIEW || ADD || EDIT )
		{
		    $this->bPassThrough = true;		    
		    return false; 
		}
		
		$this->SetInValid();

		switch($this->GetMode()) {
			case self::MODE_ADD :
				$this->CheckPermissions();
				$this->AddProfile();
				$this->SetFormElements();
				break;
			case self::MODE_DELETE :
				$this->GetCompanyIdFromUrl();
				$this->CheckPermissions();
				$this->DeleteProfile();
				break;
			case self::MODE_EDIT :
				$this->GetCompanyIdFromUrl();
				$this->CheckPermissions();
				if (isset($_POST['save_video_btn'])) {
					$this->SaveYouTubeVideo(PROFILE_COMPANY, $this->GetCompanyId(), $_POST['video']);
				}
				$this->DoImageUpload("COMPANY", $this->GetCompanyId());
				$this->EditProfile();
				$this->SetFormElements();
				break;
			case self::MODE_VIEW :
				$this->ViewProfile();
				break;
			default:
				throw new Exception(ERROR_INVALID_MODE);

		}
	}

	public function SetMVCMode($intMode)
	{
	    if ($intMode != null)
	    {
	        $this->mode = $intMode;
	    }
	}

	private function SetMode() {

		global $oBrand;
		
		$this->aRequestArray = Request::GetUri("ARRAY");

		if ($this->aRequestArray[1] != ROUTE_COMPANY) throw new Exception(ERROR_404_INVALID_REQUEST.implode("/",$$this->aRequestArray));

		// Non MVC handled request URL - view/edit placement, company A-Z... return to RequestRouter
		if (($this->aRequestArray[1] == ROUTE_COMPANY) &&
		    (strlen($this->aRequestArray[2]) > 1) &&
		    (strlen($this->aRequestArray[3]) > 1) &&
		    ($this->aRequestArray[3] != "edit"))
		{
		    return false;
		}

		$this->SetCompanyUrlName($this->aRequestArray[2]);

		switch(TRUE) {
			case $this->RequestAdd() :
				$this->mode = self::MODE_ADD;
				return true;
			case $this->RequestView() :
			    $this->mode = self::MODE_VIEW;
			    return true;
			case $this->RequestDelete() :
				$this->mode = self::MODE_DELETE;
				return true;
			case $this->RequestEdit() :
				$this->mode = self::MODE_EDIT;
				return true;
			default :
			    throw new Exception(ERROR_404_INVALID_REQUEST.implode("/",$this->aRequestArray));
		}

	}

	private function RequestAdd() {
		if (strtoupper($this->aRequestArray[2]) == "ADD") {
			return TRUE;
		}
	}

	private function RequestView() {
	    if (($this->aRequestArray[1] == ROUTE_COMPANY) && (strlen($this->aRequestArray[2]) > 2) && (strlen($this->aRequestArray[3]) < 1)) {
			return TRUE;
		}
	}

	// delete company request eg. /company/kumuku/delete
	private function RequestDelete() {

	    if (!Validation::ValidUriNamespaceIdentifier($this->aRequestArray[2])) {
	        throw new Exception(ERROR_COMPANY_PROFILE_INVALID_URL.$this->aRequestArray[2]);
		}

		if (strtolower($this->aRequestArray[3]) == ROUTE_DELETE) {
			return TRUE;
		}

	}

	// edit company request eg. /company/bunac/edit
	private function RequestEdit() {

	    if (!Validation::ValidUriNamespaceIdentifier($this->aRequestArray[2])) {
	        throw new Exception(ERROR_COMPANY_PROFILE_INVALID_URL.$this->aRequestArray[2]);
		}

		if (strtolower($this->aRequestArray[3]) == ROUTE_EDIT) {
			return TRUE;
		}

	}

	private function GetMode() {
		return $this->mode;
	}


	/* check permissions / access rights according to request type */
	private function CheckPermissions() {

		if (DEBUG)  Logger::Msg(__CLASS__."->".__FUNCTION__."()");

		global $oAuth,$oSession;


		// these should always be set correctly, for logged in and public users
		if (($oSession->GetListingType() == NULL) || ($oSession->GetMVCController() == NULL)) {
			throw new InvalidSessionException(ERROR_INVALID_SESSION);
		}

		switch($this->GetMode()) {
			case self::MODE_ADD :
				break;
			case self::MODE_EDIT :
			case self::MODE_VIEW :
			case self::MODE_DELETE :
				if (!$oAuth->ValidSession()) {
					$request_array = Request::GetUri("ARRAY");
					Http::Redirect("/".ROUTE_LOGIN.implode("/",$request_array));
				}

				if ($oAuth->oUser->isAdmin) return TRUE; // admin can edit any profile

				if ($oAuth->oUser->GetCompanyId() != $this->GetCompanyId()) { // company user can only edit profile owned by their company
					throw new Exception(ERROR_COMPANY_PROFILE_PERMISSIONS_FAIL." user_id: ".$oAuth->oUser->GetId().", requested_comp_id: ".$this->GetCompanyId());
				}
				break;
		}

		return TRUE;

	}

	public function ViewProfile() {

	    try {

            $oContentAssembler = new CompanyProfileContentAssembler();
	        $oContentAssembler->GetByPath($this->GetCompanyUrlName());
	        
	    } catch (Exception $e) {
	        throw $e;
	    }
	    
	}

	/*
	 * Get Company id and setup profile object according to type
	 * 
	 */
	private function GetCompanyIdFromUrl() {

		global $db;

		$oCompanyProfile = new CompanyProfile();
		
		$aRes = $oCompanyProfile->GetByUrlName($this->GetCompanyUrlName());

		if (!is_numeric($aRes['id'])) throw new Exception(ERROR_COMPANY_PROFILE_NOT_FOUND.$this->GetCompanyUrlName());
		
		$this->oProfile = ProfileFactory::Get($aRes['type']);

		$this->SetCompanyId($aRes['id']);

	}


	/*
	 * Set form elements/values according to the type of profile being viewed
	 *
	 */
	protected function SetFormElements() {

		global $oHeader, $oFooter, $oBrand, $oSession, $oAuth, $_CONFIG;

		// instantiate templates
		$this->SetCompanyForm(new Template);
		$this->SetGeneralProfileForm(new Template);
		$this->SetSummerCampForm(new Template);
		$this->SetSeasonalJobsForm(new Template);
		$this->SetTeachingProjectForm(new Template);
		$this->SetVolunteerProjectForm(new Template);


		$oJsInclude = new JsInclude();
		$oJsInclude->SetSrc("https://cdn.tiny.cloud/1/64vi9u0mlw972adwn9riluuctbqvquz44j5udsiffm2xvx3y/tinymce/6/tinymce.min.js");
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


		/* set default profile type based on saved profile or brand default */



		// id of default profile_type for this brand
		$brand_default_profile_type = $oBrand->GetDefaultCompanyProfileTypeId();

		// set profile type to existing id if this is a previously saved profile
		if ($this->GetMode() == self::MODE_EDIT) {
			$profile_type = $this->GetProfile()->GetProfileType();
		} else { // this is a new profile, set default id
			$profile_type = $brand_default_profile_type;
		}

		$aAvailableProfileTypeId = array();

		if (!$oAuth->oUser->isAdmin) {

			/* filter available profile types according to the brand being viewed
			 * - include the existing type if the profile was previously saved
			 *
			 */

			if (is_array($oBrand->GetAvailableCompanyProfileTypeId())) {
				$aAvailableProfileTypeId = $oBrand->GetAvailableCompanyProfileTypeId();
			}

			if (!in_array($profile_type,$aAvailableProfileTypeId)) {
				$aAvailableProfileTypeId[] = (int) $profile_type;
			}

		}

		/* whether to display category, activity, country options */
		$this->GetForm()->Set('DISPLAY_CAT_ACT_CTY_OPTIONS',$oBrand->GetDisplayCatActCtyOptions());

		$this->GetForm()->Set('COMPANY_TITLE',$oBrand->GetCompanyTitle());

		if (strlen($oBrand->GetFullDescLabel()) > 1) {
			$this->GetForm()->Set('FULL_DESC_LABEL',$oBrand->GetFullDescLabel());
		}

		// an object containing an array of all available profile types for this brand
		$oProfileType = new ProfileType($aAvailableProfileTypeId, PROFILE_COMPANY);

		/* company profile type HTML select list */
		$selected_profile_type = isset($_POST[PROFILE_FIELD_COMP_PROFILE_TYPE_ID]) ? $_POST[PROFILE_FIELD_COMP_PROFILE_TYPE_ID] : $profile_type;
		$this->GetForm()->Set('PROFILE_TYPE_LIST',$oProfileType->GetDDlist($selected_profile_type,PROFILE_FIELD_COMP_PROFILE_TYPE_ID,PROFILE_FIELD_COMP_PROFILE_TYPE_ID));
		$this->GetForm()->Set('PROFILE_TYPE_COUNT',$oProfileType->Count());
		if ($oProfileType->Count() == 1) {
			$this->GetForm()->Set('PROFILE_TYPE_SELECTED_ID',$profile_type);
		}

		/* set active set of extended profile elements */
		$this->GetForm()->Set('PROFILE_ACTIVE_PANEL','profile_type_'.$selected_profile_type);

		/* set type specific form elements/labels */
		$this->SetProfileTypeSpecificFormValues();

		/* set country select list */
		$oCountry = new Country(null);
		if (isset($_POST[PROFILE_FIELD_COMP_COUNTRY_ID])) {
			$selected = $_POST[PROFILE_FIELD_COMP_COUNTRY_ID];
		} else {
			$selected = $this->GetProfile()->GetCountryId();
		}
		$this->GetForm()->Set('COUNTRY_ID_SELECTED',$selected);
		$this->GetForm()->Set('COUNTRY_ID_LIST',$oCountry->GetCountryDropDown($selected,PROFILE_FIELD_COMP_COUNTRY_ID,PROFILE_FIELD_COMP_COUNTRY_ID));

		/* set state select list */
		$oState = new Refdata(REFDATA_US_STATE);
		$oState->SetElementId(PROFILE_FIELD_COMP_STATE_ID);
		$oState->SetElementName(PROFILE_FIELD_COMP_STATE_ID);
		$oState->SetElementCssClass("form-select");
		if (isset($_POST[PROFILE_FIELD_COMP_STATE_ID])) {
			$selected = $_POST[PROFILE_FIELD_COMP_STATE_ID];
		} else {
			$selected = $this->GetProfile()->GetStateId();
		}
		$this->GetForm()->Set('US_STATE_LIST',$oState->GetDDlist($selected));

		/* category, activity, country lists */
		$this->GetForm()->Set('ACTIVITY_LIST',$this->GetActivityList());
		$this->GetForm()->Set('CATEGORY_LIST',$this->GetCategoryList());
		$this->GetForm()->Set('COUNTRY_LIST',$this->GetCountryList());


		/* setup admin listing options (or defaults for a new listing request) */
		$this->SetAdminFormElements();

		/* now set extended profile form elements */
		$this->SetGeneralProfileFormElements();
		$this->SetSummerCampFormElements();
		$this->SetSeasonalJobsFormElements();
		$this->SetTeachingProjectFormElements();
		$this->SetVolunteerProjectFormElements();

		// inject profile type specific form elements into overall page template
		$this->GetForm()->Set('EXTENDED_FIELDSET_GENERAL_PROFILE', $this->GetGeneralProfileForm()->Render());
		$this->GetForm()->Set('EXTENDED_FIELDSET_SUMMERCAMP', $this->GetSummerCampForm()->Render());
		$this->GetForm()->Set('EXTENDED_FIELDSET_SEASONALJOBS', $this->GetSeasonalJobsForm()->Render());
		$this->GetForm()->Set('EXTENDED_FIELDSET_VOLUNTEER_PROJECT', $this->GetVolunteerProjectForm()->Render());
		$this->GetForm()->Set('EXTENDED_FIELDSET_TEACHING_PROJECT', $this->GetTeachingProjectForm()->Render());

		// general template parameters
		$this->GetForm()->Set('STEP_TITLE',$step_title = '');
		$this->GetForm()->Set('VALID',$this->Valid());
		$this->GetForm()->Set('VALIDATION_ERRORS',$this->GetValidationErrors());
		$this->GetForm()->Set('COMPANY_PROFILE',$this->GetProfile());


		$this->GetForm()->LoadTemplate("profile_company.php");

		/*
		 * add company profile edit form to tabbed panel,
		 * append any other tabs eg upload images, video etc
		 */
		$this->SetTabbedPanel();

		print $oHeader->Render();
		print $this->GetTabbedPanel()->Render();
		print $oFooter->Render();

		die();
	}


	private function SetAdminFormElements() {

		global $oAuth, $db, $_CONFIG;

		/* admin only options */
		if ($oAuth->oUser->isAdmin) {
			// listing options
			$aListingOption = ListingOption::GetAll($_CONFIG['site_id'],$currency = 'GBP',$from = 0, $to = 3);
			$this->GetForm()->Set('ADMIN_LISTING_OPTIONS',$aListingOption);

			// listing type (eg FREE, BASIC, ENHANCED...)
			$oListing = new Listing();
			if ((!is_numeric($this->GetProfile()->GetId())) ||
				(!$oListing->GetCurrentByCompanyId($this->GetProfile()->GetId()))
			) {
				$this->GetProfile()->SetListingRecordFl(FALSE);
				$this->GetForm()->Set('ADMIN_CURRENT_LISTING_OBJECT',NULL);
			} else {
				$this->GetForm()->Set('ADMIN_CURRENT_LISTING_OBJECT',$oListing);
			}


			// $sWebSiteHTML
			$oWebsite = new Website($db);
			$aSelected = array();
			if (isset($_POST['submit'])) {
				$aSelected = Mapping::GetIdByKey($_REQUEST,"web_");
			} elseif($this->GetProfile()->GetId()) {
				$aSelected = $oWebsite->GetCompanyWebsiteList($this->GetProfile()->GetId());
			}

			$this->GetForm()->Set('ADMIN_WEBSITE_HOMEPAGE_OPTIONS',$oWebsite->GetSiteSelectList($aSelected));


		}

	}


	/* set form titles / labels according to type of profile being added/editted */
	protected function SetProfileTypeSpecificFormValues() {

		if ($this->GetMode() == self::MODE_ADD) {
			$step_title = "Company Profile - Step 3 of 4";
		} else {
			$step_title = "Edit Company Profile - ".$this->GetProfile()->GetTitle();
		}

	}


	/* @depreciate - form elements for base company profile (@todo migrate into main company profile */
	protected function SetGeneralProfileFormElements() {

		$this->GetGeneralProfileForm()->Set('VALIDATION_ERRORS',$this->GetValidationErrors());
		$this->GetGeneralProfileForm()->Set('COMPANY_PROFILE',$this->GetProfile());

		$this->GetGeneralProfileForm()->LoadTemplate("general_company_profile.php");
	}


	protected function SetSeasonalJobsFormElements() {

		global $oSession;

		// duration_from / duration_to
		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_SEASONALJOBS_DURATION_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SEASONALJOBS_DURATION_FROM];
		} elseif ($this->GetProfile() instanceof SeasonalJobEmployerProfile) {
			$selected = $this->GetProfile()->GetDurationFromId();
		}
		$this->GetSeasonalJobsForm()->Set('DURATION_FROM',$oDuration->GetDDlist($selected));

		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_SEASONALJOBS_DURATION_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SEASONALJOBS_DURATION_TO];
		} elseif ($this->GetProfile() instanceof SeasonalJobEmployerProfile) {
			$selected = $this->GetProfile()->GetDurationToId();
		}
		$this->GetSeasonalJobsForm()->Set('DURATION_TO',$oDuration->GetDDlist($selected));

		// no staff
		$oNoStaff = new Refdata(REFDATA_INT_RANGE);
		$oNoStaff->SetOrderBySql(' id ASC');
		$oNoStaff->SetElementName(PROFILE_FIELD_SEASONALJOBS_NO_STAFF);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SEASONALJOBS_NO_STAFF];
		} elseif ($this->GetProfile() instanceof SeasonalJobEmployerProfile) {
			$selected = $this->GetProfile()->GetNoStaff();
		}
		$this->GetSeasonalJobsForm()->Set('NO_STAFF',$oNoStaff->GetDDlist($selected));


		$this->GetSeasonalJobsForm()->Set('VALIDATION_ERRORS',$this->GetValidationErrors());
		$this->GetSeasonalJobsForm()->Set('COMPANY_PROFILE',$this->GetProfile());

		$this->GetSeasonalJobsForm()->LoadTemplate("profile_seasonaljobs.php");

	}

	protected function SetVolunteerProjectFormElements() {

		global $oSession;

		// no volunteers
		$oNoStaff = new Refdata(REFDATA_INT_RANGE);
		$oNoStaff->SetOrderBySql(' id ASC');
		$oNoStaff->SetElementId(PROFILE_FIELD_VOLUNTEER_NO_PLACEMENTS);
		$oNoStaff->SetElementName(PROFILE_FIELD_VOLUNTEER_NO_PLACEMENTS);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_VOLUNTEER_NO_PLACEMENTS];
		} elseif ($this->GetProfile() instanceof VolunteerTravelProjectProfile) {
			$selected = $this->GetProfile()->GetNoPlacements();
		}
		$this->GetVolunteerProjectForm()->Set('NO_PLACEMENTS',$oNoStaff->GetDDlist($selected));

		// organisation type
		$oOrgType = new Refdata(REFDATA_ORG_PROJECT_TYPE);
		$oOrgType->SetOrderBySql(' id ASC');
		$oOrgType->SetElementId(PROFILE_FIELD_VOLUNTEER_ORG_TYPE);
		$oOrgType->SetElementName(PROFILE_FIELD_VOLUNTEER_ORG_TYPE);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_VOLUNTEER_ORG_TYPE];
		} elseif ($this->GetProfile() instanceof VolunteerTravelProjectProfile) {
			$selected = $this->GetProfile()->GetOrgType();
		}
		$this->GetVolunteerProjectForm()->Set('ORG_TYPE',$oOrgType->GetDDlist($selected));


		// duration_from / duration_to
		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_VOLUNTEER_DURATION_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_VOLUNTEER_DURATION_FROM];
		} elseif ($this->GetProfile() instanceof VolunteerTravelProjectProfile) {
			$selected = $this->GetProfile()->GetDurationFromId();
		}
		$this->GetVolunteerProjectForm()->Set('DURATION_FROM',$oDuration->GetDDlist($selected));

		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_VOLUNTEER_DURATION_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_VOLUNTEER_DURATION_TO];
		} elseif ($this->GetProfile() instanceof VolunteerTravelProjectProfile) {
			$selected = $this->GetProfile()->GetDurationToId();
		}
		$this->GetVolunteerProjectForm()->Set('DURATION_TO',$oDuration->GetDDlist($selected));


		// price_from, price_to
		$oPriceFrom = new Refdata(REFDATA_APPROX_COST);
		$oPriceFrom->SetOrderBySql(' sort_order ASC');
		$oPriceFrom->SetElementName(PROFILE_FIELD_VOLUNTEER_PRICE_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_VOLUNTEER_PRICE_FROM];
		} elseif ($this->GetProfile() instanceof VolunteerTravelProjectProfile) {
			$selected = $this->GetProfile()->GetPriceFromId();
		}
		$this->GetVolunteerProjectForm()->Set('PRICE_FROM',$oPriceFrom->GetDDlist($selected));

		$oPriceTo = new Refdata(REFDATA_APPROX_COST);
		$oPriceTo->SetOrderBySql(' sort_order ASC');
		$oPriceTo->SetElementName(PROFILE_FIELD_VOLUNTEER_PRICE_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_VOLUNTEER_PRICE_TO];
		} elseif ($this->GetProfile() instanceof VolunteerTravelProjectProfile) {
			$selected = $this->GetProfile()->GetPriceToId();
		}
		$this->GetVolunteerProjectForm()->Set('PRICE_TO',$oPriceTo->GetDDlist($selected));

		// currency
		$oCurrency = new Refdata(REFDATA_CURRENCY);
		$oCurrency->SetOrderBySql(' id ASC');
		$oCurrency->SetElementName(PROFILE_FIELD_VOLUNTEER_CURRENCY);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_VOLUNTEER_CURRENCY];
		} elseif ($this->GetProfile() instanceof VolunteerTravelProjectProfile) {
			$selected = $this->GetProfile()->GetCurrencyId();
		}
		$this->GetVolunteerProjectForm()->Set('CURRENCY',$oCurrency->GetDDlist($selected, $no_default = TRUE));


		// species
		$oSpecies = new Refdata(REFDATA_SPECIES);
		$aSelected = array();
		if (isset($_POST['submit'])) {
			$aSelected = Mapping::GetIdByKey($_REQUEST,REFDATA_SPECIES_PREFIX);
		} elseif ($this->GetProfile() instanceof VolunteerTravelProjectProfile) {
			$aSelected = $this->GetProfile()->GetSpeciesList();
		}
		$this->GetVolunteerProjectForm()->Set('SPECIES_LIST_SELECTED_COUNT',count($aSelected));
		$this->GetVolunteerProjectForm()->Set('SPECIES_LIST',$oSpecies->GetCheckboxList(REFDATA_SPECIES_PREFIX,$aSelected));

		// habitats
		$oHabitats = new Refdata(REFDATA_HABITATS);
		$aSelected = array();
		if (isset($_POST['submit'])) {
			$aSelected = Mapping::GetIdByKey($_REQUEST,REFDATA_HABITATS_PREFIX);
		} elseif ($this->GetProfile() instanceof VolunteerTravelProjectProfile) {
			$aSelected = $this->GetProfile()->GetHabitatsList();
		}
		$this->GetVolunteerProjectForm()->Set('HABITATS_LIST_SELECTED_COUNT',count($aSelected));
		$this->GetVolunteerProjectForm()->Set('HABITATS_LIST',$oHabitats->GetCheckboxList(REFDATA_HABITATS_PREFIX,$aSelected));



		$this->GetVolunteerProjectForm()->Set('VALIDATION_ERRORS',$this->GetValidationErrors());
		$this->GetVolunteerProjectForm()->Set('COMPANY_PROFILE',$this->GetProfile());

		$this->GetVolunteerProjectForm()->LoadTemplate("profile_volunteer.php");

	}

	protected function SetTeachingProjectFormElements() {

		global $oSession;
		
		// duration_from / duration_to
		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_TEACHING_DURATION_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_TEACHING_DURATION_FROM];
		} elseif ($this->GetProfile() instanceof TeachingProjectProfile) {
			$selected = $this->GetProfile()->GetDurationFromId();
		}
		$this->GetTeachingProjectForm()->Set('DURATION_FROM',$oDuration->GetDDlist($selected));

		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_TEACHING_DURATION_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_TEACHING_DURATION_TO];
		} elseif ($this->GetProfile() instanceof TeachingProjectProfile) {
			$selected = $this->GetProfile()->GetDurationToId();
		}
		$this->GetTeachingProjectForm()->Set('DURATION_TO',$oDuration->GetDDlist($selected));

		// no teachers
		$oNoStaff = new Refdata(REFDATA_INT_RANGE);
		$oNoStaff->SetOrderBySql(' id ASC');
		$oNoStaff->SetElementName(PROFILE_FIELD_TEACHING_NO_TEACHERS);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_TEACHING_NO_TEACHERS];
		} elseif ($this->GetProfile() instanceof TeachingProjectProfile) {
			$selected = $this->GetProfile()->GetNoTeachers();
		}
		$this->GetTeachingProjectForm()->Set('NUMBER_OF_TEACHERS',$oNoStaff->GetDDlist($selected));

		// class size
		$oClassSize = new Refdata(REFDATA_INT_RANGE);
		$oClassSize->SetOrderBySql(' id ASC');
		$oClassSize->SetElementName(PROFILE_FIELD_TEACHING_CLASS_SIZE);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_TEACHING_CLASS_SIZE];
		} elseif ($this->GetProfile() instanceof TeachingProjectProfile) {
			$selected = $this->GetProfile()->GetClassSize();
		}
		$this->GetTeachingProjectForm()->Set('CLASS_SIZE',$oClassSize->GetDDlist($selected));

		// duration / length
		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_TEACHING_DURATION);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_TEACHING_DURATION];
		} elseif ($this->GetProfile() instanceof TeachingProjectProfile) {
			$selected = $this->GetProfile()->GetDuration();
		}
		$this->GetTeachingProjectForm()->Set('DURATION_LENGTH',$oDuration->GetDDlist($selected));


		$this->GetTeachingProjectForm()->Set('VALIDATION_ERRORS',$this->GetValidationErrors());
		$this->GetTeachingProjectForm()->Set('COMPANY_PROFILE',$this->GetProfile());

		$this->GetTeachingProjectForm()->LoadTemplate("profile_teaching.php");

	}


	/* form elements for a Summer Camp profile */
	protected function SetSummerCampFormElements() {

		global $oSession;

		// duration_from / duration_to
		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_SUMMERCAMP_DURATION_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_DURATION_FROM];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetDurationFromId();
		}
		$this->GetSummerCampForm()->Set('DURATION_FROM',$oDuration->GetDDlist($selected));

		$oDuration = new Refdata(REFDATA_DURATION);
		$oDuration->SetOrderBySql(' id ASC');
		$oDuration->SetElementName(PROFILE_FIELD_SUMMERCAMP_DURATION_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_DURATION_TO];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetDurationToId();
		}
		$this->GetSummerCampForm()->Set('DURATION_TO',$oDuration->GetDDlist($selected));


		// price_from, price_to
		$oPriceFrom = new Refdata(REFDATA_APPROX_COST);
		$oPriceFrom->SetOrderBySql(' sort_order ASC');
		$oPriceFrom->SetElementName(PROFILE_FIELD_SUMMERCAMP_PRICE_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_PRICE_FROM];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetPriceFromId();
		}
		$this->GetSummerCampForm()->Set('PRICE_FROM',$oPriceFrom->GetDDlist($selected));

		$oPriceTo = new Refdata(REFDATA_APPROX_COST);
		$oPriceTo->SetOrderBySql(' sort_order ASC');
		$oPriceTo->SetElementName(PROFILE_FIELD_SUMMERCAMP_PRICE_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_PRICE_TO];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetPriceToId();
		}
		$this->GetSummerCampForm()->Set('PRICE_TO',$oPriceTo->GetDDlist($selected));

		// currency
		$oCurrency = new Refdata(REFDATA_CURRENCY);
		$oCurrency->SetOrderBySql(' id ASC');
		$oCurrency->SetElementName(PROFILE_FIELD_SUMMERCAMP_CURRENCY);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_CURRENCY];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetCurrencyId();
		}
		if (!is_numeric($selected)) {
			$selected = 292; // USD
		}
		$this->GetSummerCampForm()->Set('CURRENCY',$oCurrency->GetDDlist($selected, $no_default = TRUE));

		$oCampActivity = new Refdata(REFDATA_ACTIVITY);
		$aSelected = array();
		if (isset($_POST['submit'])) {
			$aSelected = Mapping::GetIdByKey($_REQUEST,REFDATA_ACTIVITY_PREFIX);
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$aSelected = $this->GetProfile()->GetCampActivityList();
		}
		$this->GetSummerCampForm()->Set('CAMP_ACTIVITY_LIST',$oCampActivity->GetCheckboxList(REFDATA_ACTIVITY_PREFIX,$aSelected));

		$oCampType = new Refdata(REFDATA_CAMP_TYPE);
		$oCampType->SetElementId(PROFILE_FIELD_SUMMERCAMP_CAMP_TYPE);
		$aSelected = array();
		if (isset($_POST['submit'])) {
			$aSelected = Mapping::GetIdByKey($_REQUEST,REFDATA_CAMP_TYPE_PREFIX);
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$aSelected = $this->GetProfile()->GetCampTypeList();
		}
		$this->GetSummerCampForm()->Set('CAMP_TYPE_LIST',$oCampType->GetCheckboxList(REFDATA_CAMP_TYPE_PREFIX,$aSelected));

		$oCampJobType = new Refdata(REFDATA_CAMP_JOB_TYPE);
		$oCampJobType->SetElementId(PROFILE_FIELD_SUMMERCAMP_CAMP_JOB_TYPE);
		$aSelected = array();
		if (isset($_POST['submit'])) {
			$aSelected = Mapping::GetIdByKey($_REQUEST,REFDATA_CAMP_JOB_TYPE_PREFIX);
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$aSelected = $this->GetProfile()->GetCampJobTypeList();
		}
		$this->GetSummerCampForm()->Set('CAMP_JOB_TYPE_LIST',$oCampJobType->GetCheckboxList(REFDATA_CAMP_JOB_TYPE_PREFIX,$aSelected));

		$oNoStaff = new Refdata(REFDATA_INT_RANGE);
		$oNoStaff->SetOrderBySql(' id ASC');
		$oNoStaff->SetElementId(PROFILE_FIELD_SUMMERCAMP_NO_STAFF);
		$oNoStaff->SetElementName(PROFILE_FIELD_SUMMERCAMP_NO_STAFF);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_NO_STAFF];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetNoStaff();
		}
		$this->GetSummerCampForm()->Set('NUMBER_OF_STAFF_LIST',$oNoStaff->GetDDlist($selected));

		$oCampGender = new Refdata(REFDATA_CAMP_GENDER);
		$oCampGender->SetOrderBySql(' id ASC');
		$oCampGender->SetElementId(PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER);
		$oCampGender->SetElementName(PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetCampGender();
		}
		$this->GetSummerCampForm()->Set('CAMP_GENDER_LIST',$oCampGender->GetDDlist($selected));

		$oStaffGender = new Refdata(REFDATA_GENDER);
		$oStaffGender->SetOrderBySql(' id ASC');
		$oStaffGender->SetElementId(PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER);
		$oStaffGender->SetElementName(PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetStaffGender();
		}
		$this->GetSummerCampForm()->Set('STAFF_GENDER_LIST',$oStaffGender->GetDDlist($selected));

		$oCampReligion = new Refdata(REFDATA_RELIGION);
		$oCampReligion->SetOrderBySql(' id ASC');
		$oCampReligion->SetElementId(PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION);
		$oCampReligion->SetElementName(PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetCampReligion();
		}
		$this->GetSummerCampForm()->Set('CAMP_RELIGION_LIST',$oCampReligion->GetDDlist($selected));


		$oStaffOrigin = new Refdata(REFDATA_STAFF_ORIGIN);
		$oStaffOrigin->SetOrderBySql(' id ASC');
		$oStaffOrigin->SetElementId(PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN);
		$oStaffOrigin->SetElementName(PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetStaffOrigin();
		}
		$this->GetSummerCampForm()->Set('STAFF_ORIGIN_LIST',$oStaffOrigin->GetDDlist($selected));

		// camper age from / duration_to
		$oCamperAge = new Refdata(REFDATA_AGE_RANGE);
		$oCamperAge->SetOrderBySql(' id ASC');
		$oCamperAge->SetElementName(PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_FROM);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_FROM];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetCamperAgeFromId();
		}
		$this->GetSummerCampForm()->Set('CAMPER_AGE_FROM',$oCamperAge->GetDDlist($selected));

		$oCamperAge = new Refdata(REFDATA_AGE_RANGE);
		$oCamperAge->SetOrderBySql(' id ASC');
		$oCamperAge->SetElementName(PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_TO);
		$selected = '';
		if (isset($_POST['submit'])) {
			$selected = $_POST[PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_TO];
		} elseif ($this->GetProfile() instanceof SummerCampProfile) {
			$selected = $this->GetProfile()->GetCamperAgeToId();
		}
		$this->GetSummerCampForm()->Set('CAMPER_AGE_TO',$oCamperAge->GetDDlist($selected));


		$this->GetSummerCampForm()->Set('VALIDATION_ERRORS',$this->GetValidationErrors());
		$this->GetSummerCampForm()->Set('COMPANY_PROFILE',$this->GetProfile());

		$this->GetSummerCampForm()->LoadTemplate("profile_summercamp.php");

	}


	/* populate HTML form element values from company profile attributes */
	protected function SetFormValuesFromCompanyProfile() {

		global $_CONFIG;

		$_POST[PROFILE_FIELD_COMP_TITLE] = $this->GetProfile()->GetTitle();
		$_POST[PROFILE_FIELD_COMP_DESC_SHORT] = $this->GetProfile()->GetDescShort();
		$_POST[PROFILE_FIELD_COMP_DESC_LONG] = $this->GetProfile()->GetDescLong();

		foreach($_CONFIG['aProfileVersion'] as $version_id => $version_name) {

			$this->GetProfile()->SetProfileVersionIdToFetch($version_id);
			$this->GetProfile()->SetProfileVersionNoDefault(TRUE);

			$pv_prefix = "PV::".$version_id."::";

			$_POST[$pv_prefix.PROFILE_FIELD_COMP_TITLE] = $this->GetProfile()->GetTitle();
			$_POST[$pv_prefix.PROFILE_FIELD_COMP_DESC_SHORT] = $this->GetProfile()->GetDescShort();
			$_POST[$pv_prefix.PROFILE_FIELD_COMP_DESC_LONG] = $this->GetProfile()->GetDescLong();

		}

		$_POST[PROFILE_FIELD_COMP_URL] = $this->GetProfile()->GetUrl();
		$_POST[PROFILE_FIELD_COMP_EMAIL] = $this->GetProfile()->GetEmail();
		$_POST[PROFILE_FIELD_COMP_APPLY_URL] = $this->GetProfile()->GetApplyUrl();
		$_POST[PROFILE_FIELD_COMP_ADDRESS] = $this->GetProfile()->GetAddress();
		$_POST[PROFILE_FIELD_COMP_LOCATION] = $this->GetProfile()->GetLocation();
		$_POST[PROFILE_FIELD_COMP_TELEPHONE] = $this->GetProfile()->GetTel();

		$_POST[PROFILE_FIELD_COMP_GENERAL_DURATION] = $this->GetProfile()->GetDuration();
		$_POST[PROFILE_FIELD_COMP_GENERAL_PLACEMENT_INFO] = $this->GetProfile()->GetPlacementInfo();
		$_POST[PROFILE_FIELD_COMP_GENERAL_COSTS] = $this->GetProfile()->GetCosts();

		/* set extended type specific form element values */
		$this->GetProfile()->SetTypeSpecificFormValues();

	}

	protected function GetNewCompanyProfile() {

		global $oBrand;

		if (isset($_REQUEST[PROFILE_FIELD_COMP_PROFILE_TYPE_ID])) {
			$profile_type = $_REQUEST[PROFILE_FIELD_COMP_PROFILE_TYPE_ID];
		} else {
			$profile_type = $oBrand->GetDefaultCompanyProfileTypeId();
		}

		$oCompanyProfile = ProfileFactory::Get($profile_type);

		$this->SetCompanyProfile($oCompanyProfile);
	}


	/* get a company profile in preparation for editting */
	protected function GetProfileFromDb( $id ) {

		try {
			$profile_type = CompanyProfile::GetTypeById( $id );

			if (!is_numeric($profile_type)) throw new Exception(ERROR_INVALID_PROFILE_TYPE.__FUNCTION__.$id);

			$oCompanyProfile = ProfileFactory::Get($profile_type);
			$oCompanyProfile->GetById($id,$return = "PROFILE");

			$this->SetCompanyProfile($oCompanyProfile);

		} catch (Exception $e) {

			global $oSession;

			$oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_ID_ADD_COMPANY, $message = "Sorry, an error has occured and it was not possible to retrieve this company profile.  We've logged the error and will look into it.  Contact us for assistance.");
			$oErrorController = $oSession->GetMVCController()->GetRouteByUriMapping("/".ROUTE_ERROR);
			$oErrorController->UnsetMessage();
			$oErrorController->SetMessage($oMessage);

			$oSession->Save();

			Http::Redirect("/".ROUTE_ERROR);

			die();
		}

	}



	/* process a request to edit a company profile */
	protected function EditProfile() {

        $this->GetProfileFromDb( $this->GetCompanyId() );

		// handle update if form submitted
		if (isset($_POST['submit'])) {

			$this->AddUpdateCompanyDB($_POST);

		}

		// refresh company profile
		$this->GetProfileFromDb( $this->GetCompanyId() );

		$this->SetFormValuesFromCompanyProfile(); // set _POST form values from $this->oCompanyProfile

		// put ID in session for image_upload.php
		$_SESSION['id'] = $this->GetCompanyId();
		$_SESSION['link_to'] = "COMPANY";

	}



	/* add a new company profile, creates a new account application request if this is a new signup */
	protected function AddProfile() {

		global $oSession, $_CONFIG;
		
		$this->SetInValid();
		$this->SetInComplete();
		$this->UnsetMessage();

		$this->GetNewCompanyProfile();

		if (isset($_POST['submit'])) {
		    
			/* add / update company db record */
			if (!$this->AddUpdateCompanyDB($_POST)) {
				return FALSE;
			}

			/* if this is a new registration, write account details */
			if ($oSession->GetListingType() == LISTING_REQUEST_NEW) {
			    
				if ($this->AddAccount($_POST)) { // its a user registration, redirect to confirmation page

					$this->SetComplete();
					$this->Clear();

					$message = "<h1>New Listing Request Confirmation</h1>";
					$message .= "<p>Thanks for registering, one of our team will contact you shortly.</p>";
					$oMessage = new Message(MESSAGE_TYPE_SUCCESS, 'add_account_request', $message);

					$oConfirmationStep = $oSession->GetMVCController()->GetRouteByUriMapping("/".ROUTE_CONFIRMATION);
					$oConfirmationStep->UnsetMessage();
					$oConfirmationStep->SetMessage($oMessage);

					$oSession->Save();

					Http::Redirect("/".ROUTE_CONFIRMATION);

				}

			} else { // admin added a new company, redirect to company edit url

				$this->Clear(); // clear attributes on this step instance

				// setup user notification success msg
				$msg = "SUCCESS - Added Profile <br /><a href='/".ROUTE_DASHBOARD."' title='Return to Dashboard'>Click here</a> to return to dashboard";
				$oMessage = new Message(MESSAGE_TYPE_SUCCESS, MESSAGE_ID_ADD_PLACEMENT, $msg);
				$this->SetMessage($oMessage);
				$oSession->Save();

				Http::Redirect("/".ROUTE_COMPANY."/".$this->GetCompanyUrlName()."/edit");
				die();
			}

		}
	}

	/*
	 * INSERT/UPDATE company DB Record
	 *
	 * @param array form values
	 * @return bool mixed, TRUE on success, FALSE on failure
	 *
	 */
	protected function AddUpdateCompanyDB($aFormValues) {

		if (DEBUG)  Logger::Msg(__CLASS__."->".__FUNCTION__."()");

		global $oAuth, $db, $oSession, $oBrand;

		$response = array();

		/* basic sanitization */
		foreach($aFormValues as $k => $v) {
			if (is_string($v)) {
				$v = htmlspecialchars($v,ENT_NOQUOTES,"UTF-8");
			}
			$c[$k] = $v;
		}


		if ($this->GetMode() == self::MODE_EDIT) {
			$c['id'] = $this->GetCompanyId();
		}

		$bApproved = FALSE; // whether to set company profile as approved?

		// bundle profile versions if supplied
		$c['profile_version_data'] = $this->BundleProfileVersionFields($aFormValues);

		if ($this->GetMode() == self::MODE_ADD) {
			if (isset($c['status']) && $c['status'] == true) { // approved field only presented to admin
				$bApproved = TRUE;
			}

			// set other default options (eg listing type / level)
			if (!$oAuth->oUser->isAdmin) {
				$bApproved = FALSE;
				$c[PROFILE_FIELD_COMP_PROD_TYPE] = NEW_LISTING;
				$c[PROFILE_FIELD_COMP_PROFILE_QUOTA] = FREE_PQUOTA;
				$c[PROFILE_FIELD_COMP_PROFILE_OPTIONS] = DEFAULT_PROFILE_OPT;
				$c[PROFILE_FIELD_COMP_ENQUIRY_OPTIONS] = DEFAULT_ENQUIRY_OPT;
			}

		} elseif($this->GetMode() == self::MODE_EDIT) {
			if (isset($c['status']) && $c['status'] == true) { // admin has set approved field to TRUE
				$bApproved = TRUE;
			} else { // set approved field to current state
				$bApproved = $this->GetProfile()->GetStatus();
			}

			// set other admin only options (eg listing type / level), these cannot be changed by company user
			if (!$oAuth->oUser->isAdmin) {
				$c[PROFILE_FIELD_COMP_PROD_TYPE] = $this->GetProfile()->GetProdType();
				$c[PROFILE_FIELD_COMP_PROFILE_QUOTA] = $this->GetProfile()->GetProfileQuota();
				$c[PROFILE_FIELD_COMP_PROFILE_OPTIONS] = $this->GetProfile()->GetProfileOptionBitmap();
				$c[PROFILE_FIELD_COMP_ENQUIRY_OPTIONS] = $this->GetProfile()->GetEnquiryOptionBitmap();
			}
		}

		// if category, activity, country options were hidden, default them
		if (!$oBrand->GetDisplayCatActCtyOptions()) {
			$this->SetDefaultMappingFormValues($oBrand->GetDefaultCategoryId(),"cat_",$c);
			$this->SetDefaultMappingFormValues($oBrand->GetDefaultActivityId(),"act_",$c);

			// was a country value other than the default was supplied?
			if (in_array($c['country_id'],$oBrand->GetDefaultCountryId())) {
				$this->SetDefaultMappingFormValues($oBrand->GetDefaultCountryId(),"cty_",$c);
			} else {
				$this->SetDefaultMappingFormValues(array($c['country_id']),"cty_",$c);
			}
		}

		/* try to add / update company profile */
		$result = $this->GetProfile()->DoAddUpdate($c,$response,$bRedirect = false,$bApproved,$bTX = TRUE);
		if($result) {

			Logger::DB(2,get_class($this)."::".__FUNCTION__."()","OK id: ".$response['id']);

			if ($this->GetMode() == self::MODE_ADD) {
				$this->SetCompanyId($response['id']);
				$this->SetCompanyUrlName($response['url_name']);

				return TRUE;
			}

			if ($this->GetMode() == self::MODE_EDIT) {

				// setup user notification success msg
				$oMessage = new Message(MESSAGE_TYPE_SUCCESS, MESSAGE_ID_ADD_COMPANY, "SUCCESS - updated profile");
				$oSession->GetMVCController()->GetCurrentRoute()->SetMessage($oMessage);
				$oSession->Save();

				if ($response['url_change'] == TRUE) {
					Http::Redirect($response['edit_url']);
				}
 			}

			$this->SetValid();

			return TRUE;

		} else { // something went wrong during the update

			Logger::DB(1,get_class($this)."::".__FUNCTION__."()","FAIL: ".serialize($response));

			$this->SetInValid();
			$this->ProcessValidationErrors($response['msg']);
			$this->SetValidationErrors($response);

			return FALSE;
		}

	}

	private function SetDefaultMappingFormValues($aDefaultId, $sPrefix, &$aFormValuesArray) {
		if (!is_array($aDefaultId)) return FALSE;
		foreach($aDefaultId as $id) {
			$aFormValuesArray[$sPrefix.$id] = 'on';
		}
	}


	private function BundleProfileVersionFields($aFormValues) {

		$aProfileVersionData = array();
		// look for <<prefix>>_<<site/version-id>>_<<fieldname>> eg PV_0_title
		foreach($aFormValues as $k => $v) {
			if (preg_match("/^PV::/",$k)) {
				$a = explode("::",$k);
				//if (strlen(trim($v)) > 1) {
					$aProfileVersionData[$a[1]][$a[2]] = $v;
				//}
			}
		}

		return $aProfileVersionData;
	}

	/*
	 * Write account details (from registration step) to DB
	 *
	 */
	protected function AddAccount() {

		global $oSession, $_CONFIG, $oBrand;

		$oAccount = new AccountApplication();

		$aValidationErrors = array();
		$aFormValues = $oSession->GetMVCController()->GetRouteByName('Registration')->GetFormValues();
		$aFormValues['company_id'] = $this->GetCompanyID();


		if ($oAccount->Add($aFormValues,$aValidationErrors)) {

			// send admin a notification email
			$aMessageParams = array();
			$aMessageParams['to'] = $_CONFIG['admin_email'];
			$aMessageParams['from']  = $_CONFIG['website_email'];
			$aMessageParams['reply-to'] = $_CONFIG['website_email'];
			$aMessageParams['website_name'] = $oBrand->GetName();
			$aMessageParams['website_url'] = $oBrand->GetWebsiteUrl();
			$aMessageParams['name'] = $aFormValues['name'];
			$aMessageParams['role'] = $aFormValues['role'];
			$aMessageParams['email'] = $aFormValues['email'];
			$aMessageParams['telephone']  = $aFormValues['tel'];
			$aMessageParams['comments']  = $aFormValues['comments'];
			$aMessageParams['company_name'] = $_POST['title'];
			$aMessageParams['company_url_name'] = $this->GetCompanyUrlName();
			$aMessageParams['company_status'] = ($oSession->GetListingType() == LISTING_REQUEST_NEW) ? 0 : 1;

			Logger::DB(2,get_class($this)."::".__FUNCTION__."()","ADD_ACT OK: ".serialize($aFormValues));

			$oAccount->NotifyAdmin($aMessageParams);

			$this->SetComplete();
			$oSession->Save();

			return TRUE;
		} else {

			Logger::DB(1,get_class($this)."::".__FUNCTION__."()","ADD_ACT FAIL: ".serialize($aFormValues));

			$this->ProcessValidationErrors($aValidationErrors['msg'], $clear_existing = TRUE);
			return FALSE;
		}

	}


	protected function GetCategoryList() {

		global $db, $oAuth,$_CONFIG;

		$oCategory = new Category($db);

		$aResult = Mapping::GetFromRequest($_REQUEST); /* extract selected cat/act/cty mappings from $_REQUEST */

		if (is_array($this->GetProfile()->category_array)) {
			$aSelected = $this->GetProfile()->category_array;
			if (!$oAuth->oUser->isAdmin) {
				// reduce number selected based on categories displayed on this site only
				$aCatVisible = $oCategory->GetCategoriesByWebsite($_CONFIG['site_id']);
				$aCatId = array_keys($aCatVisible);
				$aSelected = array_intersect($aSelected, $aCatId);
			}
		} elseif (count($aResult['cat']) >= 1) {
			$aSelected = $aResult['cat'];
		} else {
			$aSelected = $this->SetDefaultCategoryId();
		}

		$this->GetForm()->Set('CATEGORY_LIST_SELECTED_COUNT',count($aSelected));
		$all = ($oAuth->oUser->isAdmin) ? TRUE : FALSE;
		return $oCategory->GetCategoryLinkList("input",$aSelected,$delimiter = FALSE,$all);

	}

	/* set default selected categories according to brand / selected profile type */
	protected function SetDefaultCategoryId() {
		global $oBrand;

		$aSelected = array();
		foreach($oBrand->GetDefaultCategoryId() as $id) {
			$aSelected[] = $id;
		}

		return array_merge($aSelected,$this->SetDefaultCategoryByProfileType());
	}

	/* set default selected activities according to  brand / selected profile type */
	protected function SetDefaultActivityId() {
		global $oBrand;
		$aSelected = array();
		foreach($oBrand->GetDefaultActivityId() as $id) {
			$aSelected[] = $id;
		}

		return array_merge($aSelected, $this->SetDefaultActivityByProfileType());
	}

	/* set default selected activities according to  brand / selected profile type */
	protected function SetDefaultCountryId() {
		global $oBrand;
		$aSelected = array();
		foreach($oBrand->GetDefaultCountryId() as $id) {
			$aSelected[] = $id;
		}

		return array_merge($aSelected,$this->SetDefaultCountryByProfileType());
	}

	private function SetDefaultCategoryByProfileType() {

		global $_CONFIG;

		$aSelected = array();

		switch($this->GetProfile()->GetProfileType()) {
			case PROFILE_SUMMERCAMP :
				foreach($_CONFIG['profile_category_defaults'][PROFILE_SUMMERCAMP] as $id) {
					$aSelected[] = $id;
				}
				break;
			default:
		}

		return $aSelected;
	}

	private function SetDefaultActivityByProfileType() {

		global $_CONFIG;

		$aSelected = array();

		switch($this->GetProfile()->GetProfileType()) {
			case PROFILE_SUMMERCAMP :
				foreach($_CONFIG['profile_activity_defaults'][PROFILE_SUMMERCAMP] as $id) {
					$aSelected[] = $id;
				}
				break;
			default:
		}

		return $aSelected;

	}

	private function SetDefaultCountryByProfileType() {

		global $_CONFIG;

		$aSelected = array();

		switch($this->GetProfile()->GetProfileType()) {
			case PROFILE_SUMMERCAMP :
				foreach($_CONFIG['profile_country_defaults'][PROFILE_SUMMERCAMP] as $id) {
					$aSelected[] = $id;
				}
				break;
			default:
		}

		return $aSelected;
	}

	public function SetCompanyId($id) {
		if (is_numeric($id)) {
			$this->company_id = $id;
		}
	}

	public function GetCompanyId() {
		return $this->company_id;
	}

	public function GetCompanyUrlName() {
		return $this->company_url_name;
	}

	public function SetCompanyUrlName($company_url_name) {
		$this->company_url_name = $company_url_name;
	}

	protected function SetCompanyProfile($oProfile) {
		$this->oCompanyProfile = $oProfile;
	}

	protected function GetProfile() {
		return $this->oCompanyProfile;
	}

	protected function GetForm() {
		return $this->oCompanyForm;
	}

	protected function SetCompanyForm($oTemplate) {

		$this->oCompanyForm = $oTemplate;

	}

	protected function GetGeneralProfileForm() {
		return $this->oGeneralProfileForm;
	}

	protected function SetGeneralProfileForm($oTemplate) {
		$this->oGeneralProfileForm = $oTemplate;
	}

	protected function GetSummerCampForm() {
		return $this->oSummerCampForm;
	}

	protected function SetSummerCampForm($oTemplate) {
		$this->oSummerCampForm = $oTemplate;
	}

	protected function GetSeasonalJobsForm() {
		return $this->oSeasonalJobsForm;
	}

	protected function SetSeasonalJobsForm($oTemplate) {
		$this->oSeasonalJobsForm = $oTemplate;
	}

	protected function GetVolunteerProjectForm() {
		return $this->oVolunteerProjectForm;
	}

	protected function SetVolunteerProjectForm($oTemplate) {
		$this->oVolunteerProjectForm = $oTemplate;
	}

	protected function GetTeachingProjectForm() {
		return $this->oTeachingProjectForm;
	}

	protected function SetTeachingProjectForm($oTemplate) {
		$this->oTeachingProjectForm = $oTemplate;
	}


	private function SetTabbedPanel() {

		global $oHeader, $oAuth;

		// some of the templates below require company profile object to be in scope
		$oProfile = $this->GetProfile();


		ob_start();
			$contents_edit = $this->GetForm()->Render();
	    ob_end_clean();
	    if (($this->GetMode() == self::MODE_EDIT) && ($this->GetProfile()->GetListingType() >= BASIC_LISTING)) {
			ob_start();
		       require_once("./templates/profile_logo.php");
		   	   $contents_logo = ob_get_contents();
		    ob_end_clean();
		    ob_start();
		       require_once("./templates/profile_images.php");
		   	   $contents_images = ob_get_contents();
		    ob_end_clean();
			ob_start();
		       require_once("./templates/profile_video.php");
		   	   $contents_video = ob_get_contents();
		    ob_end_clean();
	    }

		$cols = 5;
		$oTabbedPanel = new TabbedPanel();

		$tab_panel_id = ($this->GetMode() == self::MODE_ADD) ? "TP07" : "TP08";

		$oTabbedPanel->SetId($tab_panel_id);
		$oTabbedPanel->SetCols($cols);
		$oTabbedPanel->SetCookieName(COOKIE_TAB_NAME);
		$oTabbedPanel->LoadFromXmlFile(ROOT_PATH."/conf/tabbed_panels.xml");

		if ($this->GetMode() == self::MODE_ADD) {
			if(is_object($oAuth->oUser)) {
				$oTabbedPanel->SetTitle("New Listing");
			} else { // part of a new registration step through
				$oTabbedPanel->SetTitle("New Listing - Step 2 of 3");
			}
		} else {
			$oTabbedPanel->SetTitle($this->GetProfile()->GetTitle()." : Edit Profile");
		}

		$sFormOpenTag = "<form enctype=\"multipart/form-data\" name=\"AddEditPlacementForm\" id=\"AddEditPlacementForm\" action=\"".Request::GetUri()."\" method=\"POST\">";
		$oTabbedPanel->SetContentFromHtml($sFormOpenTag);

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
		$oLayout->SetContent($contents_edit);
		$oLayout->LoadTemplate("column.php");
		$oTabbedPanel->SetContentFromObject($oLayout);


	    //if (($this->GetMode() == self::MODE_EDIT) && (($oProfile->GetListingType() >= BASIC_LISTING) || $oAuth->oUser->isAdmin)) {

	    if (($this->GetMode() == self::MODE_EDIT) && ($this->GetProfile()->GetListingType() >= BASIC_LISTING)) {

			/* edit logo */
			$oLayout = new Layout();
			$oLayout->SetId("TC02");
			$oLayout->SetCols(5);
			$oLayout->SetContent($contents_logo);
			$oLayout->LoadTemplate("column.php");
			$oTabbedPanel->SetContentFromObject($oLayout);

			/* edit images */
			$oLayout = new Layout();
			$oLayout->SetId("TC03");
			$oLayout->SetCols(5);
			$oLayout->SetContent($contents_images);
			$oLayout->LoadTemplate("column.php");
			$oTabbedPanel->SetContentFromObject($oLayout);

			/* edit youtube video */
			$oLayout = new Layout();
			$oLayout->SetId("TC04");
			$oLayout->SetCols(5);
			$oLayout->SetContent($contents_video);
			$oLayout->LoadTemplate("column.php");
			$oTabbedPanel->SetContentFromObject($oLayout);

	    } else {
			$oTabbedPanel->DeleteTabById("TAB02");
	    	$oTabbedPanel->DeleteTabById("TAB03");
	    	$oTabbedPanel->DeleteTabById("TAB04");

	    }




		/* if the page was submitted, set images tab active if this was being viewed */
		if (isset($_COOKIE[COOKIE_TAB_NAME])) {
		 	$oTabbedPanel->SetActiveTabById(strtoupper($_COOKIE[COOKIE_TAB_NAME]));
		}


		$oTabbedPanel->LoadTemplate("tabbed_panel.php");

		// inject JQuery onlick code into page header
		$oTabbedPanel->LoadTemplate("tabbed_panel.php");
		$oHeader->SetJsOnload($oTabbedPanel->GetEventJQuery());
		$oHeader->Reload();

		$this->oTabbedPanel = $oTabbedPanel;

	}

	private function DeleteProfile() {

		global $oSession, $db;

		// get company profile
		$this->GetProfileFromDb( $this->GetCompanyId() );

		$oPlacementProfile = new PlacementProfile();
		$aPlacements = $oPlacementProfile->GetProfileById($this->GetProfile()->GetId(),"COMPANY_ID",$return = "ARRAY");

		$aId = array();

		if (is_array($aPlacements)) {
			foreach($aPlacements as $o) {
				$aId[] = (int) $o->id;
			}
			unset($aPlacements);
		}

		$oArchiveManager = new ArchiveManager;

		// delete placements
		foreach($aId as $id) {
			$result = $oArchiveManager->ArchivePlacement($id);
			if (!$result) die();
		}

		// delete company profile
		$result = $oArchiveManager->ArchiveCompany($this->GetProfile()->GetId());


		if ($result) {
			$message .= "<p>SUCCESS: deleted company ".$this->GetProfile()->GetTitle()."</p>";
			$oMessage = new Message(MESSAGE_TYPE_SUCCESS, MESSAGE_ID_DELETE_PLACEMENT, $message);
		} else {
			$message .= "<p>ERROR: it was not possible to delete company ".$this->GetProfile()->GetTitle()."</p>";
			$oMessage = new Message(MESSAGE_TYPE_ERROR, MESSAGE_ID_DELETE_PLACEMENT, $message);
		}

		$oDashboardController = $oSession->GetMVCController()->GetRouteByUriMapping("/".ROUTE_DASHBOARD);
		$oDashboardController->UnsetMessage();
		$oDashboardController->SetMessage($oMessage);
		$oSession->Save();

		Http::Redirect("/".ROUTE_DASHBOARD);


	}



	private function GetTabbedPanel() {
		return $this->oTabbedPanel;
	}


	private function Clear() {

		unset($this->oCompanyForm);
		unset($this->oGeneralProfileForm);
		unset($this->oSummerCampForm);
		unset($this->oSeasonalJobsForm);
		unset($this->oVolunteerProjectForm);
		unset($this->oTeachingProjectForm);
		unset($this->oTabbedPanel);
		unset($this->oCompanyProfile);

	}

}


?>
