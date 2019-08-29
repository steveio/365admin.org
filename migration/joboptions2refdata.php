<?php

require_once("../conf/config.php");
require_once("../classes/db_pgsql.class.php");
require_once("../classes/logger.php");
require_once("../classes/Refdata.php");

$db = new db($dsn,$debug = false);

$db->query("SELECT p_hdr_id as placement_id, live_in,meals_inc, pickup_inc, contract_type FROM profile_job");

$result = $db->getRows();

//Logger::Msg($result);

$refdata_type = 19;

$hash = array(
 "live_in" => 293,
 "meals_inc" => 295,
 "pickup_inc" => 296,
 "full_time" => 306,
 "part_time" => 307
);



foreach($result as $row) {
	
	if ($row['live_in'] == 't') {
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (1,".$row['placement_id'].",19,".$hash['live_in'].");";
		Logger::Msg($insert_sql,'plaintext');
	}	
	if ($row['meals_inc'] == 't') {
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (1,".$row['placement_id'].",19,".$hash['meals_inc'].");";
		Logger::Msg($insert_sql,'plaintext');
	}	
	if ($row['pickup_inc'] == 't') {
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (1,".$row['placement_id'].",19,".$hash['pickup_inc'].");";
		Logger::Msg($insert_sql,'plaintext');
	}

	if ($row['contract_type'] == 1) {
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (1,".$row['placement_id'].",21,".$hash['full_time'].");";
		Logger::Msg($insert_sql,'plaintext');
		
		$insert_sql = "UPDATE profile_job SET duration_from_id = 126, duration_to_id = 127  WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');
		
	}
	
	if ($row['contract_type'] == 2) {
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (1,".$row['placement_id'].",21,".$hash['part_time'].");";
		Logger::Msg($insert_sql,'plaintext');
		
		$insert_sql = "UPDATE profile_job SET duration_from_id = 126, duration_to_id = 127  WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');
		
	}

	if ($row['contract_type'] == 3) {
		$insert_sql = "UPDATE profile_job SET duration_from_id = 117, duration_to_id = 120 WHERE p_hdr_id = ".$row['placement_id'];
		Logger::Msg($insert_sql,'plaintext');		
	}

	if ($row['contract_type'] == 4) {
		$insert_sql = "UPDATE profile_job SET duration_from_id = 120, duration_to_id = 123 WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');		
	}

	if ($row['contract_type'] == 5) {
		$insert_sql = "UPDATE profile_job SET duration_from_id = 123, duration_to_id = 125 WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');		
	}

	if ($row['contract_type'] == 6) {
		$insert_sql = "UPDATE profile_job SET duration_from_id = 125, duration_to_id = 127 WHERE p_hdr_id = ".$row['placement_id'].";";
		Logger::Msg($insert_sql,'plaintext');		
	}
	
}


// job contract type 
/*
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

$contract_type = array(
	1 => 'Full Time',
	2 => 'Part Time',
	3 => 'Fixed Term Contract < 1 month',
	4 => 'Fixed Term Contract 1-3 months',
	5 => 'Fixed Term Contract 3-6 months',
	6 => 'Fixed Term Contract > 6 months'
);
*/

?>