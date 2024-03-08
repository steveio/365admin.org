<?php 

/**
 * CRON script to check and report external link HTTP status
 * 
 * Scheduled execution monthly
 * 
 * Useage:  
 *  [web_developer@cloud-vps htdocs]$ php ./scripts/link_checker.php 2>&1 | tee  ../logs/365admin_link_status.log
 * 
 * 
 * 
 */

require_once("/www/vhosts/365admin.org/htdocs/conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/file.class.php");
require_once(BASE_PATH."/classes/LinkChecker.class.php");


print_r("BEGIN Processing: Link Status Report ( ".date("Y-m-d H:i:s")." ) \n\n");

$db = new db($dsn,$debug = false);
print_r(var_dump($db->db));
print_r("\n\n");

$oLinkChecker = new LinkChecker();

$sDate = date("y-m")."-01";

$oLinkChecker->SetReportDate($sDate);

$oLinkChecker->Process();


//print $oLinkChecker->GetLinkHTTPResponseStatus("https://www.");



print_r("END Processing: Link Status Report ( ".date("Y-m-d H:i:s")." ) \n\n");

?>