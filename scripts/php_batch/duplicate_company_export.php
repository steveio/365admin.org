<?php

require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "p0stgr3s","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$fp = fopen('./data/comps_by_category_free.csv', 'w');

$db->query(" 
select 
c.id,
c.title,
c.prod_type as listing_level,
(select 1 from comp_cat_map m where c.id = m.company_id and m.category_id = 0) as volunteer,
(select 1 from comp_cat_map m where c.id = m.company_id and m.category_id = 2) as travel_tour,
(select 1 from comp_cat_map m where c.id = m.company_id and m.category_id = 3) as summer_camp,
(select 1 from comp_cat_map m where c.id = m.company_id and m.category_id = 4) as teaching,
(select 1 from comp_cat_map m where c.id = m.company_id and m.category_id = 6) as seasonal_jobs,
(select 1 from comp_cat_map m where c.id = m.company_id and m.category_id = 1) as work
from company c
where 
c.prod_type < 1
order by listing_level desc, c.title asc;
");

$result = $db->getRows();

foreach($result as $row) {

	print_r($row);

	fputcsv($fp, $row);
	
}




?>
