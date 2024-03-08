<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$fp = fopen('./data/placements_by_category.csv', 'w');

$db->query("select 
p.id,
p.title,
c.title as company,
(select 1 from prod_cat_map m where p.id = m.prod_id and m.category_id = 0) as volunteer,
(select 1 from prod_cat_map m where p.id = m.prod_id and m.category_id = 2) as travel_tour,
(select 1 from prod_cat_map m where p.id = m.prod_id and m.category_id = 3) as summer_camp,
(select 1 from prod_cat_map m where p.id = m.prod_id and m.category_id = 4) as teaching,
(select 1 from prod_cat_map m where p.id = m.prod_id and m.category_id = 6) as seasonal_jobs,
(select 1 from prod_cat_map m where p.id = m.prod_id and m.category_id = 1) as work
from 
profile_hdr p, 
company c 
where 
p.company_id = c.id 
order by c.title asc;
");

$result = $db->getRows();

foreach($result as $row) {

	print_r($row);

	fputcsv($fp, $row);
	
}




?>
