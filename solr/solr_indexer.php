<?php

/*
 * solr_indexer.php
 * SOLR Batch Indexer is an asyncronous offline processor
 * responsible for indexing profile data 
 * via solarium / SOLR Lucene 
 * 
 * It is called from command line, usually via cron
 * and takes a single principal parameter 'mode;
 * representing either full reindex or incremental update
 * 
 * Useage-
 *    php solr_indexer.php { ALL | DELTA }
 *    
 * An optional second argument can be supplied to index only one content type- 
 *    php solr_indexer.php { ALL | DELTA } { PLACEMENT | COMPANY | ARTICLE }
 * 
 * The indexer uses the Logger class an outputs to 365admin.org/logs/365_indexer.log
 * 
 */

ini_set('display_errors',1);
ini_set('log_errors', 1);
ini_set('error_log', '/www/vhosts/365admin.org/logs/365admin_indexer.log');
error_reporting(E_ALL & ~E_NOTICE & ~ E_STRICT);

define("LOG", TRUE);
define('LOG_PATH',"/www/vhosts/365admin.org/logs/365admin_indexer.log");
define("JOBNAME","SOLR_INDEXER");

// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');


$debug = false;

$path = "/www/vhosts/oneworld365.org/htdocs";

include($path."/conf/config.php");
include($path."/classes/db_pgsql.class.php");
include($path."/classes/template.class.php");
include($path."/classes/activity.class.php");
include($path."/classes/category.class.php");
include($path."/classes/country.class.php");
include($path."/classes/stemmer.class.php");
include($path."/classes/indexer.class.php");
include($path."/classes/file.class.php");
include($path."/classes/logger.php");
include($path."/classes/image.class.php");
include($path."/classes/review.class.php");

/* legacy company/placement classes - methods to return id lists */
include($path."/classes/company.class.php");
include($path."/classes/placement.class.php");

/* Profile System */
require_once($_CONFIG['root_path']."/classes/ProfileInterface.php");
require_once($_CONFIG['root_path']."/classes/ProfileAbstract.class.php");
require_once($_CONFIG['root_path']."/classes/ProfileFactory.class.php");
require_once($_CONFIG['root_path']."/classes/ProfilePlacement.class.php");
require_once($_CONFIG['root_path']."/classes/ProfileCompany.class.php");
require_once($_CONFIG['root_path']."/classes/ProfileGeneral.class.php");
require_once($_CONFIG['root_path']."/classes/ProfileTour.class.php");
require_once($_CONFIG['root_path']."/classes/ProfileJob.class.php");
require_once($_CONFIG['root_path']."/classes/ProfileSummerCamp.php");
require_once($_CONFIG['root_path']."/classes/ProfileVolunteerTravelProject.php");
require_once($_CONFIG['root_path']."/classes/ProfileTeachingProject.php");
require_once($_CONFIG['root_path']."/classes/ProfileSeasonalJobsEmployer.php");
require_once($_CONFIG['root_path']."/classes/Refdata.php");


// global exception catcher
function exception_handler($e) {
	if (LOG) Logger::DB(1,JOBNAME.' UNCAUGHT EXCEPTION: '.$e->getMessage());
	die($e->getMessage());
}

set_exception_handler('exception_handler');



/* SOLR Search Engine */
require_once("/www/vhosts/365api.org/htdocs/classes/SolrSearch.php");
require_once("/www/vhosts/365api.org/htdocs/classes/SolrPlacementSearch.php");
require_once("/www/vhosts/365api.org/htdocs/classes/SolrCompanySearch.php");
require_once("/www/vhosts/365api.org/htdocs/classes/SolrIndexer.php");


// Solarium
$solr_config = array(
    'adapteroptions' => array(
        'host' => '127.0.0.1',
        'port' => 8983,
        'path' => '/solr/collection1/'
        
    )
);

//require('/www/vhosts/365admin.org/htdocs/vendor/solarium/solarium/library/Solarium/Autoloader.php');
//Solarium_Autoloader::register();

// Try to connect to the named server, port, and url
// create a client instance
$client = new Solarium\Client($solr_config);
$client->getEndpoint('localhost')->setCore('collection1');


// create a ping query
$ping = $client->createPing();

// execute the ping query
try{
	$result = $client->ping($ping);
}catch(Solarium\Exception $e){
	if (LOG) Logger::DB(1,JOBNAME,'SOLR PING FAILED: '.implode($solr_config));
	die('SOLR ping failed');
}




// Do not use the site specific views, use the base tables
$_CONFIG['company_table'] = "company";
$_CONFIG['placement_table'] = "profile_hdr";


$db = new db($dsn,false);


define('REFDATA_US_STATE',0);
define('REFDATA_CAMP_TYPE',1);
define('REFDATA_CAMP_JOB_TYPE',2);
define('REFDATA_ACTIVITY',3);
define('REFDATA_INT_RANGE',4);
define('REFDATA_DURATION',5);
define('REFDATA_ORG_SUBTYPE',6);
define('REFDATA_BONDING',7);
define('REFDATA_STAFF_ORIGIN',8);
define('REFDATA_GENDER',9);
define('REFDATA_APPROX_COST',10);
define('REFDATA_HABITATS',11);
define('REFDATA_SPECIES',12);
define('REFDATA_ACCOMODATION',13);
define('REFDATA_MEALS',14);
define('REFDATA_TRAVEL_TRANSPORT',15);
define('REFDATA_ADVENTURE_SPORTS',16);
define('REFDATA_ORG_PROJECT_TYPE',17);
define('REFDATA_CURRENCY',18);
define('REFDATA_JOB_OPTIONS',19);
define('REFDATA_INT_SMALL_RANGE',20);
define('REFDATA_JOB_CONTRACT_TYPE',21);
define('REFDATA_US_REGION',22);
define('REFDATA_AGE_RANGE',23);
define('REFDATA_RELIGION',24);
define('REFDATA_CAMP_GENDER',25);



