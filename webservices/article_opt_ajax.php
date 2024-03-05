<?php

/*
 * Handles :
 * 	- attaching/ detaching links from associated objects
 * 
 *  
 */

require_once("../conf/config.php");
require_once("../conf/brand_config.php");
require_once("../classes/Brand.php");
require_once("../classes/json.class.php");
require_once("../classes/db_pgsql.class.php");
require_once("../classes/logger.php");
require_once("../classes/template.class.php");
require_once("../classes/TemplateList.class.php");
require_once("../classes/article.class.php");
require_once("../classes/ArticleCollection.class.php");
require_once("../classes/ContentMapping.class.php");
require_once("../classes/cache.class.php");

$db = new db($dsn,$debug = false);

if (!is_object($oBrand))
{
    $oBrand = new Brand($aBrandConfig[HOSTNAME]);
}


$aResponse = array();
$aResponse['retVal'] = false;
$aResponse['status'] = "";
$aResponse['msg'] = "";


$mid = $_POST['mid'];
$opts = $_POST['opts'];
$search_keywords = trim($_POST['q']);
$p_title = trim($_POST['pt']);
$p_intro = trim($_POST['pi']);
$tid = $_POST['tid'];
$scid = $_POST['scid'];


if (!is_numeric($mid)) {
    $aResponse['retVal'] = false;
    $aResponse['status'] = "warning";
	$aResponse['msg'] = "ERROR : Invalid / Missing Mapping ID";
	sendResponse($aResponse);
}

$opts_array = array();

$opts_bits = explode("::",$opts);
foreach($opts_bits as $opt) {
	$bits = explode("_",$opt);
	$opt_id = $bits[2]; 
	$opt_val =  $bits[3];
	if (is_numeric($opt_id) && in_array($opt_val,array("T","F"))) {
		$opts_array[$opt_id] =  strtolower($opt_val);
	}
} 


if (!is_array($opts_array) || count($opts_array) < 1) {
    $aResponse['retVal'] = false;
    $aResponse['status'] = "warning";
	$aResponse['msg'] = "ERROR : Invalid / empty content options array";
	sendResponse($aResponse);
}

$oContentMapping = new ContentMapping($mid,NULL,NULL);
$result = $oContentMapping->GetById();
if (!$result) {
    $aResponse['retVal'] = false;
    $aResponse['status'] = "warning";
	$aResponse['msg'] = "ERROR : Unable to retrieve mapping";
	sendResponse($aResponse);
}

$aTextFieldOpts = array(
					"search_keywords" => $search_keywords,
					"p_title" => $p_title,
					"p_intro" => $p_intro
					);

$opts_array[ARTICLE_DISPLAY_OPT_TEMPLATE_ID] = $tid;
$opts_array[ARTICLE_DISPLAY_OPT_SEARCH_CONFIG] = $scid;

$oTemplateList = new TemplateList();
$oTemplateList->GetFromDB();
$oTemplateCfg = $oTemplateList->GetById($opts_array[ARTICLE_DISPLAY_OPT_TEMPLATE_ID]);

if ($oTemplateCfg->is_collection && ($opts_array[ARTICLE_DISPLAY_OPT_ATTACHED] == "f" && $opts_array[ARTICLE_DISPLAY_OPT_PATH] == "f"))
{
    $aResponse['retVal'] = false;
    $aResponse['status'] = "warning";
    $aResponse['msg'] = "ERROR : Content From must be either Attached Articles or Path";
    sendResponse($aResponse);
}


if ($opts_array[ARTICLE_DISPLAY_OPT_PATH] == "t" && $opts_array[ARTICLE_DISPLAY_OPT_ATTACHED] == "t")
{
    $aResponse['retVal'] = false;
    $aResponse['status'] = "warning";
    $aResponse['msg'] = "ERROR : Content From must be either Attached Articles or Path";
    sendResponse($aResponse);
}

if ($oContentMapping->SetOptions($mid,$opts_array, $aTextFieldOpts))
{
    // trigger cache update of published page
    $oContentMapping->SetCacheUpdate();
    
    $aResponse['retVal'] = true;
    $aResponse['status'] = "success";
    $aResponse['msg'] = "SUCCESS: Updated article content options";
    sendResponse($aResponse);
} else {    
    $aResponse['retVal'] = false;
    $aResponse['status'] = "warning";
    $aResponse['msg'] = "ERROR: unable to save article publisher options";
    sendResponse($aResponse);
}





	

function sendResponse($aResponse) {

    /* return response back to the caller */
    $oJson = new Services_JSON;
    header('Content-type: application/x-json');
    print $oJson->encode($aResponse);
    die();	

}

?>
