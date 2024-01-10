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
require_once("./conf/brand_config.php");
require_once("./classes/logger.php");
require_once("./classes/json.class.php");
require_once("./classes/db_pgsql.class.php");
require_once("./classes/file.class.php");
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
$match = $_GET['match'];
$filterDate = $_GET['filterDate'];
$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];


// Validate Input Params
if (preg_match("/[^a-zA-Z0-9\/ _\-\%]/",$exp)) {
	$aResponse['msg'] = "ERROR : Invalid Search Expression";
	sendResponse($aResponse);
}
if (preg_match("/[^a-zA-Z0-9\/ _\-\%\.]/",$template)) {
	$aResponse['msg'] = "ERROR : Invalid template name";
	sendResponse($aResponse);
}
if(!is_numeric($match)) {
    $aResponse['msg'] = "ERROR : Invalid match param";
    sendResponse($aResponse);
}

Logger::DB(2,basename(__FILE__)." exp:".$exp.", t: ".$template." match: ".$match);


if (preg_match("/^\//",$exp) || $exp = "UNPUBLISHED")
{
    uriSearch($exp, $match, $filterDate, $fromDate, $toDate);
} 


function uriSearch($uri, $match, $filterDate, $fromDate, $toDate)
{
    global $db, $aBrandConfig;
    
    if (preg_match("/^\/company\//",$uri)) // org or placement uri search /company/*
    {

        $template = "search_result_list_profile.php";
        
        $uri_depth = substr_count($uri, '/');

        $uri = strtolower($uri);
        $fuzzy = false;
        if (substr_count($uri, '%') >= 1)
        {
            $fuzzy = true;
        }

        if ($uri_depth >= 1 && $uri_depth <= 2) // company search: /company/company-url  
        {
            
            $oCompany = new Company($db);

            $uri = preg_replace("/\/company\//","",$uri);
            $aResult = $oCompany->GetByUri($uri, $fuzzy);

            if (is_array($aResult) && count($aResult) >= 1)
            {
                $oTemplate = new Template();
                $oTemplate->Set("RESULT_ARRAY",$aResult);
                $oTemplate->Set("WEBSITE_URL",$aBrandConfig['oneworld365.org']['website_url']);
                $oTemplate->Set("RESULT_TYPE",'COMPANY');
                $oTemplate->LoadTemplate($template);
                $oTemplate->Render();

                $aResponse['retVal'] = true;
                $aResponse['msg'] = "Found ".count($aResult)." result(s).";
                $aResponse['html'] = $oTemplate->Render();
                $aResponse['status'] = "success";
                sendResponse($aResponse);

            } else {

                $aResponse['retVal'] = true;
                $aResponse['msg'] = "Found 0 results.";
                $aResponse['html'] = '';
                $aResponse['status'] = "warning";
                
            }
            

        } else {  // placement search: /company/company-url/placememt-url


            $oPlacement = new Placement($db);

            // extract placement uri from search expression
            $aUri = explode("/",$uri);

            if (!isset($aUri[3]) || strlen($aUri[3]) < 1)
            {
                $aResponse['msg'] = "Invalid url";
                $aResponse['status'] = "warning";
                sendResponse($aResponse);
            }

            $uri = $aUri[3];

            if ($uri == "%") // wildcard return all profile for /company/comp-name/% 
            {
                $comp_id = Company::GetIdByUri($aUri[2]);
                if (!is_numeric($comp_id))
                {
                    $aResponse['msg'] = "Invalid company url";
                    $aResponse['status'] = "warning";
                    sendResponse($aResponse);
                }

                $aResult = $oPlacement->GetByCompId($comp_id);

            } else {
                $aResult = $oPlacement->GetByUri($uri, $fuzzy);
            }
            
            if (is_array($aResult) && count($aResult) >= 1)
            {
                $oTemplate = new Template();
                $oTemplate->Set("RESULT_ARRAY",$aResult);
                $oTemplate->Set("WEBSITE_URL",$aBrandConfig['oneworld365.org']['website_url']);
                $oTemplate->Set("RESULT_TYPE",'PLACEMENT');
                $oTemplate->LoadTemplate($template);
                $oTemplate->Render();
                
                $aResponse['retVal'] = true;
                $aResponse['msg'] = "Found ".count($aResult)." result(s).";
                $aResponse['html'] = $oTemplate->Render();
                $aResponse['status'] = "success";
                sendResponse($aResponse);
                
            } else {
                
                $aResponse['retVal'] = true;
                $aResponse['msg'] = "Found 0 results.";
                $aResponse['html'] = '';
                $aResponse['status'] = "warning";
                
            }
            

        }

        sendResponse($aResponse);
        
    } else { // article search

        $template = "article_search_result_list_03.php";

        $bUnpublished = ($uri == "UNPUBLISHED") ? true : false;
        
        $oArticleCollection = new ArticleCollection();
        
        if ($match == ARTICLE_SEARCH_MODE_EXACT) {
            $oArticleCollection->SetSearchMode(ARTICLE_SEARCH_MODE_EXACT);
        }

        $oArticleCollection->SetLimit(250);
        $oArticleCollection->GetBySectionId(0,$uri,$getAttachedObj = false,$bUnpublished, $filterDate, $fromDate, $toDate);
        
        if ($oArticleCollection->Count() < 1) {
            $aResponse['msg'] = "No articles found matching uri: ".$uri."<br />Try again with a pattern match eg %".$uri;
            sendResponse($aResponse);
        }

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
