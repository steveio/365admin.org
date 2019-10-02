<?php

require_once("./conf/config.php");
require_once("./init.php");
require_once("./conf/brand_config.php");


$allowed_hosts = array("oneworld365.org", "gapyear365.com", "seasonaljobs365.com","summercamp365.com", "tefl365.com");
$hostname = "oneworld365.org";

define('HOSTNAME',$hostname);
define('BASE_URL','http://admin.'.$hostname);
define("COOKIE_DOMAIN", ".".HOSTNAME);

/* setup an instance of session authentication */
$oAuth = new Authenticate($db,$redirect = TRUE, $redirect_url = "/".ROUTE_LOGIN, COOKIE_NAME);
$oAuth->ValidSession();



/* set some additional $_CONFIG params so the legacy classes work */
$_CONFIG['site_id'] = $aBrandConfig[HOSTNAME]['site_id'];
$_CONFIG['admin_email'] = $aBrandConfig[HOSTNAME]['admin_email'];
$_CONFIG['website_email'] = $aBrandConfig[HOSTNAME]['website_email'];

$oBrand = new Brand($aBrandConfig[HOSTNAME]);


include("./includes/header.php");
include("./includes/footer.php");



/* route request */

$oController = $oSession->GetStepController();

if (!$oController) {
	$oController = new StepController(BASE_PATH);	
	$oController->SetStepsFromXmlFile(BASE_PATH."/conf/steps.xml",$oBrand->GetSiteId());
	$oSession->SetStepController($oController);
}



$request_array = Request::GetUri("ARRAY");
//print_r($request_array);
$request_uri = "/".$request_array[1]; 


try {
	/**
	 * Match older standalong php file routes eg enquiry_repory, article system
	 */
	switch ($request_uri) {

		case "/category-admin" :
			require_once("category_admin.php");
			die();
			break;
		case "/activity-admin" :
			require_once("activity_admin.php");
			die();
			break;
		case "/review-report" :
			require_once("review_report.php");
			die();
			break;
		case "/enquiry-report" :
			require_once("enquiry_report.php");
			die();
			break;
		case "/article" :
			require_once("article.php");
			die();
			break;
		case "/article-manager" :
			require_once("article_mgr.php");
			die();
			break;
		case "/article-editor" :
			require_once("article_edit.php");
			die();
			break;
		case "/article-publisher" :
			require_once("article_pub.php");
			die();
			break;
					
	}
	
} catch (Exception $e) {
	die($e->getMessage());
}


try {
	
	/**
	 * Now match MVC step routes
	 */
	$oController->SetRequestUri($request_uri);
	$oController->MapRequest();
	$oController->Process();
	$oSession->Save();

} catch (InvalidSessionException $e) {  // invalid session / session expired 
	Http::Redirect("/");
} catch (NotFoundException $e) {  // 404 not found error
	print_r(1,"404NotFoundException: ".$e->getMessage());
	header('HTTP/1.0 404 Not Found');	
	die();
	Http::Redirect("/".ROUTE_ERROR);
} catch (Exception $e) { // general exception
	print_r(1,"GeneralException: ".$e->getMessage());
	die();
	Http::Redirect("/".ROUTE_ERROR);
}



?>
