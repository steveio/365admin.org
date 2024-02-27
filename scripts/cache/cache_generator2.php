<?
/* Cache Generator 2 - backed cache page generator */
	
include("/www/vhosts/oneworld365.org/htdocs/conf/config.php");
include(ROOT_PATH."/classes/db_pgsql.class.php");
include(ROOT_PATH."/classes/logger.php");
include(ROOT_PATH."/classes/cache.class.php");

$db = new db($dsn,$debug = false);
$db->query("SELECT sid,uri FROM cache WHERE active = 'F' AND sid = ".$_CONFIG['site_id']);

if ($db->getNumRows() >= 1) {
	$aRows = $db->getRows();
	foreach($aRows as $aRow) {
		$sUrl = "https://www.".$brand.$aRow['uri'];
		Cache::Generate($sUrl,$aRow['uri'],$_CONFIG['site_id']);
	}
}
?>
