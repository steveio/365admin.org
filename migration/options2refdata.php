<?php

require_once("../conf/config.php");
require_once("../classes/db_pgsql.class.php");
require_once("../classes/logger.php");
require_once("../classes/Refdata.php");

$db = new db($dsn,$debug = false);

$accom_hash = array(
	1 =>	227,
	2 =>	228,
	3 =>	229,
	4 =>	230,
	5 =>	231,
	6 =>	232,
	7 =>	233,
	8 =>	234
);

$meals_hash = array(
	9 =>	235,
	10 =>	236,
	11 =>	237,
	12 =>	238
);

$travel_hash = array(
	13 =>	239,
	14 =>	240,
	15 =>	241,
	16 =>	242,
	17 =>	243,
	18 =>	244,
	19 =>	245,
	20 =>	246,
	21 =>	247,
	22 =>	248,
	23 =>	249,
	24 =>	250
);



$db->query("SELECT m.prod_id as placement_id, m.option_id, o.type FROM prod_opt_map m, option o WHERE m.option_id = o.id");

$result = $db->getRows();

//Logger::Msg($result);

foreach($result as $row) {
	$hash_name = getHash($row['type']);
	$hash = $$hash_name;
	$refdata_id = $hash[$row['option_id']];
	$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (1,".$row['placement_id'].",".getTypeId($row['type']).",".$refdata_id.");";
	Logger::Msg($insert_sql,'plaintext');	
}


function getHash($type) {
	switch($type) {
		case "ACCOM" :
			return 'accom_hash';
		case "MEALS" :
			return 'meals_hash';
		case "TRAVEL" :
			return 'travel_hash';
	}
}

function getTypeId($type) {
	switch($type) {
		case "ACCOM" :
			return 13;
		case "MEALS" :
			return 14;
		case "TRAVEL" :
			return 15;
	}
}

?>