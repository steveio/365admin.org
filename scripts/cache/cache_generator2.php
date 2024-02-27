<?
/* Cache Generator 2 - HTML page output cache generator 
 * 
 * Reads URLs from db.cache table, invokes Cache  
 * 
 */
	
include("/www/vhosts/365admin.org/htdocs/conf/config.php");
include(ROOT_PATH."/classes/db_pgsql.class.php");
include(ROOT_PATH."/classes/logger.php");
include(ROOT_PATH."/classes/cache.class.php");

$sTargetUrl = (strlen($argv[1]) > 1) ? $argv[1] : "";
if (strlen($sTargetUrl) < 1) die("ERROR : Website URL (eg http://www.oneworld365.org) must be supplied");


$db = new db($dsn,$debug = false);
$db->query("SELECT sid,uri FROM cache WHERE active = 'F' AND sid = ".$_CONFIG['site_id']);

//print_r("Cache rows to refresh: ".$db->getNumRows())."\n";

if ($db->getNumRows() >= 1) {
	$aRows = $db->getRows();
	foreach($aRows as $aRow) {
	    $sUrl = $sTargetUrl.$aRow['uri'];
		print_r($sUrl."\n");
		Cache::Generate($sUrl,$aRow['uri'],$_CONFIG['site_id']);
	}
}
?>