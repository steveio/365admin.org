<?php
ini_set('display_errors',1);


require_once("../conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");

$dsn = array("dbhost" => "localhost","dbuser" => "postgres", "dbpass" => "buster123","dbname" => "oneworld365","dbport" => "5432");
$db = new db($dsn,$debug = false);

$fp = fopen('./data/company_profiles.csv', 'w');

$db->query("SELECT 
   c.id, 
   c.title, 
   (select distinct('x') from comp_cat_map m where m.company_id = c.id and m.category_id = 0) as volunteer,
   (select distinct('x') from comp_cat_map m where m.company_id = c.id and m.category_id = 6) as seasonaljobs,
   (select distinct('x') from comp_cat_map m where m.company_id = c.id and m.category_id = 2) as tour,
   (select distinct('x') from comp_cat_map m where m.company_id = c.id and m.category_id = 4) as teach,
   (select distinct('x') from comp_cat_map m where m.company_id = c.id and m.category_id = 3) as summercamp,
   'http://www.oneworld365.org/company/'||c.url_name as url
FROM 
   company c 
WHERE 
   prod_type >= 1 
ORDER BY c.title ASC;
");

$result = $db->getRows();

foreach($result as $row) {
	fputcsv($fp, $row);
	//print_r($row);
}

fclose($fp);


$fp = fopen('./data/placement_profiles.csv', 'w');

$db->query("SELECT 
p.id, 
c.title as company, 
p.title, 
p.ad_active as active,
   (select distinct('x') from prod_cat_map m where m.prod_id = p.id and m.category_id = 0) as volunteer,
   (select distinct('x') from prod_cat_map m where m.prod_id = p.id and m.category_id = 6) as seasonaljobs,
   (select distinct('x') from prod_cat_map m where m.prod_id = p.id and m.category_id = 2) as tour,
   (select distinct('x') from prod_cat_map m where m.prod_id = p.id and m.category_id = 4) as teach,
   (select distinct('x') from prod_cat_map m where m.prod_id = p.id and m.category_id = 3) as summercamp,
   'http://www.oneworld365.org/company/'||c.url_name||'/'||p.url_name as url
FROM 
profile_hdr p, 
company c 
WHERE 
p.company_id = c.id AND 
c.prod_type >= 1 
ORDER BY 
c.title ASC;
");

$result = $db->getRows();

foreach($result as $row) {
	fputcsv($fp, $row);
	//print_r($row);
}

fclose($fp);

//Logger::Msg($result);

?>
