<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);


$sql = "select oid,* from article_map where website_id = 0 and section_uri like '/work-volunteer-travel/%/work%'";
$db->query($sql);
$result = $db->getRows();

foreach($result as $row) {
	$row['new_section_uri'] = preg_replace("/work$/","work-abroad",$row['section_uri']); 
	print "update article_map set section_uri = '".$row['new_section_uri']."' where oid = ".$row['oid'].";";
	print "\n";
}

$sql = "select oid,* from article_map where website_id = 0 and section_uri like '/work-volunteer-travel/%/volunteer%'";
$db->query($sql);
$result = $db->getRows();

foreach($result as $row) {
        $row['new_section_uri'] = preg_replace("/volunteer$/","volunteer-abroad",$row['section_uri']);
        print "update article_map set section_uri = '".$row['new_section_uri']."' where oid = ".$row['oid'].";";
        print "\n";
}


$sql = "select oid,* from article_map where website_id = 0 and section_uri like '/work-volunteer-travel/%/teach%'";
$db->query($sql);
$result = $db->getRows();

foreach($result as $row) {
        $row['new_section_uri'] = preg_replace("/teach$/","teaching-projects",$row['section_uri']);
        print "update article_map set section_uri = '".$row['new_section_uri']."' where oid = ".$row['oid'].";";
        print "\n";
}


$sql = "select oid,* from article_map where website_id = 0 and section_uri like '/work-volunteer-travel/%/travel-tour%'";
$db->query($sql);
$result = $db->getRows();

foreach($result as $row) {
        $row['new_section_uri'] = preg_replace("/travel-tour$/","travel-adventure-tours",$row['section_uri']);
        print "update article_map set section_uri = '".$row['new_section_uri']."' where oid = ".$row['oid'].";";
        print "\n";
}




?>
