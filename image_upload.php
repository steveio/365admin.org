<?php


require_once("./conf/config.php");
require_once("./classes/file_upload.class.php");
require_once("./classes/logger.php");
require_once("./classes/db_pgsql.class.php");
require_once("./classes/logger.php");
require_once("./classes/session.php");
require_once("./classes/image.class.php");
require_once("./classes/file.class.php");
require_once("./classes/logger.php");

ini_set('display_errors',0);
ini_set('log_errors', 1);
ini_set('error_log', '/www/vhosts/365admin.org/logs/365admin_indexer.log');
error_reporting(E_ALL & ~E_NOTICE & ~ E_STRICT);


define("DEBUG",FALSE);
define("LOG", TRUE);
define("JOBNAME","IMAGE_UPLOAD");

if (LOG) Logger::DB(3,JOBNAME,'STARTED PROCESSING: ');

// script must not be called directly
if(!isset($_SERVER['HTTP_REFERER']) || strlen($_SERVER['HTTP_REFERER']) < 1)
{
    header("HTTP/1.1 403 Origin Denied");
    die();
}



/* establish database connection */
$db = new db($dsn,$debug = false);

/* start a new session */
session_start();

$oSession = new Session;
if ($oSession->Exists()) {
    $oSession = $oSession->Get();
}


/***************************************************
* Only these origins are allowed to upload images *
 ***************************************************/
$accepted_origins = array("https://localhost", "https://192.168.1.1", "https://admin.oneworld365.org", "http://admin.oneworld365.org");


if (isset($_SERVER['HTTP_ORIGIN'])) {
    // same-origin requests won't set an origin. If the origin is set, it must be valid.
    if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
      header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    } else {
      header("HTTP/1.1 403 Origin Denied");
      return;
    }
}

// Don't attempt to process the upload on an OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    return;
}


$link_id = (is_numeric($_SESSION['id'])) ? $_SESSION['id'] : 0; // article/profile must be saved
$link_to = $_SESSION['link_to'];

if (!is_numeric($link_id))
{
    die("ERROR: Article / Profile must be saved");
}

if (LOG) Logger::DB(3,JOBNAME,'ArticleId: '.$article_id);


$max_size = IMAGE_MAX_UPLOAD_SIZE;
$max_uploads  = 4;
$path = '/www/vhosts/oneworld365.org/htdocs/img/101/';


try {

	$url = '';
	$message = '';

	reset ($_FILES);
  	$temp = current($_FILES);

	$upload = new File_upload();
	$upload->allow('images');
	$upload->set_path($path);
	$upload->set_max_size($max_size);

	$aFile = $upload->upload($temp,FALSE);


	if (LOG) Logger::DB(3,JOBNAME,json_encode($aFile));

	$error = false;
	if ($upload->is_error()) {
		throw new Exception($upload->get_error);
	} else {
		$url = "http://www.oneworld365.org/img/101/".$aFile['FILENAME'];
	}

	Logger::DB(3,JOBNAME,$url);


	// generate small, medium, large proxy images, attach to content
	$oImageProcessor = new ImageProcessor_FileUpload();
	$oImageProcessor->Process(array($aFile['TMP_PATH']), $link_to, $link_id);


	$aId = $oImageProcessor->GetProcessedIds();


	$iImageId = array_shift($aId);

	if (LOG) Logger::DB(3,JOBNAME,"Image Id: ".$iImageId);

	$oImage = new Image();
	$oImage->GetById($iImageId);



	echo json_encode(array('location' => $oImage->GetUrl()));


} catch(Exception $e) {
	Logger::DB(1,JOBNAME,$e->getMessage());
	header("HTTP/1.1 500 Server Error");
}
?>
