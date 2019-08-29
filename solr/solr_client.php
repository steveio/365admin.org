<?php

ini_set('display_errors',1);
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




// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');


$solr = new Apache_Solr_Service();

$query = "your query";
$start = 0;
$rows = 10;

$additionalParameters = array(
		'fq' => 'a filtering query',
		'facet' => 'true',
		// notice I use an array for a muti-valued parameter
		'facet.field' => array(
				'field_1',
				'field_2'
		)
);



?>