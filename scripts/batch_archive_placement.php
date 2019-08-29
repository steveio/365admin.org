<?php

ini_set('display_errors',0);
ini_set('display_startup_errors',0);
error_reporting(E_ALL);

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/file.class.php");
require_once(BASE_PATH."/classes/logger.php");
require_once(BASE_PATH."/classes/ArchiveManager.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);



$oArchiveManager = new ArchiveManager;
//$result = $oArchiveManager->ArchivePlacement($this->GetPlacementProfile()->GetId());


$db->query("select p.id,p.title from profile_hdr p, company c where c.id = p.company_id and c.url_name = 'maasai-international-challenge-africa-mica'");

$result = $db->getRows();

foreach($result as $row) {
	var_dump($row);
	$oArchiveManager = new ArchiveManager;
	var_dump($oArchiveManager);
	$result = $oArchiveManager->ArchivePlacement($row['id']);
}

?>
