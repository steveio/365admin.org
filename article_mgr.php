<?php

require_once("./conf/config.php");
require_once("./init.php");
require_once("./conf/brand_config.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/client_login.php",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");


$oArticle = new Article();


$oWebsite = new Website($db);
$sWebSiteListHTML = $oWebsite->GetSiteSelectList(array(),$checked = TRUE, $markup = "DIV");




$aDelete = array();

foreach($_REQUEST as $k => $v) {
	if (preg_match("/art_/",$k)) {
		$id = preg_replace("/art_/","",$k);
		if ($v == "delete") $aDelete[] = $id;
	}
}

if (count($aDelete) >= 1) {
	foreach($aDelete as $id) {
		if (DEBUG) Logger::Msg("Delete : id = ".$id);
		$oArticle->SetId($id);
		if ($oArticle->Delete()) {
			$aResponse['msg'] = "SUCCESS : Deleted article.";
		}
	}
}


$aFilter = array();

if (strlen($_REQUEST['filter_uri']) >= 1) $aFilter['URI'] = $_REQUEST['filter_uri'];

if (count($aFilter) >= 1) $aArticle = $oArticle->GetAll($aFilter);





print $oHeader->Render();
?>


<!-- BEGIN Page Content Container -->
<div class="container">
<div class="align-items-center justify-content-center">



<div id="msgtext" class="col-12" style="color: red; font-size: 10px;">
<?= AppError::GetErrorHtml($aResponse['msg']);  ?>
</div>


<form enctype="multipart/form-data" name="linkadmin" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">



<div class='row'>

	<h1>Content Manager</h1>

	<div class="col">

		Url:
		<input type="text" id="search_phrase" style="width: 350px;" value="<?= $_REQUEST['filter_uri'] ?>" />

		<button class="btn btn-primary rounded-pill px-3" type="button" onclick="javascript: ArticleSearch('<?= $_CONFIG['url'] ?>','search','','article_search_result_list_03.php'); return false;" name="article_search">Search</button>


		Exact? <input type="checkbox" id="search_exact" name="search_exact" />

		<ul>
			<li>Patterns: <span class="p_small">"%" = all  OR  "%africa" = contains "africa" OR  "/activity/animals" OR "UNPUBLISHED" = new articles</i></span></li>
		</ul>

	</div>

</div>

<div class="row">
	<div id="article_search_msg"></div>
	<div id="article_search_result"></div>
</div>

<div class="row">
	<div class="col">
		<span class="">
			<button class="btn btn-primary rounded-pill px-3" type="button" onclick="javascript: window.location = './article-editor'; return false;">New Article</button>
		</span>
	</div>
</div>

</form>


</div>
</div>

<?

print $oFooter->Render();

?>
