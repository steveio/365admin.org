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


$strCountryList = <<<EOF
Benin
Botswana 
Bukino Faso
Cameroon
Cape Verde
Cote D'Ivoire
Egypt 
Ethiopia
Gambia 
Ghana 
Kenya 
Lesotho
Liberia
Libya
Madagascar 
Malawi 
Mali 
Mauritania 
Morocco 
Mozambique 
Namibia 
Nigeria
Rwanda
Senegal 
Seychelles
Sierra Leone
Swaziland 
Tanzania 
Togo
Uganda 
Zambia 
Zanzibar
Zimbabwe 
Bahrain
Bangladesh
Brunei 
Cambodia 
East Timor
Hong Kong
Iran
Israel
Jordan
Kuwait
Laos
Lebanon 
Maldives
Mongolia 
Myanmar
Oman
Pakistan
Palestine
Philippines 
Qatar
Saudi Arabia 
Taiwan
Tibet 
United Arab Emirates
Vietnam 
Papua New Guinea 
Tonga
Vanuatu 
Bahamas 
Barbados
Belize  
Cuba
Dominica 
Dominican Republic 
Guatemala 
Haiti
Honduras 
Jamaica
Mauritius 
Nicaragua
Panama
St Vincent
Tobago
Trinidad 
West Indies
Fiji 
El Salvador
Falkland Islands
Galapagos Islands
Guyana 
Paraguay 
Uruguay
Venezuela 
Belgium
Albania
Armenia
Bosnia 
Bulgaria
Finland 
Kosovo
Latvia 
Lithuania
Malta
Moldova 
Romania 
Serbia
Svalbard
Turkey 
Ukraine 
Poland 
Cyprus
EOF;

//print_r($strCountryList);
$arrCountryInScope = explode("\n",$strCountryList);

$arrUrlPatterns = array(
"country" => "/country/",
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
	global $arrCountryInScope, $arrUrlPatterns, $_CONFIG;

	foreach($result as $row) {

		if (!in_array($row['name'],$arrCountryInScope)) continue;

		print_r("-- Country: ".$row['name']."\n");

		$strFile = './data/destination/'.$row['url_name'].'_20181029.txt';
		$fp = fopen($strFile, 'w');
		
		$oArticle = new Article();
		$oArticle->SetFetchMode(FETCHMODE__SUMMARY);
		$oArticle->SetAttachedArticleFetchLimit(10);
		$strUrl = "/travel/".$row['url_name'];
		if (!$oArticle->Get($_CONFIG['site_id'],$strUrl,1)) continue;
		
		foreach($arrUrlPatterns as $key => $strRelatedUrlBase)
		{
			$strRelatedUrl = $strRelatedUrlBase.$row['url_name'];
 
			try {
				$oRelatedArticle = new Article();
				$oRelatedArticle->SetFetchMode(FETCHMODE__SUMMARY);
				$oRelatedArticle->SetAttachedArticleFetchLimit(10);
				if (!$oRelatedArticle->Get($_CONFIG['site_id'],$strRelatedUrl,1)) continue;

				if (in_array($key,array("tours","volunteer")))
				    $oArticle->SetDescFull($oArticle->GetDescFull()."\n".$oRelatedArticle->GetDescFull());

				// -- delete article_map
				$strSql = "delete from article where id = (select article_id from article_map where section_uri = '".$strRelatedUrl."');";

				print_r($strSql."\n");
				
				// -- delete article
				$strSql = "delete from article_map where section_uri = '".$strRelatedUrl."';";

				print_r($strSql."\n");

				// -- insert 301 redirect
				$strSql = "insert into url_map (url_from,url_to,sid) values ('".$strRelatedUrl."','/travel/".$row['url_name']."',0);";

				print_r($strSql."\n");
	

			} catch (Exception $e) {
				
			}

		}

		$oArticle->SetTitle($row['name']." Travel Guide, Gap Year Volunteering and Tours");
		fputs($fp,$oArticle->GetTitle());
		fputs($fp,$oArticle->GetDescFull());
		
		$response = array();
		$oArticle->Save($response);

		fclose($fp);

		print_r("\n\n");

	}
}

function cleanText($str){
	$str = html_entity_decode(strip_tags($str), ENT_QUOTES, 'utf-8');
	return strip_tags(preg_replace("/&#?[a-z0-9]+;/i"," ",$str));
}

?>
