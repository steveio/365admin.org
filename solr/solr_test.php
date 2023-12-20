<?php

ini_set('display_errors',0);
ini_set('log_errors', 1);
ini_set('error_log', '/www/vhosts/365admin.org/logs/365admin_error.log');
error_reporting(E_ALL & ~E_NOTICE & ~ E_STRICT);


$config = array(
		'adapteroptions' => array(
				'host' => '127.0.0.1',
				'port' => 8983,
				'path' => '/solr/',
		)
);


require('../vendor/solarium/solarium/library/Solarium/Autoloader.php');
Solarium_Autoloader::register();



// check solarium version available
echo 'Solarium library version: ' . Solarium_Version::VERSION . ' - ';


// create a client instance
$client = new Solarium_Client($config);


// create a ping query
$ping = $client->createPing();

// execute the ping query
try{
	$result = $client->ping($ping);
	echo 'Ping query successful';
	echo '<br/><pre>';
	var_dump($result->getData());
}catch(Solarium\Exception $e){
	echo 'Ping query failed';
}


?>
