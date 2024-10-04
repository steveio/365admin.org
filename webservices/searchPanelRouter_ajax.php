<?php

/*
 * Search Panel Router : 
 * Resolves Destination / Activity selection to associated URL
 *  
*/

//$db = new db($dsn,$debug = false);


$aResponse = array();

$aResponse['retVal'] = false;
$aResponse['msg'] = "";


// VALIDATE Inputs ----------------------------------------

$key = trim(urldecode($_GET['key']));
if (strlen($key) > 8 || preg_match("/[^a-zA-Z0-9\/ _\-\%]/",$key)) {
    $aResponse['msg'] = "ERROR : Invalid Search Key";
    sendResponse($aResponse);
}


$aBits = explode("_", $key);

$id = $aBits[1];
if (!is_numeric($id))
{
    $aResponse['msg'] = "ERROR : Invalid Search Id";
    sendResponse($aResponse);
}


// Process Request ----------------------------------------


$tbl = "";
$url_prefix = "";
switch($aBits[0])
{
    case "act" :
        $tbl = "activity";
        $url_prefix = "/";
        break;
    case "cat" :
        $tbl =  "category";
        $url_prefix = "/";
        break;
    case "cn" :
        $tbl =  "continent";
        $url_prefix = "/continent/";
        break;
    case "cty" :
        $tbl =  "country";
        $url_prefix = "/travel/";
        break;
    default:
        $aResponse['msg'] = "ERROR : Invalid Key";
        sendResponse($aResponse);
}


$sql = "SELECT url_name from ".$tbl." WHERE id = ".$id;
$url_name = $db->getFirstCell($sql);

$dispatch_url = $url_prefix . $url_name;


$aResponse['retVal'] = true;
$aResponse['html'] = $dispatch_url;
$aResponse['status'] = "success";

sendResponse($aResponse);



function sendResponse($aResponse) {
    
    /* return response back to the caller */
    $oJson = new Services_JSON;
    header('Content-type: application/x-json');
    print $oJson->encode($aResponse);
    die();
}

?>