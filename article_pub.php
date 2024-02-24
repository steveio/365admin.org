<?php

require_once("./conf/config.php");
require_once("./init.php");
require_once("./conf/brand_config.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");

if (!is_numeric($_REQUEST['id'])) AppError::StopRedirect($sUrl = $_CONFIG['url']."/article-manager",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");


$oArticle = new Article();

$oArticle->SetFetchMode(FETCHMODE__SUMMARY);

$oWebsite = new Website($db);
$sWebSiteListHTML = $oWebsite->GetSiteSelectList(array());



/* unpublish (delete article mapping) */
$aMappingId = Mapping::GetIdByKey($_REQUEST,"map_");
if (count($aMappingId) >= 1) {
	foreach($aMappingId as $id) {
		$oArticle->MapDeleteById($id);
	} 
}	


if (isset($_REQUEST['publish'])) {

	$oArticle->SetId($_REQUEST['article_id']);

	$oArticle->Publish($_REQUEST,$aResponse);

}


if (!$oArticle->GetById($_REQUEST['id'])) {
	$aResponse['msg'] = "ERROR : Unable to retrieve article";
}


$oTemplateList = new TemplateList();
$oTemplateList->GetFromDB();


print $oHeader->Render();

?>

<div class="container">
<div class="align-items-center justify-content-center">


<h1>Article Publisher</h1>

<div class="row my-3">
	<div class="col">
    	<button class="btn btn-outline-primary rounded-pill px-3" type="button" title="Edit article" onclick="javascript: go('./article-editor?id=<?= $oArticle->GetId(); ?>'); return false;" name="new" value="Edit Article">Edit Article</button>
    	<button class="btn btn-outline-primary rounded-pill px-3" type="button" title="View article" onclick="javascript: go('<?= $oArticle->GetUrl(); ?>'); return false;" name="new" value="View Article">View Article</button>
    	<button class="btn btn-outline-primary rounded-pill px-3" type="button" title="Article Manager" onclick="javascript: go('./article-manager'); return false;" name="new" value="Article Manager">Article Manager</button>
    </div>		
</div>

<? 
if (isset($aResponse['msg']) && strlen($aResponse['msg']) >= 1) { 
    $alert = (isset($aResponse['status'])) ? $aResponse['status'] : "warning";
    ?>
<div class="alert alert-<?= $alert; ?>" role="alert">
    <?= $aResponse['msg'];  ?>
</div>
<? } ?>


<form enctype="multipart/form-data" name="edit_article" id="publish_article" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">
<input type="hidden" name="article_id" value="<?= $oArticle->GetId(); ?>" />


<div class="row my-3">
	<h2>Article: <?= $oArticle->GetTitle(); ?> <span class="small">( <a href="<?= $oArticle->GetUrl(); ?>" target="_new"><?= $oArticle->GetUrl(); ?></a> )</span></h2>
</div>

<div class="row my-3">
	<h2>Publish to Url :</h2>
	

	<input type="hidden" name="web_0" value="on" />

    <div class="row">
    	<span class="input_col"><input type="text" id="section_uri" class="form-control" name="section_uri" value="<?= $_REQUEST['section_uri'] ?>" /></span>
    </div>

    <div class="row my-3">
    	<div class="col-2">
    	<button class="btn btn-primary rounded-pill px-3" type="submit" title="publish article" name="publish" value="publish">Publish</button>
    	</div>
	</div>
</div>

</form>


<form enctype="multipart/form-data" name="edit_article" id="publish_article" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">

<div class="row my-3">

<h2>Published To:</h2>

<div id="alert-msg" class="alert alert-success" role="alert" style="display: none;"></div>

<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">Website Url</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>

<? 
if (count($oArticle->GetMapping()) >= 1) {
	foreach($oArticle->GetMapping() as $oArticleMapping) {
	?>

    <tr>
      <td>
		<div class="col-10">
			<?= $oArticleMapping->GetLabel() ?>
		</div>
      </td>
      <td>
	      <input type="submit" onclick="javscript: return confirm('Are you sure you wish to unpublish : <?= $oArticleMapping->GetLabel(); ?>?');" name="map_<?= $oArticleMapping->GetId() ?>" value="delete" />
      </td>
    </tr>


    <tr>
      <td colspan="2">
			<?php $checked = ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_PLACEMENT) == "t") ? "checked" : "" ; ?>
			Profile Search Results <input type="checkbox" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_PLACEMENT; ?>" <?= $checked ?> />

			<?php $checked = ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_IMG) == "t") ? "checked" : "" ; ?>
			Intro Article / Image <input type="checkbox" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_IMG; ?>" <?= $checked ?> />

			<?php $checked = ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_BLOG) == "t") ? "checked" : "" ; ?>
			Blog Articles <input type="checkbox" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_BLOG; ?>" <?= $checked ?> />		

			<?php $checked = ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_PROFILE) == "f") ?  "" : "checked"; ?>
			Related Articles <input type="checkbox" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_ARTICLE; ?>" <?= $checked ?> />		

			<?php $checked = ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_PROFILE) == "f") ?  "" : "checked"; ?>
			Related Profiles <input type="checkbox" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_PROFILE; ?>" <?= $checked ?> />		

			<?php $checked = ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_REVIEW) == "f") ?  "" : "checked"; ?>
			Comments / Reviews <input type="checkbox" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_REVIEW; ?>" <?= $checked ?> />		

			<?php $checked = ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_SOCIAL) == "f") ?  "" : "checked"; ?>
			Social Buttons <input type="checkbox" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_SOCIAL; ?>" <?= $checked ?> />		

			<?php $checked = ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_ADS) == "f") ?  "" : "checked"; ?>
			Google Ads <input type="checkbox" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_ADS; ?>" <?= $checked ?> />		
		</td>
	</tr>
    <tr>
        <td colspan="2">

      	<div class="row">
    		<div class="col-2">
    			Template: 
    		</div>
    		<div class="col-9">
				<select id="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_TEMPLATE_ID; ?>" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_TEMPLATE_ID; ?>" class="form-select">
				<?php 
				foreach($oTemplateList->GetTemplateList() as $oTemplate)
				{ ?>
					<option value="<?= $oTemplate->id ?>" <?= ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_TEMPLATE_ID) == $oTemplate->id) ? "selected" : ""; ?>><?= $oTemplate->title ?> : <?= $oTemplate->desc_short ?></option>
				<?php 
				}
				?>
				</select>
				Content From:
    			<?php $checked = ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_ATTACHED) == "t") ? "checked" : "" ; ?>
    			Attached Articles <input type="checkbox" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_ATTACHED; ?>" <?= $checked ?> />
    			<?php $checked = ($oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_PATH) == "t") ? "checked" : "" ; ?>
    			Path <input type="checkbox" name="opt_<?= $oArticleMapping->GetId() ?>_<?= ARTICLE_DISPLAY_OPT_PATH; ?>" <?= $checked ?> />

    		</div>
		</div>
		</td>
	</tr>
    <tr>
        <td colspan="2">

      	<div class="row">
    		<div class="col-2">
    			Profile Result Title: 
    		</div>
    		<div class="col-9">
				<input id="ptitle_<?= $oArticleMapping->GetId() ?>" type="text" class="form-control" name="ptitle_<?= $oArticleMapping->GetId() ?>" value="<?= $oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_PTITLE); ?>" maxlength="128"  style="width: 300px;" />    		
    		</div>
    		<div class="col-2">
    			Profile Intro Paragraph:
    		</div>
    		<div class="col-9">
				<textarea id="pintro_<?= $oArticleMapping->GetId() ?>" type="text" name="pintro_<?= $oArticleMapping->GetId() ?>" class="form-control" value="<?= $oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_PINTRO); ?>" style="width: 300px; height: 60px;" maxlength="512"><?= $oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_PINTRO); ?></textarea>    		
    		</div>
    		<div class="col-2">
    			Search Result Keywords:
    		</div>
    		<div class="col-9">
				<input id="sphrase_<?= $oArticleMapping->GetId() ?>" type="text" name="sphrase_<?= $oArticleMapping->GetId() ?>" class="form-control" value="<?= $oArticleMapping->GetOptionById(ARTICLE_DISPLAY_OPT_SEARCH_KEYWORD); ?>" maxlength="128" style="width: 300px;" />    		
    		</div>
    	</div>

    	</td>
	</tr>

	<tr>
		<td colspan="2">
	    	<button class="btn btn-primary rounded-pill px-3" type="submit" name="opt_<?= $oArticleMapping->GetId() ?>" onclick="javascript: return ArticleMapOptions(<?= $oArticleMapping->GetId(); ?>);" value="update" >Update</button>
		</td>
	</tr>

<?php  
	}
} else {
    print "<tr><td colspan='2'><div id=\"msgtext\" >";
	print "Article is not currently published.";
	print "</div></td></tr>";
}
?>
   </tbody>
</table>
</div>


</form>
	
</div>

</div>
</div>


<?
print $oFooter->Render();
?>