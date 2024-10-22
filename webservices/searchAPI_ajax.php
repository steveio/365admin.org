<?php

/*
 * Search API AJAX - an endpoint for searching / retrieving profiles (companies / placements) & articles
 * 
 * This API serves results based on relational DB query (rather than SOLR API)
 * 
 * @param $_GET['stype'] Search Type : 0 = Uri, 1 = Keyword  
 * @param $_GET['uri'] Uri (relative path)
 * @param $_GET['keywords'] Search Keywords
 * @param $_GET['filter'] 
 * @param $_GET['template'] template: path to PHP file in /templates/
 *
 */

require_once("../conf/config.php");
require_once("../conf/brand_config.php");
require_once("../classes/Brand.php");
require_once("../classes/session.php");
require_once("../classes/user.class.php");
require_once("../classes/authenticate.class.php");
require_once("../classes/logger.php");
require_once("../classes/json.class.php");
require_once("../classes/db_pgsql.class.php");
require_once("../classes/file.class.php");
require_once("../classes/logger.php");
require_once("../classes/Message.php");
require_once("../classes/template.class.php");
require_once("../classes/link.class.php");
require_once("../classes/article.class.php");
require_once("../classes/ContentMapping.class.php");
require_once("../classes/ArticleCollection.class.php");
require_once("../classes/company.class.php");
require_once("../classes/placement.class.php");



$db = new db($dsn,$debug = false);


if (!is_object($oAuth))
{
    /* setup an instance of session authentication */
    $oAuth = new Authenticate($db,$redirect = TRUE, "/".ROUTE_LOGIN, COOKIE_NAME);
    $oAuth->ValidSession();
}



if (!is_object($oBrand))
{
    $oBrand = new Brand($aBrandConfig[HOSTNAME]);
}


$oSession = Session::initSession();

$aResult = array();
$aResponse = array();

$aResponse['retVal'] = false;
$aResponse['msg'] = "";

$exp = $_GET['exp'];
$ctype = $_GET['ctype'];
$exact = $_GET['exact'];
$filterDate = $_GET['filterDate'];
$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];
$fuzzy = true;
$bUnpublished = false;

// Validate Input Params
if (preg_match("/[^a-zA-Z0-9\/ _\-\%]/",$exp)) {
	$aResponse['msg'] = "ERROR : Invalid Search Expression";
	sendResponse($aResponse);
}
if (preg_match("/[^a-zA-Z0-9\/ _\-\%\.]/",$template)) {
	$aResponse['msg'] = "ERROR : Invalid template name";
	sendResponse($aResponse);
}
if(!is_numeric($exact)) {
    $aResponse['msg'] = "ERROR : Invalid exact param";
    sendResponse($aResponse);
}
if(!is_numeric($ctype)) {
    $aResponse['msg'] = "ERROR : Invalid ctype param";
    sendResponse($aResponse);
}


search($exp, $exact, $ctype,  $filterDate, $fromDate, $toDate);



function search($uri, $exact, $ctype, $filterDate, $fromDate, $toDate)
{
    global $aResult, $aResponse;

    /* @todo - search unpublished article
    if ($uri == "UNPUBLISHED")
    {
        $bUnpublished = true;
    }*/

    $aResult = searchSQL($uri, $exact, $ctype, $filterDate, $fromDate, $toDate);
    prepareResponse();
    sendResponse($aResponse);
}


