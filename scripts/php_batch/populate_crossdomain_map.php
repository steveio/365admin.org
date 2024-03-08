<?php

/*
 * 
 * CREATE TABLE cross_domain_map (
    	request_uri_from character varying(256),
    	site_id smallint,
    	request_uri_to character varying(256)
	);
 * 
 * 
 */

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$path = './data/crossdomain_mappings.csv';

$aMapping= array();


if (($handle = fopen($path, "r")) !== FALSE) {
	$i = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // request_uri_from	site_id	request_uri_to
                if (strlen(trim($data[0])) < 1) continue;
                $aMapping[$i] = array("REQUEST_URI_FROM" => trim($data[0]), 
                					"SITE_ID" => $data[1],
                					"REQUEST_URI_TO" => trim($data[2])
                					);
                $sql = "INSERT INTO cross_domain_map (request_uri_from, site_id, request_uri_to) VALUES ('".$aMapping[$i]['REQUEST_URI_FROM']."',".$aMapping[$i]['SITE_ID'].",'".$aMapping[$i]['REQUEST_URI_TO']."');";
                Logger::Msg($sql);
                Logger::Msg("\n\n");
				$db->query($sql);
				$i++;
        }
    fclose($handle);
}

//Logger::Msg($aMapping);

?>
