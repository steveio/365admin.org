<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$path = './data/placement_category.csv';

$aCategory = array(
			// CSV index => database category id
			3 => 0,
			4 => 7,
			5 => 6,
			6 => 2,
			7 => 4,
			8 => 3
		);

$csvIndex = range(2,7);
$comp_updated = array();


if (($handle = fopen($path, "r")) !== FALSE) {
    $i = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
	if (!is_numeric($data[0])) continue;

	//print "Processing row: ".$i."\n";
	
	// get company id 
	$sql = "SELECT company_id FROM profile_hdr WHERE id = ".$data[0];
	$company_id = $db->getFirstCell($sql);

	if (!is_numeric($company_id)) continue;

	print "\n\n";

	// delete existing placement category mappings
	$sql = "DELETE FROM prod_cat_map WHERE prod_id = ".$data[0].";";
 
	print $sql."\n";

	// insert new mappings from CSV 
	foreach($csvIndex as $index) {
		if ($data[$index] == "x") {
			unset($cat_id);
			unset($sql);
 			$cat_id = $aCategory[$index];
			$sql = "INSERT INTO prod_cat_map (prod_id,category_id) values (".$data[0].",".$cat_id.");";	
			print $sql."\n";

			// check that the company is also in the same category as the placement
			$sql = "SELECT 1 FROM comp_cat_map WHERE company_id = ".$company_id." AND category_id = ".$cat_id.";";
			//print $sql."\n";
			$db->query($sql);
			if ($db->getNumRows() != 1) {
				if (!isset($comp_updated[$company_id][$cat_id])) {
 					$sql = "INSERT INTO comp_cat_map (company_id,category_id) VALUES (".$company_id.",".$cat_id.");";
					print $sql."\n";
					$comp_updated[$company_id][$cat_id] = TRUE;
				}
			}			 

		}
	}

	$i++;
    }
    fclose($handle);
}

?>
