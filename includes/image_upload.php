<?php

ini_set('display_errors',0);

require_once("/www/vhosts/oneworld365.org/htdocs/conf/config.php");

require_once("/www/vhosts/oneworld365.org/htdocs/classes/file_upload.class.php");
require_once("/www/vhosts/oneworld365.org/htdocs/classes/db_pgsql.class.php");
require_once("/www/vhosts/oneworld365.org/htdocs/classes/logger.php");


$db = new db($dsn,$debug = false);


$max_size = 6291456;
$max_uploads  = 4;
$path = "/www/vhosts/oneworld365.org/htdocs/img/101/";



$callback = $_GET['CKEditorFuncNum'];
$url = '';
$message = '';


$upload = new File_upload();
$upload->allow('images');
$upload->set_path($path);
$upload->set_max_size($max_size);

$aFile = $upload->upload($_FILES['upload'],FALSE);

$error = false;
if ($upload->is_error()) {
	$error = true;
	print $msg= $upload->get_error();
} else {
	$url = "http://www.oneworld365.org/img/101/".$aFile['FILENAME'];
	$msg= 'Image uploaded: '.$url;
}


$output = '<html><body><script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$callback.', "'.$url.'","'.$msg.'");</script></body></html>';
echo $output;

?>
