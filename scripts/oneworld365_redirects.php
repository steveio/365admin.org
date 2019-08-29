<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "buster","dbname" => "oneworld365","dbport" => "5432");

$db = new db($dsn,$debug = false);


if (strlen($argv[1]) < 1) die("invalid redirect input file");

$sid = 0;
$fp = fopen($argv[1], "r");


while(!feof($fp))
{
  $line = fgets($fp);
 

  $a = explode("\t",$line);
  
  if (strlen($a[0]) < 3) continue;
  
  //var_dump($a);
 
  $uri_from = trim($a[0]);
  $uri_to = trim($a[1]);
 
  //var_dump("from: ".$uri_from);
  //var_dump("to: ".$uri_to);
  
  $exists = $db->getFirstCell("SELECT 1 FROM url_map WHERE sid = ".$sid." AND url_from ='".$uri_from."'");
  
  //var_dump("Exists: ".$exists);
  
  if ($exists == 1) {
		print $sql = "DELETE FROM url_map WHERE sid = ".$sid." AND url_from ='".$uri_from."';\n";
  }

  $exists = $db->getFirstCell("SELECT 1 FROM url_map WHERE sid = ".$sid." AND url_to ='".$uri_from."'");

  if ($exists == 1) {
		print $sql = "UPDATE url_map SET url_to ='".$uri_to."' WHERE sid = ".$sid." and url_to ='".$uri_from."';\n";
  }
  
  print $sql = "INSERT INTO url_map (url_from,url_to,sid,date) VALUES ('".$uri_from."','".$uri_to."',".$sid.",now()::timestamp);\n";
}
fclose($fp);

?>
