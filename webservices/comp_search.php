<?php

require_once("../conf/config.php");
require_once("../init.php");


$raw_search_str = $_GET['s'];
$search_str = addslashes(preg_replace("/[^a-zA-Z0-9 _\-\/\']/","",$raw_search_str));

$oCompanyProfile = new CompanyProfile;
$aResult = array();
$aResult['count'] = 0;
$aResult['data'] = array();

/* setup an instance of session authentication */
$oAuth = new Authenticate($db,$redirect = FALSE, $redirect_url = "", COOKIE_NAME);
if (!$oAuth->ValidSession()) sendResponse($aResponse);


if (preg_match("/\/company/",$search_str)) { // search by url_name
	
} else { // search by name
	$aResult['data'] = $oCompanyProfile->GetCompanyListByName($search_str);
	$aResult['count'] = count($aResult['data']);
}


sendResponse($aResult);


function sendResponse($aResponse) {
	
	/* return response back to the caller */
	$oJson = new Services_JSON;
	header('Content-type: application/x-json');
	print $oJson->encode($aResponse);
	die();
}


?>