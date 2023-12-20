<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$fp = fopen('./data/comps_by_category_free.csv', 'w');

/*
$db->query("select * FROM article_map where website_id = 4 and section_uri LIKE '/tefl-tesol-courses/%';");

$result = $db->getRows();

foreach($result as $row) {
	print "UPDATE article_map SET section_uri = '".preg_replace("/\/tefl-tesol-courses/","",$row['section_uri'])."' WHERE section_uri = '".$row['section_uri']."' AND website_id = 4;\n"; 
	fputcsv($fp, $row);
}
*/

$db->query("select * FROM article_map where website_id = 4 and section_uri LIKE '/teaching-jobs/%';");

$result = $db->getRows();

foreach($result as $row) {
        print "UPDATE article_map SET section_uri = '".preg_replace("/\/teaching-jobs/","",$row['section_uri'])."' WHERE section_uri = '".$row['section_uri']."' AND website_id = 4;\n";
        fputcsv($fp, $row);
}




?>
