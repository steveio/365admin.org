<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");

$db = new db($dsn,$debug = false);

$path = './data/cat_act_reorg.csv';

$arrCategoryId = array(
	2 => 6,
	3 => 3,
	4 => 4,
	5 => 2,
	6 => 0,
	7 => 8,
	8 => 11,
	9 => 12   		
);


if (($handle = fopen($path, "r")) !== FALSE) {
    $i = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
    	if ($i < 2) {
    		$i++; 
    		continue;
    	}
	
		$activity_id = $data[0];

		for($i=2;$i<10;$i++)
		{
			if (trim($data[$i]) == "x")
			{
				$category_id = $arrCategoryId[$i];
				print "INSERT INTO cat_act_map (category_id, activity_id) values (".$category_id.",".$activity_id.");\n";
			}
		}
		
		$i++;
    }
    fclose($handle);
}

?>
