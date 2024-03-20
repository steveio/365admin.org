<?php


class Validation {

	public static function ValidUriNamespaceIdentifier($str) {
        if (!preg_match('/[a-zA-Z0-9_\-\/]{1,120}/',$str)) {
			return FALSE;
        }
        return TRUE;
	}

	public static function SanitizeAlphaNumeric(&$input) {
		if (is_array($input)) {
			foreach($input as $k => $v) {
				if (is_string($v)) {
					$input[$k] = preg_replace("^[a-zA-Z0-9 ]$"," ",$v);
				}
			}
		} elseif (is_string($input)) {
			$input = pre_replace("^[a-zA-Z0-9 ]$"," ",$input);
		}

	}

	public static function Sanitize(&$input) {

		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."()");

		if (is_array($input)) {
			foreach($input as $k => $v) {
				if (is_string($v)) {
					$input[$k] = htmlspecialchars($v,ENT_NOQUOTES,"UTF-8");
				}
			}
		} elseif (is_string($input)) {
			$input = htmlspecialchars($input,ENT_NOQUOTES,"UTF-8");
		}

	}

	public static function AddSlashes(&$input,$db_escape = true) {

		if (is_array($input)) {
			foreach($input as $k => $v) {
				if (is_string($v)) {
					$input[$k] = addslashes($v);
                    if ($db_escape)
                    	$input[$k] = pg_escape_string($input[$k]);
				}
			}
		} elseif (is_string($input)) {
			$input = addslashes($input);
		}

		return $input;
	}

	public static function IsValidDate($sStr) {
		if (preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}/",$sStr)) {
			return true;
		}
	}

	public static function ValidatePlacement($p,&$aResponse) {

		global $db,$oAuth, $oSession;

		$aResponse['msg'] = array();

        if (strlen($p['title']) < 1) $aResponse['msg']['title'] = "Please enter a title.";
        if (strlen($p['title']) > 119) $aResponse['msg']['title'] = "Title should be short - 119 characters or less.";


		if (($p['company_id'] == "select") || (!is_numeric($p['company_id']))) {
			$aResponse['msg']['company'] = "You must select a valid <b>company</b>.";
		}

		if (strlen($p['desc_short']) < 1) $aResponse['msg']['desc_short'] = "Please enter a short description (2000 chars or less).</b>.";
        if (strlen($p['desc_short']) > 2000) $aResponse['msg']['desc_short'] = "Short Description should be brief - a single paragraph, 2000 characters or less.";

        if (strlen($p['desc_long']) < 1) $aResponse['msg']['desc_long'] = "Full Description must be supplied.";
		if (strlen($p['desc_long']) > 20000) $aResponse['msg']['desc_long'] = "Full Description should be less than 20000chars.";

        if (strlen($p['location']) > 199) {
        	$aResponse['msg']['location'] = "Location/Region must be less than 199 chars.";
        }

        if ((strlen($p['apply_url']) < 1) && (strlen($p['email']) < 1))  {
                $aResponse['msg']['email'] = "Please enter either an enquiry email address or an apply/booking URL.";
        }

        if (strlen($p['apply_url']) > 255) {
        	$aResponse['msg']['apply_url'] = "Apply URL must be less than 256 chars.  Use a url shortening service eg tinyurl.com, bitly.com or goo.gl to shorten long urls";
        }

        if (strlen($p['email']) > 119) {
        	$aResponse['msg']['email'] = "Enquiry / Apply email must be less than 119 chars.";
        }

        if (strlen($p['keyword_exclude']) > 255) {
        	$aResponse['msg']['keyword_exclude'] = "Keyword exclude must be less than 256 chars.";
        }


		$iCat = 0;
		$iAct = 0;
		$iCty = 0;
		foreach($p as $k => $v) {
			if ((preg_match("/cat_/",$k)) && ($v == "on")) $iCat++;
			if ((preg_match("/act_/",$k)) && ($v == "on")) $iAct++;
			if ((preg_match("/cty_/",$k)) && ($v == "on")) $iCty++;
		}

		if ($iCat == 0) $aResponse['msg']['category'] = "Select at least one category.";
		if ($iAct == 0) $aResponse['msg']['activity'] = "Select at least one activity.";
		if ($iCty == 0) $aResponse['msg']['country'] = "Select at least one country.";

		//if (strlen($p['email']) < 1) $aResponse['msg']['email'] = "Please enter an enquiry / sales email.</b>.";
		//if (strlen($p['url']) < 1) $aResponse['msg']['url'] = "Please enter a more info / apply / bookings url.</b>.";


		/*
		 * if admin is adding a placement
		 * check that the selected profile type
		 * is enabled on the company profile
		 *
		 */
		if ($oAuth->oUser->isAdmin) {
			$oCProfile = new CompanyProfile();
			$oCProfile->SetId($p['company_id']);
			$oCProfile->GetProfileOptionBitmapFromDB();
			if (!$oCProfile->HasProfileOption($p['profile_type'])) {
				$aResponse['msg']['profile_type'] = "This type of profile is not enabled on company profile.";
			}
		}

		if ($p['profile_type'] == PROFILE_VOLUNTEER) {
			if (($p[PROFILE_FIELD_PLACEMENT_DURATION_FROM] == "null") || ($p[PROFILE_FIELD_PLACEMENT_DURATION_TO] == "null")){
				$aResponse['msg'][PROFILE_FIELD_PLACEMENT_DURATION_LABEL] = "Please specify placement duration from/to.";
			}
			if (($p[PROFILE_FIELD_PLACEMENT_PRICE_FROM] == "null") ||
				($p[PROFILE_FIELD_PLACEMENT_PRICE_TO] == "null") ||
				($p[PROFILE_FIELD_PLACEMENT_CURRENCY] == "null")
				) {
				$aResponse['msg'][PROFILE_FIELD_PLACEMENT_PRICE_LABEL] = "Please specify approx costs from/to and currency.";
			}

		}

		if ($p['profile_type'] == PROFILE_TOUR) {

			if (strlen($p[PROFILE_FIELD_PLACEMENT_TOUR_CODE]) > 29) $aResponse['msg'][PROFILE_FIELD_PLACEMENT_TOUR_CODE] = "Tour code must be less than 30 characters.";

			if (strlen($p[PROFILE_FIELD_PLACEMENT_START_DATES]) > 512) $aResponse['msg'][PROFILE_FIELD_PLACEMENT_START_DATES] = "Start dates must be less than 512 characters.";

			if (strlen($p[PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS]) > 512) $aResponse['msg'][PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS] = "Requirements must be less than 512 characters.";

			if (($p[PROFILE_FIELD_PLACEMENT_TOUR_DURATION_FROM] == "null") || ($p[PROFILE_FIELD_PLACEMENT_TOUR_DURATION_TO] == "null")){
				$aResponse['msg'][PROFILE_FIELD_PLACEMENT_TOUR_DURATION_LABEL] = "Please specify tour duration from/to.";
			}

			if (($p[PROFILE_FIELD_PLACEMENT_TOUR_PRICE_FROM] == "null") ||
				($p[PROFILE_FIELD_PLACEMENT_TOUR_PRICE_TO] == "null") ||
				($p[PROFILE_FIELD_PLACEMENT_TOUR_CURRENCY] == "null")
				) {
				$aResponse['msg'][PROFILE_FIELD_PLACEMENT_TOUR_DURATION_LABEL] = "Please specify approx costs from/to and currency.";
			}

		}


		if ($p['profile_type'] == PROFILE_JOB) {

			/*
			if (($p['StartDateDay'] == "null") ||
				($p['StartDateMonth'] == "null") ||
				($p['StartDateYear'] == "null")) {
				$aResponse['msg']['job_start_date'] = "Please specify job start date.";
			}

			if (($p['CloseDateDay'] == "null") ||
				($p['CloseDateMonth'] == "null") ||
				($p['CloseDateYear'] == "null")) {
				$aResponse['msg']['close_date'] = "Please specify job application closing date.";
			}
			*/

			if (strlen($p[PROFILE_FIELD_PLACEMENT_JOB_REFERENCE]) > 29) {
				$aResponse['msg'][PROFILE_FIELD_PLACEMENT_JOB_REFERENCE] = "Job Reference must be less than 30chars.";
			}

			if (strlen($p[PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE]) > 2000) {
				$aResponse['msg'][PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE] = "Job Start dates must be less than 2000 chars.";
			}

			if (($p[PROFILE_FIELD_PLACEMENT_JOB_DURATION_FROM] == "null") || ($p[PROFILE_FIELD_PLACEMENT_JOB_DURATION_TO] == "null")) {
				$aResponse['msg'][PROFILE_FIELD_PLACEMENT_JOB_DURATION_LABEL] = "Please specify job duration from/to.";
			}

			if (strlen($p[PROFILE_FIELD_PLACEMENT_JOB_SALARY]) < 1) {
				$aResponse['msg'][PROFILE_FIELD_PLACEMENT_JOB_SALARY] = "Please specify job salary / pay.";
			}

			if (strlen($p[PROFILE_FIELD_PLACEMENT_JOB_SALARY]) > 2000) {
				$aResponse['msg'][PROFILE_FIELD_PLACEMENT_JOB_SALARY] = "Job salary must be less than 2000 chars.";
			}

			if (strlen($p[PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE]) > 2000) {
				$aResponse['msg'][PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE] = "Experience must be less than 2000 chars.";
			}

			/*
			if ($p['contract_type'] == "") {
				$aResponse['msg']['contract_type'] = "Please specify contract type.";
			}
			*/

		}



        /* validate contact email / info url */
        // @note - disabled 13/12/2008 pending bugfix
        /*
        if (strlen($p['email']) > 1) {
    		if (!Validation::IsValidEmail($p['email'])) {
				$aResponse['msg'] = "ERROR: Contact email does not appear to be valid.";
              	return false;
    		}
        }
        if (strlen($p['img_url1']) > 1) {
        	if (!Validation::IsValidRemoteImage($p['img_url1'],"Photo Url 1",$aResponse)) {
        		return false;
        	}
        }
        if (strlen($p['img_url2']) > 1) {
        	if (!Validation::IsValidRemoteImage($p['img_url2'],"Photo Url 2",$aResponse)) {
        		return false;
        	}
        }
		if (strlen($p['img_url3']) > 1) {
        	if (!Validation::IsValidRemoteImage($p['img_url3'],"Photo Url 3",$aResponse)) {
        		return false;
        	}
        }
		*/

		if (count($aResponse['msg']) >= 1) return false;

		return true;
	}

	public static function IsValidRemoteImage($sUrl,$sFieldName,&$aResponse) {
		/* removed pending addition of a robust regex for url checking */
		//if (!Validation::IsValidUrl($sUrl)) {
        //        $aResponse['msg'] = "ERROR: ".$sFieldName." must be a valid url to your image eg. http://www.yourdomain.com/photo.jpg";
        //      return false;
        //} else {
		//	if (!Validation::RemoteFileExists($sUrl)) {
        //       $aResponse['msg'] = "ERROR: ".$sFieldName." not found at url : ". $sUrl;
        //      	return false;
		//	}
        //}
        return true;
	}


	/*
	 * Check that a remote file specified by url exists
	 *
	 * @param string url
	 * @return boolean
	 *
	*/
	public static function RemoteFileExists($sUrl) {
		$addr = parse_url($sUrl);
		$addr['port']= 80;
		if (!$sh=@fsockopen($addr['host'],$addr['port'],$errorno,$errorstr,3)) return false;
		fputs($sh,"HEAD {$addr['path']} HTTP/1.1\r\nHost: {$addr['host']}\r\n\r\n");
		$line=@fgets($sh,16);
		if (preg_match('/^HTTP\/1.1 200 OK/',$line,$m)) {
			fclose($sh);
			return true;
		}
	}


	public static function ValidateCompany($p,&$aResponse) {

		if (DEBUG) Logger::Msg(get_class()."::".__FUNCTION__."()");

		global $oAuth, $_CONFIG;

		if (preg_replace("/ /","",$p['title']) == "") {
			$aResponse['msg']['title'] = "You must enter valid title.";
		}

		if (strlen($p['title']) > 80) {
			$aResponse['msg']['title'] = "Title should be short - 80 characters or less.";
		}

		if (preg_replace("/ /","",$p['desc_short']) == "") {
			$aResponse['msg']['desc_short'] = "You must enter valid short description.";
		}

                if (strlen($p['desc_short']) > 1999) {
                        $aResponse['msg']['title'] = "Short description should be less than 2000 characters.";
                }


		if (preg_replace("/ /","",$p['desc_long']) == "") {
			$aResponse['msg']['desc_long'] = "You must enter a full description.";
		}

		if (strlen($p['email']) < 1) {
			$aResponse['msg']['email'] = "You must enter an enquiry email address.";
		}

		if (strlen($p['email']) > 60) {
			$aResponse['msg']['email'] = "Email address must be less than 60 characters.";
		}

		//if ((preg_replace("/ /","",$p['url']) == "") || ($p['url'] == "http://www.")) {
		//	$aResponse['msg']['url'] = "You must enter valid url.";
		//}

		if (strlen($p['url']) > 256) {
			$aResponse['msg']['url'] = "Url must be less the 256 characters. Use a shortening service like tinyurl.com, goo.gl or bit.ly if your url is too long.";
		}

		if ((strlen(trim($p['apply_url'])) > 1) && (strlen($p['apply_url']) > 256)) {
			$aResponse['msg']['apply_url'] = "Apply Url must be less the 256 characters.";
		}

		//var_dump($p);

		//if (strlen($p['desc_short']) > 300) {
		//	$aResponse['msg']['desc_short'] = "Short Description should be brief - a single paragraph, 300 characters or less.";
		//}

		if (strlen($p['desc_long']) > 20000) {
			$aResponse['msg']['desc_long'] = "Full Description should be less than 20000chars.";
		}

		if ((strlen(trim($p['address'])) > 1) && (strlen($p['address']) > 999)) {
			$aResponse['msg']['address'] = "Address must be less than 999 characters.";
		}

		if ( isset($p['location']) && (strlen(trim($p['location'])) > 1) && (strlen($p['location']) > 99)) {
			$aResponse['msg']['location'] = "Region / State must be less than 99 characters.";
		}

		if ((strlen(trim($p['tel'])) > 1) && (strlen($p['tel']) > 39)) {
			$aResponse['msg']['tel'] = "Telephone no must be less than 39 characters.";
		}


		$iCat = 0;
		$iAct = 0;
		$iCty = 0;
		foreach($p as $k => $v) {
			if ((preg_match("/cat_/",$k)) && ($v == "on")) $iCat++;
			if ((preg_match("/act_/",$k)) && ($v == "on")) $iAct++;
			if ((preg_match("/cty_/",$k)) && ($v == "on")) $iCty++;
		}

		if ($iCat == 0) {
			$aResponse['msg']['category'] = "Select at least one category.";
		}

		if ($iAct == 0) {
			$aResponse['msg']['activity'] = "Select at least one activity.";
		}
		if ($iCty == 0) {
			$aResponse['msg']['country'] = "Select at least one country.";
		}

		/*  validate listing options (ADMIN ONLY) */
		if (($oAuth->oUser->isAdmin) && ($p['prod_type'] >= BASIC_LISTING)) {

			$aListingOption = ListingOption::GetAll($_CONFIG['site_id'],$currency = 'GBP',$from = -1, $to = 3);

			if ($p['listing_type'] == "null") {
				$aResponse['msg']['listing_type'] = "Please select a listing type eg Basic Listing 6 months.";
			}
			/* listing deal type must match listing type */;
			if ($aListingOption[$p['listing_type']]['type'] != $p['prod_type']) {

				switch($p['prod_type']) {
					case SPONSORED_LISTING :
						$listing = "Sponsored Listing";
						break;
					case ENHANCED_LISTING :
						$listing = "Enhanced Listing";
						break;
					case BASIC_LISTING :
						$listing = "Basic Listing";
						break;
					default :
						$listing = "Free Listing";
						break;
				}
				$aResponse['msg']['listing_type'] = "Listing type (".$aListingOption[$p['listing_type']]['label'].") does not match listing type (".$listing.").";
			}

			if (($p['ListingMonth'] == "null") || ($p['ListingYear'] == "null")) {
				$aResponse['msg']['listing_start_date'] = "Please enter the listing start date.";
			}

		}


		if ($oAuth->oUser->isAdmin) {
			if ((!isset($p['prof_opt_1'])) &&
				(!isset($p['prof_opt_2'])) &&
				(!isset($p['prof_opt_3']))
			) {
				$aResponse['msg']['prof_opt_1'] = "At least one profile type must be enabled or this company will be unable to post placements.";
			}
		}


		// profile type specific validations
		if ($p['profile_type'] == PROFILE_COMPANY) {
			if (strlen($p[PROFILE_FIELD_COMP_GENERAL_DURATION]) > 1024) {
				$aResponse['msg'][PROFILE_FIELD_COMP_GENERAL_DURATION] = "Duration / Dates must be less than 1024 characters";
			}
			if (strlen($p[PROFILE_FIELD_COMP_GENERAL_PLACEMENT_INFO]) > 1024) {
				$aResponse['msg'][PROFILE_FIELD_COMP_GENERAL_PLACEMENT_INFO] = "Placement / Job Info must be less than 1024 characters";
			}
			if (strlen($p[PROFILE_FIELD_COMP_GENERAL_COSTS]) > 1024) {
				$aResponse['msg'][PROFILE_FIELD_COMP_GENERAL_COSTS] = "Costs / Pay must be less than 1024 characters";
			}
		}


		if ($p['profile_type'] == PROFILE_VOLUNTEER_PROJECT) {

		    if (($p[PROFILE_FIELD_VOLUNTEER_DURATION_FROM] == null) || ($p[PROFILE_FIELD_VOLUNTEER_DURATION_TO] == null)) {
		        $aResponse['msg'][PROFILE_FIELD_VOLUNTEER_DURATION_LABEL] = "Please enter program duration.";
		    }

		    if (($p[PROFILE_FIELD_VOLUNTEER_PRICE_FROM] == null) || ($p[PROFILE_FIELD_VOLUNTEER_PRICE_TO] == null)) {
		        $aResponse['msg'][PROFILE_FIELD_VOLUNTEER_PRICE_LABEL] = "Please enter approx program costs / fees.";
		    }

			if (strlen($p[PROFILE_FIELD_VOLUNTEER_FOUNDED]) > 32) {
				$aResponse['msg'][PROFILE_FIELD_VOLUNTEER_FOUNDED] = "Founded must be less than 32 characters";
			}
			if (strlen($p[PROFILE_FIELD_VOLUNTEER_AWARDS]) > 511) {
				$aResponse['msg'][PROFILE_FIELD_VOLUNTEER_AWARDS] = "Awards must be less than 511 characters";
			}
			if (strlen($p[PROFILE_FIELD_VOLUNTEER_SAFETY]) > 511) {
				$aResponse['msg'][PROFILE_FIELD_VOLUNTEER_SAFETY] = "Safety must be less than 511 characters";
			}
			if (strlen($p[PROFILE_FIELD_VOLUNTEER_SUPPORT]) > 511) {
				$aResponse['msg'][PROFILE_FIELD_VOLUNTEER_SUPPORT] = "Support must be less than 511 characters";
			}

		}

		if ($p['profile_type'] == PROFILE_SEASONALJOBS) {

			if (strlen($p[PROFILE_FIELD_SEASONALJOBS_JOB_TYPES]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_SEASONALJOBS_JOB_TYPES] = "Job types must be less than 512 characters";
			}
			if (strlen($p[PROFILE_FIELD_SEASONALJOBS_PAY]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_SEASONALJOBS_PAY] = "Salary / Pay must be less than 512 characters";
			}
			if (strlen($p[PROFILE_FIELD_SEASONALJOBS_BENEFITS]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_SEASONALJOBS_BENEFITS] = "Benefits must be less than 512 characters";
			}
			if (strlen($p[PROFILE_FIELD_SEASONALJOBS_REQUIREMENTS]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_SEASONALJOBS_REQUIREMENTS] = "Requirements must be less than 512 characters";
			}
			if (strlen($p[PROFILE_FIELD_SEASONALJOBS_HOW_TO_APPLY]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_SEASONALJOBS_HOW_TO_APPLY] = "How to apply must be less than 512 characters";
			}

		}

		if ($p['profile_type'] == PROFILE_TEACHING) {

			if (strlen($p[PROFILE_FIELD_TEACHING_SALARY]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_TEACHING_SALARY] = "Salary must be less than 512 characters";
			}
			if (strlen($p[PROFILE_FIELD_TEACHING_BENEFITS]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_TEACHING_BENEFITS] = "Benefits must be less than 512 characters";
			}
			if (strlen($p[PROFILE_FIELD_TEACHING_QUALIFICATIONS]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_TEACHING_QUALIFICATIONS] = "Qualifications must be less than 512 characters";
			}
			if (strlen($p[PROFILE_FIELD_TEACHING_REQUIREMENTS]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_TEACHING_REQUIREMENTS] = "Requirements must be less than 512 characters";
			}
			if (strlen($p[PROFILE_FIELD_TEACHING_HOW_TO_APPLY]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_TEACHING_HOW_TO_APPLY] = "How to apply must be less than 512 characters";
			}

		}

		if ($p['profile_type'] == PROFILE_SUMMERCAMP) {

		    if (!is_numeric($p[PROFILE_FIELD_COMP_STATE_ID])) {
		        $aResponse['msg'][PROFILE_FIELD_COMP_STATE_ID] = "Please specify camp US state.";
		    }

			$aCampType = Mapping::GetIdByKey($p,REFDATA_CAMP_TYPE_PREFIX);
			if (count($aCampType) < 1) {
				$aResponse['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_TYPE] = "Please specify camp type.";
			}

			if (($p[PROFILE_FIELD_SUMMERCAMP_DURATION_FROM] == null) || ($p[PROFILE_FIELD_SUMMERCAMP_DURATION_TO] == null)) {
				$aResponse['msg'][PROFILE_FIELD_SUMMERCAMP_DURATION_LABEL] = "Please enter program duration.";
			}

			if (($p[PROFILE_FIELD_SUMMERCAMP_PRICE_FROM] == null) || ($p[PROFILE_FIELD_SUMMERCAMP_PRICE_TO] == null)) {
			    $aResponse['msg'][PROFILE_FIELD_SUMMERCAMP_PRICE_LABEL] = "Please enter approx program costs / fees.";
			}

			if (strlen($p[PROFILE_FIELD_SUMMERCAMP_SEASON_DATES]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_SUMMERCAMP_SEASON_DATES] = "Season dates must be less than 512 characters";
			}
			if (strlen($p[PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS] = "Requirements must be less than 512 characters";
			}
			if (strlen($p[PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY]) > 512) {
				$aResponse['msg'][PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY] = "How to apply must be less than 512 characters";
			}

			$aCampActivities = Mapping::GetIdByKey($p,REFDATA_ACTIVITY_PREFIX);
			if (count($aCampActivities) < 1) {
				$aResponse['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_ACTIVITY] = "Please select one or more activity types available at camp.";
			}

		}

		if ($p['profile_type'] == PROFILE_COURSES) {
		    		    
		    if (($p[PROFILE_FIELD_COURSES_DURATION_FROM] == null) || ($p[PROFILE_FIELD_COURSES_DURATION_TO] == null)) {
		        $aResponse['msg'][PROFILE_FIELD_COURSES_DURATION_LABEL] = "Please enter program duration.";
		    }

		    if (($p[PROFILE_FIELD_COURSES_PRICE_FROM] == null) || ($p[PROFILE_FIELD_COURSES_PRICE_TO] == null)) {
		        $aResponse['msg'][PROFILE_FIELD_COURSES_PRICE_LABEL] = "Please enter approx program costs / fees.";
		    }
		    
		    if (strlen($p[PROFILE_FIELD_COURSES_START_DATES]) > 512) {
		        $aResponse['msg'][PROFILE_FIELD_COURSES_START_DATES] = "Start dates must be less than 512 characters";
		    }
		    if (strlen($p[PROFILE_FIELD_COURSES_QUALIFICATION]) > 512) {
		        $aResponse['msg'][PROFILE_FIELD_COURSES_QUALIFICATION] = "Qualification must be less than 512 characters";
		    }
		    if (strlen($p[PROFILE_FIELD_COURSES_PREPARATION]) > 512) {
		        $aResponse['msg'][PROFILE_FIELD_COURSES_PREPARATION] = "Preparation must be less than 512 characters";
		    }
		    if (strlen($p[PROFILE_FIELD_COURSES_REQUIREMENTS]) > 512) {
		        $aResponse['msg'][PROFILE_FIELD_COURSES_REQUIREMENTS] = "Requirements must be less than 512 characters";
		    }
		    if (strlen($p[PROFILE_FIELD_COURSES_HOW_TO_APPLY]) > 512) {
		        $aResponse['msg'][PROFILE_FIELD_COURSES_HOW_TO_APPLY] = "How to apply must be less than 512 characters";
		    }
		    
		}

		if (is_array($aResponse['msg']) && (count($aResponse['msg']))) {
			return false;
		}


		return true;
	}

	public static function IsValidEmail( $sEmail ){
		if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", trim($sEmail))) {
			return true;
		}
	}


}


?>