function searchSQL($term, $exact, $ctype, $filterDate, $fromDate, $toDate)
{
    global $db;
    
    $term = strtoupper($term);

    if (preg_match("/^\//", $term))
    {
        $stype = 0; // URL
    } else {
        $stype = 1; // Keyword(s)
    }
    
    if ($exact)
    {
        $operator = " = ";
    } else { // fuzzy
        $operator = " like ";
        if (strpos($term, "%") < 1)
        {
            if ($stype == 0)
            {
                $term = $term."%";
            } elseif ($stype == 1) {
                $term = "%".$term."%";
            }
        }
    }
    
    $strCompanyDateSQL = "";
    $strPlacementDateSQL = "";
    $strArticleDateSQL = "";

    if ($filterDate == 1)
    {
        $strCompanyDateSQL = "and c.added >= '".$fromDate."' and c.added <= '".$toDate."' ";
        $strPlacementDateSQL = "and p.added >= '".$fromDate."' and p.added <= '".$toDate."' ";
        $strArticleDateSQL = "and published_date >= '".$fromDate."' and published_date <= '".$toDate."' ";
    }

    $aCtype = str_split($ctype);
    
    $operator = ($exact == true) ? " = " : " like ";
    
    if ($stype == 0)
    {
        $strSQLWhereCompany = "UPPER('/COMPANY/'||c.url_name) ".$operator." '".$term."'";
        $strSQLWherePlacement = "UPPER('/company/'||c.url_name||'/'||p.url_name) ".$operator." '".$term."'";
        $strSqlWhereArticle = "UPPER(m.section_uri)  ".$operator." '".$term."'";
        
    } elseif ($stype == 1) {
        $strSQLWhereCompany = "UPPER(c.title)  ".$operator." '".$term."'";
        $strSQLWherePlacement = "UPPER(p.title)  ".$operator." '".$term."'";
        $strSqlWhereArticle = "UPPER(a.title)  ".$operator." '".$term."'";
    }
    
    $strCompanySQL = "";
    $strPlacementSQL = "";
    $strArticleSQL = "";
   
    if ($aCtype[2] == 1)
    {
        $strCompanySQL = "
        (select
		c.id,
		'COMPANY' as content_type,
		c.title,
        '' as company,
		'/company/'||c.url_name as url,
        c.added,
		c.last_updated
		from
		company c
		where 
        ".$strSQLWhereCompany."
        ".$strCompanyDateSQL."
        and c.last_updated is not null
		order by c.last_updated desc limit 50 )
        ";
        
        if ($aCtype[1] == 1 || $aCtype[0] == 1 )
        {
            $strCompanySQL = $strCompanySQL." union ";
        }
    }

    if ($aCtype[1] == 1)
    {
        $strPlacementSQL = "
		(select
		p.id,
		'PLACEMENT' as content_type,
		p.title,
        c.title as company,
		'/company/'||c.url_name||'/'||p.url_name as url,
        p.added,
		p.last_updated
		from
		profile_hdr p,
		company c
		where 
        ".$strSQLWherePlacement." 
        ".$strPlacementDateSQL."
        and p.company_id = c.id
		and p.last_updated is not null
		order by last_updated desc limit 50 )
        ";
        
        if ($aCtype[0] == 1 )
        {
            $strPlacementSQL = $strPlacementSQL." union ";
        }
    }

    if ($aCtype[0] == 1)
    {
        $strArticleSQL = "
		(select
		a.id,
		'ARTICLE' as content_type,
		a.title,
        '' as company,
		m.section_uri as url,
        a.created_date as added,
		a.published_date as last_updated
		from
		article a left outer join article_map m on a.id = m.article_id
		where 
        ".$strSqlWhereArticle."
        ".$strArticleDateSQL."
		order by published_date desc limit 50 )
        ";        
    }

    $sql = "
		select * from
		(".$strCompanySQL." ".$strPlacementSQL." ".$strArticleSQL.") q1
		order by last_updated DESC";
    
    $db->query($sql);
    
    return $db->getObjects();
}


function prepareResponse()
{
    global $aResult, $aResponse, $oAuth;

    if(is_object($oAuth) && $oAuth->oUser->isAdmin)
    {
        $template = "search_result_list_profile_admin.php";
    } else {
        $template = "search_result_list_profile.php";
    }
    

    $oTemplate = new Template();
    $oTemplate->Set("RESULT_ARRAY",$aResult);
    $oTemplate->Set("WEBSITE_URL",$aBrandConfig['oneworld365.org']['website_url']);
    $oTemplate->Set("RESULT_TYPE",'MIXED');
    $oTemplate->LoadTemplate($template);
    $oTemplate->Render();
    
    $aResponse['retVal'] = true;
    
    $aResponse['html'] = $oTemplate->Render();
    $aResponse['status'] = "success";
}


function sendResponse($aResponse) {

	/* return response back to the caller */
	$oJson = new Services_JSON;
	header('Content-type: application/x-json');
	print $oJson->encode($aResponse);
	die();	

}

?>
