<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$fp = fopen('./data/redirects_20012014.csv', 'w');

$db->query("
select
url_from,
url_to,
sid
from url_map ORDER BY sid asc;
");

$result = $db->getRows();

foreach($result as $row) {

        print_r($row);

        fputcsv($fp, $row);

}




?>

