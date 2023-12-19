<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);


// VOLUNTEER 

$db->query("select * from article_map where website_id = 0 and section_uri like '/travel/%/volunteer%';");

$result = $db->getRows();

foreach($result as $row) {

	$bits = explode("/",$row['section_uri']);
	$url = "/travel/".$bits[2]."/volunteer-in-".$bits[2];
	print "INSERT INTO article_map (article_id,website_id,section_uri) VALUES (".$row['article_id'].",".$row['website_id'].",'".$url."');";
	//print_r($row);
	print "\n";
}

// TEACH

$db->query("select * from article_map where website_id = 0 and section_uri like '/travel/%/teaching-projects%';");

$result = $db->getRows();

foreach($result as $row) {

        $bits = explode("/",$row['section_uri']);
        $url = "/travel/".$bits[2]."/teach-in-".$bits[2];
        print "INSERT INTO article_map (article_id,website_id,section_uri) VALUES (".$row['article_id'].",".$row['website_id'].",'".$url."');";
        //print_r($row);
        print "\n";
}

// WORK

$db->query("select * from article_map where website_id = 0 and section_uri like '/travel/%/work-abroad%';");

$result = $db->getRows();

foreach($result as $row) {

        $bits = explode("/",$row['section_uri']);
        $url = "/travel/".$bits[2]."/working-holidays-in-".$bits[2];
        print "INSERT INTO article_map (article_id,website_id,section_uri) VALUES (".$row['article_id'].",".$row['website_id'].",'".$url."');";
        //print_r($row);
        print "\n";
}



// TRAVEL / TOUR

$db->query("select * from article_map where website_id = 0 and section_uri like '/travel/%/travel-adventure-tours%';");

$result = $db->getRows();

foreach($result as $row) {

        $bits = explode("/",$row['section_uri']);
        $url = "/travel/".$bits[2]."/tours-in-".$bits[2];
        print "INSERT INTO article_map (article_id,website_id,section_uri) VALUES (".$row['article_id'].",".$row['website_id'].",'".$url."');";
        //print_r($row);
        print "\n";
} 

?>
