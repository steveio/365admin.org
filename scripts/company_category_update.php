<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");

$db = new db($dsn,$debug = false);

$path = './data/company_category.csv';

$aCategory = array(
			// CSV index => database category id
			2 => 0,
			3 => 7,
			4 => 6,
			5 => 2,
			6 => 4,
			7 => 3
		);

$csvIndex = range(2,7);

if (($handle = fopen($path, "r")) !== FALSE) {
    $i = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
	if (!is_numeric($data[0])) continue;

	print "\n\n";

	// delete existing company category mappings
	$sql = "DELETE FROM comp_cat_map WHERE company_id = ".$data[0].";";
 
	print $sql."\n";

	// insert new mappings from CSV 
	foreach($csvIndex as $index) {
		if ($data[$index] == "x") {
			unset($cat_id);
			unset($sql);
 			$cat_id = $aCategory[$index];
			$sql = "INSERT INTO comp_cat_map (company_id,category_id) values (".$data[0].",".$cat_id.");";	
			print $sql."\n";
		}
	}

	$i++;
    }
    fclose($handle);
}

?>
