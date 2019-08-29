<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$fp = fopen('./data/company_profiles.csv', 'w');

$db->query("SELECT * FROM article_map m WHERE website_id = 0 and section_uri like '/country/%';");

$result = $db->getRows();

foreach($result as $row) {
	$row['new_section_uri'] = preg_replace("/^\/country/","/work-volunteer-travel",$row['section_uri']); 

	print "INSERT INTO article_map (website_id,article_id,section_uri) VALUES (0,".$row['article_id'].",'".$row['new_section_uri']."');";

	//print_r($row);
	print "\n";
}




?>
