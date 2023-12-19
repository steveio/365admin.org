<?php


require_once("/www/vhosts/oneworld365.org/htdocs/conf/config.php");
require_once(BASE_PATH."/classes/db_pgsql.class.php");
require_once(BASE_PATH."/classes/logger.php");
require_once(BASE_PATH."/classes/template.class.php");
require_once(BASE_PATH."/classes/file.class.php");
require_once(BASE_PATH."/classes/logger.php");
require_once(BASE_PATH."/classes/image.class.php");


require_once($_CONFIG['root_path']."/classes/link.class.php");
require_once($_CONFIG['root_path']."/classes/article.class.php");
require_once($_CONFIG['root_path']."/classes/validation.class.php");
require_once($_CONFIG['root_path']."/classes/cache.class.php");

$db = new db($dsn,$debug = false);



$db->query("
SELECT
c.name,
c.url_name
FROM
country c
order by c.name asc
");

$resultCountry = $db->getRows();

outputContent($resultCountry);


function outputContent($result)
{
	global $_CONFIG;

	foreach($result as $row) {

		$strUrl = "/travel/".$row['url_name'];

		$oArticle = new Article();
		$oArticle->SetFetchMode(FETCHMODE__SUMMARY);
		$oArticle->SetAttachedArticleFetchLimit(10);
		if (!$oArticle->Get($_CONFIG['site_id'],$strUrl,1)) continue;
		
		try {
		    $strRelatedUrl = "/tours/".$row['url_name'];
			$oRelatedArticle = new Article();
			$oRelatedArticle->SetFetchMode(FETCHMODE__SUMMARY);
			$oRelatedArticle->SetAttachedArticleFetchLimit(10);
			if (!$oRelatedArticle->Get($_CONFIG['site_id'],$strRelatedUrl,1)) continue;

			//$strFile = './data/destination/'.$row['url_name'].'_20181029.txt';
			//$fp = fopen($strFile, 'w');
			
			print_r("-- Country: ".$row['name']."\n");

		    $oArticle->SetDescFull($oArticle->GetDescFull()."\n".$oRelatedArticle->GetDescFull());

			// -- delete article
			$strSql = "delete from article where id = (select article_id from article_map where section_uri = '".$strRelatedUrl."');";

			print_r($strSql."\n");
			
			// -- delete article map
			$strSql = "delete from article_map where section_uri = '".$strRelatedUrl."';";

			print_r($strSql."\n");

			// -- insert 301 redirect
			$strSql = "insert into url_map (url_from,url_to,sid) values ('".$strRelatedUrl."','".$strUrl."',0);";

			print_r($strSql."\n");


		} catch (Exception $e) {
			
		}

		$oArticle->SetTitle(trim($row['name'])." Travel & Tours");
		$response = array();
		$oArticle->Save($response);
		//fputs($fp,$oArticle->GetTitle());
		//fputs($fp,$oArticle->GetDescFull());
		
		//fclose($fp);

		print_r("\n\n");

	}
}

function cleanText($str){
	$str = html_entity_decode(strip_tags($str), ENT_QUOTES, 'utf-8');
	return strip_tags(preg_replace("/&#?[a-z0-9]+;/i"," ",$str));
}

?>
