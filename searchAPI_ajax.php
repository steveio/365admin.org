<?php


/*
 * Search API AJAX - an endpoint for searching / retrieving profiles (companies / placements) & articles
 * 
 * This API serves results based on relational DB query (rather than SOLR API)
 * 
 * @param $_GET['mode'] Search Mode : 0 = Uri, 1 = Keyword  
 * @param $_GET['uri'] Uri (relative path)
 * @param $_GET['keywords'] Search Keywords
 * @param $_GET['filter'] 
 * @param $_GET['template'] template: path to PHP file in /templates/
 *
 */

require_once("./conf/config.php");
require_once("./classes/logger.php");
require_once("./classes/json.class.php");
require_once("./classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/file.class.php");
require_once("./classes/logger.php");
require_once("./classes/template.class.php");
require_once("./classes/link.class.php");
require_once("./classes/article.class.php");
require_once("./classes/company.class.php");
require_once("./classes/placement.class.php");


$db = new db($dsn,$debug = false);



$aResponse = array();
$aResponse['retVal'] = false;
$aResponse['msg'] = "";

$exp = $_GET['exp'];
$template = $_GET['t'];


// Validate Input Params
if (preg_match("/[^a-zA-Z0-9\/ _\-\%]/",$exp)) {
	$aResponse['msg'] = "ERROR : Invalid Search Expression";
	sendResponse($aResponse);
}
if (preg_match("/[^a-zA-Z0-9\/ _\-\%\.]/",$template)) {
	$aResponse['msg'] = "ERROR : Invalid template name";
	sendResponse($aResponse);
}
if (!file_exists("./templates/".$template)) {
    $aResponse['msg'] = "ERROR : Invalid template";
    sendResponse($aResponse);
}


Logger::DB(2,basename(__FILE__)." exp:".$exp.", t: ".$template);


if (preg_match("/^\//",$exp))
{
    uriSearch($exp,$template);    
} 


function uriSearch($uri,$template)
{

    if (preg_match("/^\/company\//",$uri))
    {
        
    } else { // article search
        $oArticleCollection = new ArticleCollection();

        $oArticleCollection->GetBySectionId(0,$uri,$getAttachedObj = false,$bUnpublished = false);
        
        if ($oArticleCollection->Count() < 1) {
            $aResponse['msg'] = "No articles found matching uri: ".$uri."<br />Try again with a pattern match eg %".$uri;
            sendResponse($aResponse);
        }

        //Logger::DB(2,basename(__FILE__)." res: ".serialize($oArticleCollection->Get()));
        
        $oArticleCollection->LoadTemplate($template);
        
        $aResponse['retVal'] = true;
        $aResponse['msg'] = "Found ".$oArticleCollection->Count()." articles.";
        $aResponse['html'] = $oArticleCollection->Render();
        $aResponse['status'] = "success";
        sendResponse($aResponse);

    }
}



function sendResponse($aResponse) {

	/* return response back to the caller */
	$oJson = new Services_JSON;
	header('Content-type: application/x-json');
	print $oJson->encode($aResponse);
	die();	

}

?>
