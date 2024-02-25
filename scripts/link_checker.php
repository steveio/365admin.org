<?php 

require_once("../conf/config.php");
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

$oLinkChecker->Setup();
//$oLinkChecker->GetCompanyLinkStatus();
$oLinkChecker->GetPlacementLinkStatus();



print_r("END Processing: Link Status Report ( ".date("Y-m-d H:i:s")." ) \n\n");

?>