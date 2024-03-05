<?php


/*
 * Handles :
 * 	- delete attached images 
 * 
 * @param $_GET['link_to'] {ARTICLE||COMPANY||PLACEMENT}
 * @param $_GET['id'] int link object id
 * @param $_GET['image_id'] image id 
 *  
 */

require_once("../conf/config.php");
require_once("../init.php");
require_once("../classes/json.class.php");
require_once("../classes/db_pgsql.class.php");
require_once("../classes/logger.php");
require_once("../classes/file.class.php");
require_once("../classes/image.class.php");
require_once("../classes/session.php");



$db = new db($dsn,$debug = false);

$oAuth = new Authenticate($db,$redirect = TRUE, $redirect_url = "/".ROUTE_LOGIN, COOKIE_NAME);
if (!$oAuth->ValidSession())
{
    $aResponse['msg'] = "ERROR: Invalid session";
    sendResponse($aResponse);
    die();
}

$aResponse = array();
$aResponse['retVal'] = false;
$aResponse['msg'] = "";


$link_to = $_GET['link_to'];
$link_id = $_GET['link_id'];
$img_id = $_GET['image_id']; 


if (!is_numeric($link_id) ||
    !is_numeric($img_id) ||
    !in_array($link_to,array("ARTICLE","COMPANY","PLACEMENT"))
    ) {
        $aResponse['msg'] = "ERROR: Invalid params in call to remove image";
        sendResponse($aResponse);
    }


if (!$oAuth->oUser->isAdmin)
{
    switch($link_to)
    {
        case "COMPANY" :
            if ($oAuth->oUser->GetCompanyId() != $link_id) {
                $aResponse['msg'] = "ERROR: Invalid request";
                sendResponse($aResponse);
            }
            break;
        case "PLACEMENT" :
            $sql = "SELECT company_id from profile_hdr where id = ".$link_id;
            $company_id = $db->getFirstCell($sql);
            $oAuth->oUser->GetCompanyId();
            if ($oAuth->oUser->GetCompanyId() != $company_id) { 
                $aResponse['msg'] = "ERROR: Invalid request";
                sendResponse($aResponse);
            }
            break;
        default: // non-admin can only edit profiles
            $aResponse['msg'] = "ERROR: Invalid request";
            sendResponse($aResponse);
    }
}

$sql = "SELECT i.*,m.type FROM image_map m, image i WHERE m.img_id = i.id AND m.link_to = '".$link_to."' AND m.link_id = ".$link_id." AND i.id = ".$img_id;
$db->query($sql);

if ($db->getNumRows() == 1) {
	$o = $db->getObject();
	/*
	 * Do not delete image from filesystem, there may be html pages with links
	$oImage = new Image($o->id,$o->type,$o->ext,$o->dimensions,$o->width,$o->height,$o->aspect);
	$oImage->Delete();
	*/

	$sql = "DELETE FROM ".$_CONFIG['image_map']." WHERE link_to = '".$link_to."' and link_id = '".$link_id."' AND img_id = ".$img_id;
	$db->query($sql);
		
	$aResponse['retVal'] = true;
	$aResponse['msg'] = "SUCCESS: Detached image";
	$aResponse['image_id'] = $o->id;
	sendResponse($aResponse);
} else {
   	$aResponse['msg'] = "ERROR: Unable to resolve supplied image parameters";
	sendResponse($aResponse);	 
}





function sendResponse($aResponse) {

	//Logger::Msg($aResponse);

	/* return response back to the caller */
	$oJson = new Services_JSON;
	header('Content-type: application/x-json');
	print $oJson->encode($aResponse);
	die();	

}

?>
