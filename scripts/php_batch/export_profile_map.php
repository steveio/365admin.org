<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$fp = fopen('./data/profile_map_11022014.csv', 'w');

$db->query("
SELECT 
p.id,
p.title as PROFILE_TITLE,
c.title as COMPANY,
(SELECT 1 FROM prod_cat_map m WHERE m.prod_id = p.id AND m.category_id = 0) as VOLUNTEER,
(SELECT 1 FROM prod_cat_map m WHERE m.prod_id = p.id AND m.category_id = 2) as TRAVEL_TOUR,
(SELECT 1 FROM prod_cat_map m WHERE m.prod_id = p.id AND m.category_id = 3) as SUMMER_CAMP,
(SELECT 1 FROM prod_cat_map m WHERE m.prod_id = p.id AND m.category_id = 4) as TEACHING,
(SELECT 1 FROM prod_cat_map m WHERE m.prod_id = p.id AND m.category_id = 6) as SEASONAL_JOBS,
(SELECT 1 FROM prod_cat_map m WHERE m.prod_id = p.id AND m.category_id = 7) as GAP_YEAR
FROM
profile_hdr p,
company c
WHERE p.company_id = c.id
ORDER BY c.title ASC, p.title ASC;
");

$result = $db->getRows();

foreach($result as $row) {

        print_r($row);

        fputcsv($fp, $row);

}




?>

