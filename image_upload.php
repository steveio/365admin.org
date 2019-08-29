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
define("LOG", FALSE);
define("LOG_PATH","/www/vhosts/365admin.org/logs/365admin_indexer.log");
define("JOBNAME","IMAGE_UPLOAD");

if (LOG) Logger::DB(3,JOBNAME,'STARTED PROCESSING: ');




/* establish database connection */
$db = new db($dsn,$debug = false);

/* start a new session */
session_start();

$oSession = new Session;
if ($oSession->Exists()) {
    $oSession = $oSession->Get();
}

$article_id = $_SESSION['article_id'];

if (LOG) Logger::DB(3,JOBNAME,'ArticleId: '.$article_id);


$max_size = IMAGE_MAX_UPLOAD_SIZE;
$max_uploads  = 4;
$path = '/www/vhosts/oneworld365.org/htdocs/img/101/';



$callback = $_GET['CKEditorFuncNum'];
$url = '';
$message = '';


$upload = new File_upload();
$upload->allow('images');
$upload->set_path($path);
$upload->set_max_size($max_size);

$aFile = $upload->upload($_FILES['upload'],FALSE);

if (LOG) Logger::DB(3,JOBNAME,json_encode($aFile));

$error = false;
if ($upload->is_error()) {
	$error = true;
	$msg= $upload->get_error();
} else {
	$url = "http://www.oneworld365.org/img/101/".$aFile['FILENAME'];
	$msg= 'Image uploaded: '.$url;
}

// generate small, medium, large proxy images, attach to article
$oImageProcessor = new ImageProcessor_FileUpload();
$oImageProcessor->Process(array($aFile['TMP_PATH']), 'ARTICLE', $article_id);

$aId = $oImageProcessor->GetProcessedIds();

$iImageId = array_shift($aId);

if (LOG) Logger::DB(3,JOBNAME,"Image Id: ".$iImageId);

$oImage = new Image();
$oImage->GetById($iImageId);

if (LOG) Logger::DB(3,JOBNAME,var_export($oImage->GetUrl()));

$output = '<html><body><script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$callback.', "'.$oImage->GetUrl().'","'.$msg.'");</script></body></html>';
echo $output;

?>