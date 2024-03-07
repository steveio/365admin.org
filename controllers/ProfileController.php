<?php

/*
 * ProfileController
 * Functionality common to both Company and Placement routes
 * for example upload image / video
 * 
 */

class ProfileController extends GenericController {
	
	protected function __construct( ){
		
		parent::__construct();
		
	}

	public function CheckDomain()
	{
	    // NGINX reverse proxy does currently not set hostname in $_SERVER
	    // if (Request::GetHostName($subdomain = true) != ADMIN_SYSTEM_HOSTNAME)
	    if (CURRENT_SITE != ADMIN_SYSTEM)
	    {
	        Http::Redirect(ADMIN_SYSTEM.Request::GetUri("STRING"));
	    }
	}

	public function SaveYouTubeVideo($link_to, $link_id, $video_str) {
		
		//if (strlen(trim($video_str)) < 1) return FALSE; 
		
		if (!is_numeric($link_id)) return FALSE;
		
		global $db, $_CONFIG;
		
		$video_str = Validation::AddSlashes(trim($video_str));
		
		if ($link_to == PROFILE_COMPANY) {
			$sql = "UPDATE ".$_CONFIG['company_table']." SET video = '".$video_str."' WHERE id = ".$link_id.";";
		}
		
		if ($link_to == PROFILE_PLACEMENT) {
			$sql = "UPDATE ".$_CONFIG['placement_table']." SET video1 = '".$video_str."' WHERE id = ".$link_id.";";
		}
				
		$db->query($sql);
		
		if ($db->getAffectedRows() == 1) return TRUE;
		
		
	}
	
	/*
	 * Handle an image upload request 
	 * 
	 * @param string link_to { COMPANY || PLACEMENT }
	 * 
	 */
	public function DoImageUpload($link_to, $link_id) {

	    $aError = array();

		/* file upload params */
		//$max_size = 1024000;
		$max_size = IMAGE_MAX_UPLOAD_SIZE;
		$max_uploads  = 4;
		$path = ROOT_PATH_IMAGE_UPLOAD.'/upload/images/';
		

		if (isset($_REQUEST['do_file_upload']) || isset($_REQUEST['do_logo_upload']) || isset($_REQUEST['do_promo_upload'])) {
		
			/* retrieve uploaded file from correct field depending on whether we upload a logo or an image */
		    if (isset($_REQUEST['do_logo_upload']))
		    {
		        $file_field = "logo";
		    } elseif ($_REQUEST['do_promo_upload'])
		    {
		        $file_field = "promo";
		    } else {
		        $file_field = "file";
		    }
			
			if (count($_FILES[$file_field]['name'])<=$max_uploads) {
				
				$upload = new File_upload();
				$upload->allow('images');
				$upload->set_path($path);
				$upload->set_max_size($max_size);		

				$aResult = $upload->upload_multiple($_FILES[$file_field]);
				
				if ($upload->is_error()) {
					$aError['msg']['file_upload'] = $upload->get_error();
				}
				
				if (!is_array($aError['msg']) && is_array($aResult['TMP_PATH']) && (count($aResult['TMP_PATH']) >= 1)) {

					/* Now call ImageProcessor to generate proxy images */
					if (isset($_REQUEST['do_file_upload'])) {

						/* process profile images */
						$oIP = new ImageProcessor_FileUpload;						
						$oIP->Process($aResult['TMP_PATH'],$link_to,$link_id,$iImgType = PROFILE_IMAGE);

					} elseif (isset($_REQUEST['do_logo_upload'])) {

						/* process logo */
						$oIP = new ImageProcessor_FileUpload;		
						$oIP->SetResizeProfile(LOGO_IMAGE);

						$aImageDetails = $oIP->Identify($aResult['TMP_PATH'][0]);
						
						/* logo size boundaries */						
						if (($aImageDetails['width'] > LOGO__DIMENSIONS_MAXWIDTH) ||
							($aImageDetails['width'] < LOGO__DIMENSIONS_MINWIDTH) ||
							($aImageDetails['height'] > LOGO__DIMENSIONS_MAXHEIGHT) ||
							($aImageDetails['height'] < LOGO__DIMENSIONS_MINHEIGHT)
							)
						{
							/* wrong size image, delete uploaded file, throw an error */
							unset($aResult['TMP_PATH'][0]);

							$aError['msg']['img_filesize'] = "ERROR: Invalid size.  Minimum size: width: ".LOGO__DIMENSIONS_MINWIDTH."px / height: ".LOGO__DIMENSIONS_MINHEIGHT."px";

						} else {
			
							/* process the new logo */
							$oIP->Process($aResult['TMP_PATH'],$link_to,$link_id,$iImgType = LOGO_IMAGE);
									
							/* delete existing (old) logo */
							if ($link_to == "COMPANY") {
								$aProfile = new CompanyProfile();
							} elseif ($link_to == "PLACEMENT") {
							    $aProfile = new PlacementProfile();
							}
							$aProfile->SetId($link_id);
							$aExistingLogo = $aProfile->GetImages(LOGO_IMAGE);
							unset($aProfile);
							if (is_array($aExistingLogo) && count($aExistingLogo) >= 1) {								
								foreach($aExistingLogo as $oLogoImage) {
									if (!in_array($oLogoImage->GetId(), $oIP->GetProcessedIds())) {
										$oLogoImage->Delete();
									}
								}
							}
							
						}
					} elseif (isset($_REQUEST['do_promo_upload'])) {

					    /* process promo image */
					    $oIP = new ImageProcessor_FileUpload;
					    $oIP->SetResizeProfile(PROMO_IMAGE);
					    
					    $aImageDetails = $oIP->Identify($aResult['TMP_PATH'][0]);					    
					    
					    /* logo size boundaries */
					    if (($aImageDetails['width'] > PROMO__DIMENSIONS_MAXWIDTH) ||
					        ($aImageDetails['width'] < PROMO__DIMENSIONS_MINWIDTH) ||
					        ($aImageDetails['height'] > PROMO__DIMENSIONS_MAXHEIGHT) ||
					        ($aImageDetails['height'] < PROMO__DIMENSIONS_MINHEIGHT)
					        )
					    {
					        /* wrong size image, delete uploaded file, throw an error */
					        unset($aResult['TMP_PATH'][0]);

					        $aError['msg']['img_filesize'] = "ERROR: Invalid size.  Minimum size: width: ".PROMO__DIMENSIONS_MINWIDTH."px / height: ".PROMO__DIMENSIONS_MINHEIGHT."px";

					    } else {
					        
					        /* process the new logo */
					        $oIP->Process($aResult['TMP_PATH'],$link_to,$link_id,$iImgType = PROMO_IMAGE);
					        
					        /* delete existing (old) logo */
					        if ($link_to == "COMPANY") {
					            $aProfile = new CompanyProfile();
					        } elseif ($link_to == "PLACEMENT") {
					            $aProfile = new PlacementProfile();
					        }
					        $aProfile->SetId($link_id);
					        $aExistingImg = $aProfile->GetImages(PROMO_IMAGE);
					        unset($aProfile);
					        if (is_array($aExistingImg) && count($aExistingImg) >= 1) {
					            foreach($aExistingImg as $oImg) {
					                if (!in_array($oImg->GetId(), $oIP->GetProcessedIds())) {
					                    $oImg->Delete();
					                }
					            }
					        }
					        
					    }

					}
				}
				
			} else {  
				$aError['msg']['img_upload'] = 'Trying to upload to many files';
			}   
			
			if (is_array($aError['msg']) && count($aError['msg']) >= 1) {
			    $oMessage = new Message(MESSAGE_TYPE_WARNING, 'img_upload', implode("<br/>",$aError['msg']));
			    $this->SetMessage($oMessage);
			} else {
				$plural = (count($aResult['FILENAME']) > 1) ? "s" : "";
				$message = "SUCCESS : uploaded ".count($aResult['FILENAME']) ." file".$plural."<br/>".implode("<br />",$aResult['FILENAME']);
				$oMessage = new Message(MESSAGE_TYPE_SUCCESS, 'img_upload', $message);
				$this->SetMessage($oMessage); 
			}	
		}
		
	}
	
	
	protected function GetCategoryList() {
	    
	    global $db;
	    
	    $oCategory = new Category($db);
	    
	    $aResult = Mapping::GetFromRequest($_REQUEST); /* extract selected cat/act/cty mappings from $_REQUEST */
	    
	    $aSelected = array();
	    
	    if (is_array($this->GetProfile()->category_array)) {
	        $aSelected = $this->GetProfile()->category_array;
	    } elseif (count($aResult['cat']) >= 1) {
	        $aSelected = $aResult['cat'];
	    } else {
	        //$aSelected = $this->SetDefaultCategoryId();
	    }
	    
	    $this->GetForm()->Set('CATEGORY_LIST_SELECTED_COUNT',count($aSelected));
	    return $oCategory->GetCategoryLinkList("input",$aSelected,$delimiter = FALSE);
	    
	}
	