$mode = (strlen($argv[1]) > 1) ? $argv[1] : $_GET['mode'];
if (strlen($mode) < 1) die("ERROR : Mode (ALL || DELTA || SINGLE) must be supplied");

// Optional 2nd runtime parameter { ALL | COMPANY | PLACEMENT | ARTICLE }
$type = $argv[2];
if (strlen($type) < 1) $type = "ALL";

// Optional 3rd runtime parameter id 
$id = $argv[3];
if (($mode == "SINGLE") && (!is_numeric($id) || $id === null)) die("An ID must be supplied in SINGLE index mode");

$debug = false;

if (LOG) Logger::DB(3,JOBNAME,'STARTED PROCESSING mode: '.$mode);

// optionally reset status flag
$db->query("UPDATE indexer SET status = 'f';");

// are we already running?
// indexer not designed for parralel execution, should be single process
$status = $db->getFirstCell("SELECT status FROM indexer;");

if ($status == "t") {
	if (LOG) Logger::DB(2,JOBNAME,'INDEXER STATUS CHECK FAIL');
	die();
}

// set status to processing
$db->query("UPDATE indexer SET status = 't';");



if (strtoupper($mode) == "ALL") {
	$key = "INDEX_LIST_ALL";
} elseif (strtoupper($mode) == "DELTA") {
	$key = "INDEX_LIST_DELTA_SOLR";
} elseif ($mode == "SINGLE") {

} else {
	if (LOG) Logger::DB(2,JOBNAME,'INVALID MODE ('.$mode.') SPECIFIED not ALL or DELTA');
	die();
}



if (in_array($type,array("ALL", "COMPANY"))) {

        if (LOG) Logger::DB(2,JOBNAME,'BEGIN PROCESSING COMPANY');

	$aCompany = array();
	$oCompany = new Company($db);

	if ($mode == "SINGLE") {
		$aId = array($id => array("id" => $id));
	} else {	
		$aId = $oCompany->GetCompanyList($key);
	}
	
	if (is_array($aId) && count($aId) >= 1) {
		if (LOG) Logger::DB(2,JOBNAME,'FOUND '.count($aId).' COMPANY');
		$oSolrIndexer = new SolrIndexer();
		$oSolrIndexer->debug = $debug;
		$oSolrIndexer->setId($aId);
		$oSolrIndexer->indexCompany();
	}

        //if (LOG) Logger::DB(2,JOBNAME,'END PROCESSING COMPANY');

	
} // end if process compa



//////////////////////////////////////////////////////////////////////////////////////



if (in_array($type,array("ALL", "PLACEMENT"))) {
	
	if (LOG) Logger::DB(2,JOBNAME,'BEGIN PROCESSING PLACEMENTS');
	
	$oPlacement = new Placement($db);

        if ($mode == "SINGLE") {
                $aId = array($id => array("id" => $id, 'type' => "2"));
        } else {
		  $aId = $oPlacement->GetPlacementById($id,$key,$ret_type = "rows");
        }

	if (is_array($aId) && count($aId) >= 1) {
		$oSolrIndexer = new SolrIndexer;
		$oSolrIndexer->debug = $debug;
		$oSolrIndexer->setId($aId);
		$oSolrIndexer->indexPlacement();		
		$oSolrIndexer->reindexPlacementWithExtras();
	}

        //if (LOG) Logger::DB(2,JOBNAME,'END PROCESSING PLACEMENTS');

	
} // end if process placements


//////////////////////////////////////////////////////////////////////////////////////

if (in_array($type,array("ALL", "ARTICLE"))) {

        if (LOG) Logger::DB(2,JOBNAME,'BEGIN PROCESSING ARTICLES');

	require_once($_CONFIG['root_path']."/classes/link.class.php");
	require_once($_CONFIG['root_path']."/classes/article.class.php");

        if ($mode == "SINGLE") {
                $aId = array($id => array("id" => $id, 'type' => "2"));
        } else {
	
		$oArticle = new Article();
		$aFilter = array("URI" => "%");
		if (strtoupper($mode) == "DELTA") {
			$aFilter['LAST_INDEXED'] = TRUE;
		}
		$aId = $oArticle->GetAll($aFilter,$fields = "a.id",$fetch = FALSE);
        }

	if (is_array($aId) && count($aId) >= 1) {
		
		$oSolrIndexer = new SolrIndexer;
		$oSolrIndexer->debug = $debug;
		$oSolrIndexer->setId($aId);
		$oSolrIndexer->indexArticle();
	
	}
	
        //if (LOG) Logger::DB(2,JOBNAME,'END PROCESSING ARTICLES');

}
	
//////////////////////////////////////////////////////////////////////////////////////

// set status to not processing
$db->query("UPDATE indexer SET status = 'f';");


if (LOG) Logger::DB(3,JOBNAME,'FINISHED PROCESSING');

unset($aCompany,$oCompany,$aPlacement,$oPlacement,$oIndexer,$db);
unset($c,$p);
unset($status,$debug);


?>
