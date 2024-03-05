<?php

require_once("../conf/config.php");
require_once("../classes/db_pgsql.class.php");
require_once("../classes/logger.php");
require_once("../classes/Refdata.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");

$db = new db($dsn,$debug = false);

$db->query("SELECT id, sc_gender, staff_gender, sub_type, location  FROM company WHERE (sc_gender IS NOT NULL AND sc_gender != '')  OR (staff_gender IS NOT NULL AND staff_gender != '') OR (sub_type IS NOT NULL AND sub_type != '')");

$result = $db->getRows();

//Logger::Msg($result);
//die();



foreach($result as $row) {

	$insert_sql = "UPDATE company SET profile_type = 5  WHERE id = ".$row['id'].";";
	Logger::Msg($insert_sql,'plaintext');
	
	// insert a record in profile_summercamp
	$insert_sql = "INSERT INTO profile_summercamp (company_id) VALUES (".$row['id'].");";
	Logger::Msg($insert_sql,'plaintext');

	// tag company as activity = summer camp jobs 27 / summer jobs 21
	$insert_sql = "DELETE FROM comp_act_map WHERE company_id = ".$row['id']." AND activity_id IN (27,21);";
	Logger::Msg($insert_sql,'plaintext');
	$insert_sql = "INSERT INTO comp_act_map (company_id,activity_id) VALUES (".$row['id'].",27);";
	Logger::Msg($insert_sql,'plaintext');
	$insert_sql = "INSERT INTO comp_act_map (company_id,activity_id) VALUES (".$row['id'].",21);";
	Logger::Msg($insert_sql,'plaintext');
	
	if (is_numeric(MapStaffGender($row['staff_gender']))) {
		$insert_sql = "UPDATE profile_summercamp SET staff_gender = ".MapStaffGender($row['staff_gender'])."  WHERE company_id = ".$row['id'].";";
		Logger::Msg($insert_sql,'plaintext');
	}
	
	if (is_numeric(MapCampGender($row['sc_gender']))) {
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,".MapCampGender($row['sc_gender']).");";
		Logger::Msg($insert_sql,'plaintext');
	}	
	
	ProcessCampTypeString($row['sub_type'],$row);

	$result = ProcessState($row['location']);
	
	if (is_array($result)) {
		$state_id = (is_numeric($result['state_id'])) ? $result['state_id'] : "NULL";
		$country_id = (is_numeric($result['country_id'])) ? $result['country_id'] : "NULL"; 
		
		$insert_sql = "UPDATE company SET state_id = ".$state_id.", country_id = ".$country_id."  WHERE id = ".$row['id'].";";
		Logger::Msg($insert_sql,'plaintext');
	}
}


function MapCampGender($key) {
	switch(trim($key)) {
		case 'Coed' :
			return 57;
		case 'Girls' :
			return 54;
		case 'Boys' :
			return 53;
		default :
			return NULL;		 
	}
}

function MapStaffGender($key) {
	switch(trim($key)) {
		case 'F' :
			return 134;
		case 'M' :
			return 133;
		case 'MF' :
			return 135;
		default :
			return NULL;
	}
}

function ProcessCampTypeString($str,$row) {
	
switch(trim($str)) {
case 'ADHD / Disability' :
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,56);";
		Logger::Msg($insert_sql,'plaintext');
 break; 
case 'Camp helping with aids' : 
 break; 
case 'Charity' : 
 break; 
case 'Coed Camp with Residential & Day Camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,57);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,55);";
		Logger::Msg($insert_sql,'plaintext');		
	break; 
case 'Coed Camp with Residential & Day Camp Sessions' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,57);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,55);";
		Logger::Msg($insert_sql,'plaintext');		
	break; 
case 'Coed Christian adventure programs' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,57);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,63);";
		Logger::Msg($insert_sql,'plaintext');		
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,77);";
		Logger::Msg($insert_sql,'plaintext');		
 break; 
case 'Coed Disability' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,57);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,56);";
		Logger::Msg($insert_sql,'plaintext');		
	break; 
case 'Day Camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,55);";
		Logger::Msg($insert_sql,'plaintext');		
	break; 
case 'Day Camp Disability' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,55);";
		Logger::Msg($insert_sql,'plaintext');		
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,56);";
		Logger::Msg($insert_sql,'plaintext');
	break; 
case 'Day Camp coed' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,55);";
		Logger::Msg($insert_sql,'plaintext');		
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,57);";
		Logger::Msg($insert_sql,'plaintext');				
	break; 
case 'Day camp' :
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,55);";
		Logger::Msg($insert_sql,'plaintext');		
 break; 
case 'Day camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,55);";
		Logger::Msg($insert_sql,'plaintext');		
 break; 
case 'Diabetic Camp' : 
 break; 
case 'Disability' : 
case 'Disability camp' : 
case 'Disability ccamp' :	
case 'Disability summer camp' : 
case 'Disabiltity' :	
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,56);";
		Logger::Msg($insert_sql,'plaintext');		
case 'Disability coed' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,56);";
		Logger::Msg($insert_sql,'plaintext');		
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,57);";
		Logger::Msg($insert_sql,'plaintext');
	break; 
case 'Gymnastics Camp' : 	 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,100);";
		Logger::Msg($insert_sql,'plaintext');
 	break; 
