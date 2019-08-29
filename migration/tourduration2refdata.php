<?php

require_once("../conf/config.php");
require_once("../classes/db_pgsql.class.php");
require_once("../classes/logger.php");
require_once("../classes/Refdata.php");

$db = new db($dsn,$debug = false);

$db->query("SELECT p_hdr_id as placement_id, duration FROM profile_tour");

$result = $db->getRows();

/*
	1 => "1-7 days",
	2 => "1-2 weeks",
	3 => "2-4 weeks",
	4 => "1-2 months",
	5 => "2-3 months",
	6 => "> 3 months"
	
116	 < 1 week
117	 1 week
118	 2 weeks
119	 3 weeks
120	 4 weeks
121	 6 weeks
122	 2 months
123	 3 months
124	 4 months
125	 6 months
126	 1 year
127	 > 1 year
	
*/


foreach($result as $row) {
	
	if ($row['duration'] == 1) {
		$insert_sql = "UPDATE profile_tour SET duration_from_id = 116, duration_to_id = 117  WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');		
	}

	if ($row['duration'] == 2) {
		$insert_sql = "UPDATE profile_tour SET duration_from_id = 117, duration_to_id = 118  WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');		
	}

	if ($row['duration'] == 3) {
		$insert_sql = "UPDATE profile_tour SET duration_from_id = 118, duration_to_id = 120  WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');		
	}

	if ($row['duration'] == 4) {
		$insert_sql = "UPDATE profile_tour SET duration_from_id = 120, duration_to_id = 122  WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');		
	}

	if ($row['duration'] == 5) {
		$insert_sql = "UPDATE profile_tour SET duration_from_id = 122, duration_to_id = 123  WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');		
	}

	if ($row['duration'] == 6) {
		$insert_sql = "UPDATE profile_tour SET duration_from_id = 123, duration_to_id = 126  WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');		
	}
	
}



?>