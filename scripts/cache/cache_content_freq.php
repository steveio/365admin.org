<?

include("/www/vhosts/oneworld365.org/htdocs/conf/config.php");
include(ROOT_PATH."/classes/db_pgsql.class.php");
include(ROOT_PATH."/classes/logger.php");
include(ROOT_PATH."/classes/cache.class.php");

$db = new db($dsn,$debug = false);


$sUrl = $_CONFIG['url'];
$sPath = ROOT_PATH;
$sUri = "/";
$id = $_CONFIG['site_id'];

Cache::Generate($sUrl,$sUri,$id);

?>