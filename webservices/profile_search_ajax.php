<?php


/*
 * Profile Search AJAX - an API for searching / retrieving profiles (companies / placements)
 * 
 * @param $_GET['keywords'] Search Keywords
 * @param $_GET['m'] mode: 0 = Placement, 1 = Company, 2 = Combined
 * @param $_GET['t'] template: path to PHP file in /templates/
 *
 */

require_once("./conf/config.php");
require_once("./classes/json.class.php");
require_once("./classes/db_pgsql.class.php");
require_once("./classes/logger.php");
require_once("./classes/template.class.php");
require_once("./classes/link.class.php");
require_once("./classes/company.class.php");
require_once("./classes/placement.class.php");


$db = new db($dsn,$debug = false);



$aResponse = array();
$aResponse['retVal'] = false;
$aResponse['msg'] = "";


$keywords = $_GET['keywords'];
$mode = $_GET['m'];
$template = $_GET['t'];


// Validate Input Params
if (!is_numeric($mode) || !in_array($mode,array(0,1,2))) {
	$aResponse['msg'] = "ERROR : Invalid mode";
	sendResponse($aResponse);
}
if (preg_match("/[^a-zA-Z0-9\/ _\-\%]/",$keywords)) {
	$aResponse['msg'] = "ERROR : Invalid Keywords";
	sendResponse($aResponse);
}
if (preg_match("/[^a-zA-Z0-9\/ _\-\%\.]/",$template)) {
	$aResponse['msg'] = "ERROR : Invalid template name";
	sendResponse($aResponse);
}



$oCompany = new Company($db);

/*
 * search for articles published to uri and return a result list
if ($mode == "search") { 

	$bUnpublished = ($uri == "UNPUBLISHED") ? true : false;
	
	$oArticleCollection = new ArticleCollection();
	if ($match == ARTICLE_SEARCH_MODE_EXACT) {
		$oArticleCollection->SetSearchMode(ARTICLE_SEARCH_MODE_EXACT);
	}
	$oArticleCollection->GetBySectionId($website_id,$uri,$getAttachedObj = false,$bUnpublished);
	
	if ($oArticleCollection->Count() < 1) {
		$aResponse['msg'] = "No articles found matching uri: ".$uri."<br />Try again with a pattern match eg %".$uri;
		sendResponse($aResponse);
	} 
	
	$oArticleCollection->LoadTemplate($template);
	
	$aResponse['retVal'] = true;
	$aResponse['msg'] = "Found ".$oArticleCollection->Count()." articles.";
	$aResponse['html'] = $oArticleCollection->Render(); 
	sendResponse($aResponse);
	
	
}
 */



function sendResponse($aResponse) {

	/* return response back to the caller */
	$oJson = new Services_JSON;
	header('Content-type: application/x-json');
	print $oJson->encode($aResponse);
	die();	

}

?>
