<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$fp = fopen('./data/oneworld365_profile_map_edit.csv', 'r');

$a = array(1=>0,2=>2,3=>3,4=>4,5=>6,6=>7); 

while(! feof($fp))
{ 
   $line = fgetcsv($fp);
   $id = $line[0];
   if(!is_numeric($id)) continue;
   print "DELETE FROM prod_cat_map WHERE prod_id = ".$id.";\n";
   for($y=1;$y<=6;$y++) {
      if ($line[$y] == 1) {
	  //print_r($line)."\n";
          print "INSERT INTO prod_cat_map (prod_id,category_id) VALUES (".$id.",".$a[$y].");\n";
      }
   }
}

fclose($fp);



?>
