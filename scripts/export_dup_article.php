<?php

/*

// postprocess with -
dos2unix ./data/destination/*
find ./data/destination/ -name "*.txt" -exec sed -i.bak $'s/\r//g' {} +
find ./data/destination/ -name "*.txt" -exec sed -i.bak -E $'s/\t//g' {} +
find ./data/destination/ -name "*.txt" -exec sed -i.bak '/^$/N;/^\n$/D' {} +
rm -f ./data/destination/*.bak

*/

require_once("/www/vhosts/oneworld365.org/htdocs/conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");
require_once(BASE_PATH."/classes/template.class.php");
require_once(BASE_PATH."/classes/file.class.php");
require_once(BASE_PATH."/classes/logger.php");
require_once(BASE_PATH."/classes/image.class.php");


require_once($_CONFIG['root_path']."/classes/link.class.php");
require_once($_CONFIG['root_path']."/classes/article.class.php");

$db = new db($dsn,$debug = false);


$arrUrlPatterns = array(
"travel" => "/travel/",
"country" => "/country/",
"continent" => "/continent/",
"tours" => "/tours/",
"volunteer" => "/volunteer/",
"jobs" => "/jobs/",
"internships" => "/internships/",
"teaching" => "/teaching/",
"tefl-courses" => "/tefl-courses/",
"study-abroad" => "/study-abroad/",
"scuba-diving" => "/scuba-diving/"
);


$db->query("
SELECT
c.url_name as destination
FROM
continent c
order by c.name asc
");

$resultContinent = $db->getRows();

$db->query("
SELECT
c.url_name as destination
FROM
country c
order by c.name asc
");

$resultCountry = $db->getRows();

//outputContent($resultContinent);
outputContent($resultCountry);

function outputContent($result)
{
	global $arrUrlPatterns, $_CONFIG;

	foreach($result as $row) {

	        $strFile = './data/destination/'.$row['destination'].'_20181029.txt';
		$fp = fopen($strFile, 'w');

		foreach($arrUrlPatterns as $key => $strUrlBase)
		{
			$strUrl = $strUrlBase.$row['destination'];
 
			try {
				$oArticle = new Article();
				$oArticle->SetFetchMode(FETCHMODE__SUMMARY);
				$oArticle->SetAttachedArticleFetchLimit(10);
				if (!$oArticle->Get($_CONFIG['site_id'],$strUrl,1)) continue;

				fputs($fp, "URL: ".$strUrl."\n");
				fputs($fp, "TITLE: ".cleanText($oArticle->GetTitle())."\n");
				fputs($fp,cleanText($oArticle->GetDescShort())."\n");
			        fputs($fp,cleanText($oArticle->GetDescFull())."\n\n\n");

	

			} catch (Exception $e) {
				
			}

		}

		fclose($fp);


	}
}

function cleanText($str){
	$str = html_entity_decode(strip_tags($str), ENT_QUOTES, 'utf-8');
	return strip_tags(preg_replace("/&#?[a-z0-9]+;/i"," ",$str));
}

?>