case 'Horse Riding Camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,97);";
		Logger::Msg($insert_sql,'plaintext');	
	break; 
case 'Motorsport Camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,104);";
		Logger::Msg($insert_sql,'plaintext');	
 break; 
case 'Outdoor Education Program' :
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,106);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,91);";
		Logger::Msg($insert_sql,'plaintext');		
 break; 
case 'Religious' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,61);";
		Logger::Msg($insert_sql,'plaintext');	
	break; 
case 'Religious Christian camp' :
case 'Religious Christian summer camp' :	
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,61);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,63);";
		Logger::Msg($insert_sql,'plaintext');
 break; 
case 'Residential & Day summer camp' : 
case 'Residential Camp with Day Camp Sessions' :
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,55);";
		Logger::Msg($insert_sql,'plaintext');
	break; 
case 'Residential Christian Camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,63);";
		Logger::Msg($insert_sql,'plaintext');	
	break; 
case 'Residential and Day computer camps' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,55);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,75);";
		Logger::Msg($insert_sql,'plaintext');		
	break; 
case 'Residential boys\' summer camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,53);";
		Logger::Msg($insert_sql,'plaintext');
		
	break; 
case 'Residential coed summer camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,57);";
		Logger::Msg($insert_sql,'plaintext');
	
	break; 
case 'Residential girls\' equestrian camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,54);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,97);";
		Logger::Msg($insert_sql,'plaintext');		
	break; 
case 'Residential girls\' summer camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,54);";
		Logger::Msg($insert_sql,'plaintext');
	
	break; 
case 'Residential horse riding camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,97);";
		Logger::Msg($insert_sql,'plaintext');			
	break; 
case 'Residential natural science summer camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,106);";
		Logger::Msg($insert_sql,'plaintext');			
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,83);";
		Logger::Msg($insert_sql,'plaintext');			
		
	break; 
 case 'Residential special needs camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,59);";
		Logger::Msg($insert_sql,'plaintext');
		
 	break; 
case 'Residential summer camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
	
	break; 
case 'Residential summer camp ADHD' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,59);";
		Logger::Msg($insert_sql,'plaintext');
		
	break; 
case 'Residential summer camp for gifted youth' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
	
	break; 
case 'Residential weight management camp' :
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,52);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,108);";
		Logger::Msg($insert_sql,'plaintext');		
 break; 
case 'Retreat' : 
 break; 
case 'Science Camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,83);";
		Logger::Msg($insert_sql,'plaintext');			
break; 
case 'Special Needs coed' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,59);";
		Logger::Msg($insert_sql,'plaintext');
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",1,57);";
		Logger::Msg($insert_sql,'plaintext');		
	break; 
case 'Sports Camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,85);";
		Logger::Msg($insert_sql,'plaintext');			
	break; 
case 'Tennis and Sports Camp' :
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,85);";
		Logger::Msg($insert_sql,'plaintext');		
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,87);";
		Logger::Msg($insert_sql,'plaintext');
 break; 
case 'Weight Loss Camp' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,90);";
		Logger::Msg($insert_sql,'plaintext');			
	break; 
case 'Wilderness adventure program' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,91);";
		Logger::Msg($insert_sql,'plaintext');		
	
 break; 
case 'Winter lodge' : 
		$insert_sql = "INSERT INTO refdata_map (link_to,link_id,refdata_type,refdata_id) VALUES (0,".$row['id'].",3,109);";
		Logger::Msg($insert_sql,'plaintext');		
	
	break; 
}

}


function ProcessState($location) {
	
$state_hash = array(
	array('Alabama',71,1),
	array('Alaska',71,2),
	array('Arizona',71,3),
	array('British Columbia',71,NULL),
	array('California',71,5),
	array('Canada',10,NULL),
	array('CA, TX',71,NULL),
	array('Colorado',71,6),
	array('Connecticut',71,7),
	array('District Of Columbia',71,9),
	array('Florida',71,10),
	array('Georgia',71,11),
	array('Illinois',71,14),
	array('Indiana',71,15),
	array('Iowa',71,16),
	array('Kentucky',71,18),
	array('KY', Canada,10,NULL),
	array('Maine',71,20),
	array('Maryland',71,21),
	array('Massachusetts',71,22),
	array('MD, DE',71,NULL),
	array('Michigan',71,23),
	array('Minnesota',71,24),
	array('Missouri',71,26),
	array('New Hampshire',71,30),
	array('New Jersey',71,31),
	array('New Mexico',71,32),
	array('New York',71,33),
	array('North Carolina',71,34),
	array('Ohio',71,36),
	array('Oklahoma',71,37),
	array('ON Canada',10,NULL),
	array('PA, NY, WV',71,NULL),
	array('Pennsylvania',71,39),
	array('South Carolina',71,41),
	array('Tennessee',71,43),
	array('Texas',71,44),
	array('Utah',71,45),
	array('Vermont',71,46),
	array('Virginia',71,47),
	array('Washington',71,48),
	array('West Virginia',71,49),
	array('Wisconsin',71,50),
	array('Wyoming',71,51),
);

$result = array();
$result['country_id'] = NULL;
$result['state_id'] = NULL;

foreach($state_hash as $map) {
	if ($map[0] == $location) {
		$result['country_id'] = $map[1];
		$result['state_id'] = $map[2];
		return $result;		
	}
}

}	
?>