	protected function GetActivityList() {
	    
	    global $db;
	    
	    $oActivity = new Activity($db);
	    
	    $aResult = Mapping::GetFromRequest($_REQUEST); /* extract selected cat/act/cty mappings from $_REQUEST */
	    
	    $aSelected = array();
	    
	    if (is_array($this->GetProfile()->activity_array)) {
	        $aSelected = $this->GetProfile()->activity_array;
	    } elseif (count($aResult['act']) >= 1) {
	        $aSelected = $aResult['act'];
	    } else {
	        //$aSelected = $this->SetDefaultActivityId();
	    }
	    $this->GetForm()->Set('ACTIVITY_LIST_SELECTED_COUNT',count($aSelected));
	    return $oActivity->GetActivityLinkList("input",$aSelected,$delimiter = FALSE,$all = FALSE, $return = "ARRAY");
	    
	}
	
	
	protected function GetCountryList() {
	    
	    global $db;
	    
	    $oCountry = new Country($db);
	    
	    $aResult = Mapping::GetFromRequest($_REQUEST); /* extract selected cat/act/cty mappings from $_REQUEST */
	    
	    $aSelected = array();
	    
	    if (is_array($this->GetProfile()->country_array)) {
	        $aSelected = $this->GetProfile()->country_array;
	    } elseif (count($aResult['cty']) >= 1) {
	        $aSelected = $aResult['cty'];
	    } else {
	        //$aSelected = $this->SetDefaultCountryId();
	    }
	    
	    $this->GetForm()->Set('COUNTRY_LIST_SELECTED_COUNT',count($aSelected));
	    return $oCountry->GetCountryLinkList("input",$aSelected,$delimiter = FALSE, $return = "ARRAY");
	    
	}

} 


?>
