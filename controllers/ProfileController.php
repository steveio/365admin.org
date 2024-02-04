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

		/* file upload params */
		//$max_size = 1024000;
		$max_size = IMAGE_MAX_UPLOAD_SIZE;
		$max_uploads  = 4;
		$path = ROOT_PATH_IMAGE_UPLOAD.'/upload/images/';
		

		if (isset($_REQUEST['do_file_upload']) || isset($_REQUEST['do_logo_upload'])) {
		
			if (DEBUG) Logger::Msg("Upload: Begin...");
		
			/* retrieve uploaded file from correct field depending on whether we upload a logo or an image */
			$file_field = (isset($_REQUEST['do_logo_upload'])) ? "logo" : "file";
			
			if (count($_FILES[$file_field]['name'])<=$max_uploads) {
				if (DEBUG) Logger::Msg("Upload: Multiple...");
				
				$upload = new File_upload();
				$upload->allow('images');
				$upload->set_path($path);
				$upload->set_max_size($max_size);		
				
				
				$aResult = $upload->upload_multiple($_FILES[$file_field]);
				
				if ($upload->is_error()) {
					$aError = array();
					$aError['msg']['img_upload'] = $upload->get_error();
				}
				
				if (!is_array($aError) && is_array($aResult['TMP_PATH']) && (count($aResult['TMP_PATH']) >= 1)) {

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

							$aError = array();
							$aError['msg']['img_upload'] = "Error: Invalid logo size.  Permitted sizes (in pixels) width: ".LOGO__DIMENSIONS_MINWIDTH."-".LOGO__DIMENSIONS_MAXWIDTH."px, height: ".LOGO__DIMENSIONS_MINHEIGHT."-".LOGO__DIMENSIONS_MAXHEIGHT."px.";
											
						} else {
														
							/* process the new logo */
							$oIP->Process($aResult['TMP_PATH'],$link_to,$link_id,$iImgType = LOGO_IMAGE);
									
							/* delete existing logo, but not the one we just uploaded */
							if (DEBUG) Logger::Msg("Checking for existing logo...");
							if ($link_to == "COMPANY") {
								$tmp = new CompanyProfile();
							} elseif ($link_to == "PLACEMENT") {
								$tmp = new PlacementProfile();
							}
							$tmp->SetId($id);
							$aExistingLogo = $tmp->GetImages(LOGO_IMAGE);
							unset($tmp);
							if (is_array($aExistingLogo) && count($aExistingLogo) >= 1) {
								if (DEBUG) Logger::Msg("Delete ".count($aExistingLogo)." Existing Logo...");
								
								foreach($aExistingLogo as $oLogoImage) {
									if (!in_array($oLogoImage->GetId(), $oIP->GetProcessedIds())) {
										$oLogoImage->Delete();
									}
								}
							}
							
						}
						
					}
				}
				
			} else {  
				$aError = array();
				$aError['msg']['img_upload'] = 'Trying to upload to many files';
			}   
			
			
			if (is_array($aError) && count($aError['msg']) >= 1) {
				$this->ProcessValidationErrors($aError['msg']);
			} else {
				$plural = (count($aResult['FILENAME']) > 1) ? "s" : "";
				$message = "SUCCESS : uploaded ".count($aResult['FILENAME']) ." file".$plural."<br/>".implode("<br />",$aResult['FILENAME']);
				$oMessage = new Message(MESSAGE_TYPE_SUCCESS, 'img_upload', $message);
				$this->SetUserMessage($oMessage); 
			}	
		}
		
	}
	
	
	
} 


?>
