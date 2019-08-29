<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$fp = fopen('./data/comp_mappings_20012014.csv', 'w');

$db->query("
SELECT 
c.id,
c.title, 
(SELECT 1 FROM company WHERE id IN (SELECT company_id FROM comp_cat_map WHERE category_id = 0 AND company_id = c.id)) as VOLUNTEER,
(SELECT 1 FROM company WHERE id IN (SELECT company_id FROM comp_cat_map WHERE category_id = 7 AND company_id = c.id)) as GAP_YEAR,
(SELECT 1 FROM company WHERE id IN (SELECT company_id FROM comp_cat_map WHERE category_id = 2 AND company_id = c.id)) as TRAVEL_TOUR,
(SELECT 1 FROM company WHERE id IN (SELECT company_id FROM comp_cat_map WHERE category_id = 6 AND company_id = c.id)) as SEASONAL_JOBS,        
(SELECT 1 FROM company WHERE id IN (SELECT company_id FROM comp_cat_map WHERE category_id = 4 AND company_id = c.id)) as TEACHING,
(SELECT 1 FROM company WHERE id IN (SELECT company_id FROM comp_cat_map WHERE category_id = 3 AND company_id = c.id)) as SUMMER_CAMP
FROM
company c 
WHERE prod_type >= 1;
");

$result = $db->getRows();

foreach($result as $row) {

        print_r($row);

        fputcsv($fp, $row);

}




?>

