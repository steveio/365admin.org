<?php

require_once("./conf/config.php");
require_once("./conf/brand_config.php");
require_once("./init.php");


include_once("./includes/header.php");
include_once("./includes/footer.php");




if (!$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = "/",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");


$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("https://cdn.tiny.cloud/1/64vi9u0mlw972adwn9riluuctbqvquz44j5udsiffm2xvx3y/tinymce/6/tinymce.min.js");
$oJsInclude->SetReferrerPolicy("origin");
$oHeader->SetJsInclude($oJsInclude);


$ckeditor_js = <<<EOT

tinymce.init({
        selector: '#desc_short',
	menubar : false,
	images_upload_url: '/image_upload.php',
  height:"291",

});


tinymce.init({
        selector: '#desc_long',
	menubar: false,
        toolbar: "undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | image link | table | numlist bullist | code",
        plugins: "image link lists table code",
        images_upload_url : '/image_upload.php',

});

EOT;

$oHeader->SetJsOnload($ckeditor_js);
$oHeader->Reload();




$mode = (is_numeric($_REQUEST['id'])) ? "EDIT" : "ADD";

$sTitle = ($mode == "EDIT") ? "Edit Article" : "New Article";

$oWebsite = new Website($db);
$sWebSiteListHTML = $oWebsite->GetSiteSelectList(array(),$checked = TRUE, $markup = "DIV");


$aResponse = array();
$oArticle = new Article();

$article_id = $_REQUEST['id'];

// put article id in session for image_upload.php
$_SESSION['article_id'] = $_REQUEST['id'];


/*
 * EVENT HANDLERS
 *
 */


if (isset($_REQUEST['attach_profile'])) {
	$oArticle->SetId($article_id);
	if (!$oArticle->AttachProfile($_REQUEST,$aResponse)) {
		$aResponse['msg'] = "ERROR : Please select a profile to attach";
	}
}


if (isset($_REQUEST['remove_profile'])) {

    $oArticle->SetId($article_id);
	if (is_numeric($_REQUEST['profile_id'])) {
		 $oArticle->RemoveProfile($_REQUEST,$aResponse);
		 $aResponse['msg'] = "SUCCESS : Removed attached profile";
		 $aResponse['status'] = "success";
	} else {
		$aResponse['msg'] = "ERROR : Please select a placement to remove";
		$aResponse['status'] = "success";
	}
}


if (isset($_REQUEST['attach_article'])) {
	$oArticle->SetId($article_id);
	$aId = Mapping::GetIdByKey($_REQUEST,"art_");
	if ($oArticle->AttachArticleId($aId)) {
		$aResponse['msg'] = "SUCCESS : Attached selected articles;";
		$aResponse['status'] = "success";
	}
}



if (isset($_REQUEST['remove_article'])) {
	$oArticle->SetId($article_id);
	$aId = Mapping::GetIdByKey($_REQUEST,"art_");
	if ($oArticle->RemoveAttachedArticle($aId)) {
		$aResponse['msg'] = "SUCCESS : Removed selected articles;";
		$aResponse['status'] = "success";
	}
}



$max_size = IMAGE_MAX_UPLOAD_SIZE;
$max_uploads  = 4;
$path = '/www/vhosts/oneworld365.org/htdocs/upload/images/';

if (isset($_REQUEST['do_file_upload'])) {

	if (count($_FILES['file']['name'])<=$max_uploads) {
		$upload = new File_upload();
		$upload->allow('images');
		$upload->set_path($path);
		$upload->set_max_size($max_size);

		$aResult = $upload->upload_multiple($_FILES['file']);

		$error = false;
		if ($upload->is_error()) {
			$error = true;
			$errstr= $upload->get_error();
		}


		if (is_array($aResult['TMP_PATH']) && count($aResult['TMP_PATH']) >= 1) {
			/* Now call ImageProcessor to generate proxy images */
			$oIP = new ImageProcessor_FileUpload;
			$result = $oIP->Process($aResult['TMP_PATH'],"ARTICLE",$article_id,$iImgType = PROFILE_IMAGE);
			if (!$result) {
				$error = TRUE;
				$errstr= 'An error occured during image thumbnail processing';
			}

		}

	} else {
		$error = TRUE;
		$errstr= 'Trying to upload to many files';
	}


	if ($error) {
		$aResponse['msg'] = "ERROR : ".$errstr;
	} else {
		$plural = (count($aResult['FILENAME']) > 1) ? "s" : "";
		$aResponse['msg'] = "SUCCESS : uploaded ".count($aResult['FILENAME']) ." file".$plural."<br/>".implode("<br />",$aResult['FILENAME']);
		$aResponse['status'] = "success";
	}

}



if (isset($_REQUEST['save'])) {

	$oArticle->SetFromArray(array(
								"id" => $_POST['id']
								,"title" => $_POST['title']
								,"short_desc" => $_POST['desc_short']
								,"full_desc" => $_POST['desc_long']
								,"meta_desc" => $_POST['meta_desc']
								,"meta_keywords" => $_POST['meta_keywords']
								,"created_by" => $oAuth->oUser->id
								,"published_status" => 0 /* DRAFT */
								),"SET", $escape_chars = FALSE /* Save()->Santitize() does escaping */
							);

	if ($oArticle->Save($aResponse)) {
		$aResponse['msg'] = "SUCCESS : Article saved OK";
		$aResponse['status'] = "success";
		$_REQUEST['id'] = $oArticle->GetId();
		$_SESSION['id'] = $oArticle->GetId();
		$_SESSION['link_to'] = "ARTICLE";
	}
}


if(($mode == "EDIT") || ($mode == "ADD")) {

	$oArticle->SetFetchMode(FETCHMODE__FULL);
	$oArticle->SetFetchAttachedTo(TRUE);
	$oArticle->SetFetchProfiles(TRUE);
	$oArticle->SetFetchAttachedTo(TRUE);

	/* get article from DB */
	if ($mode == "EDIT") {
		if (!$oArticle->GetById($_REQUEST['id'])) {
			$aResponse['msg'] = "ERROR : Unable to retrieve article";
		}
	}
}

$oCompany = new Company($db);
$sCompDDList = $oCompany->getCompanyNameDropDown($_REQUEST['company_id'],null,'company_id',true,1,$sOnChangeJS = "this.form.submit();");
if(isset($_REQUEST['company_id']))
{
    $oProfilePlacement = new PlacementProfile();
    $sPlacementDDList = $oProfilePlacement->GetPlacementDDList($_REQUEST['company_id']);
} else {
    $sPlacementDDList = "<select class='form-select' id='placement_id' disabled><option value='NULL'>select</option></select>";
    
}




print $oHeader->Render();

?>

<div class="container">
<div class="align-items-center justify-content-center">

<?
if (isset($aResponse['msg']) && strlen($aResponse['msg']) >= 1) {
?>
<div class="alert alert-<?= (isset($aResponse['status'])) ? $aResponse['status'] : "warning";  ?>" role="alert">
    <?= $aResponse['msg'];  ?>
</div>
<? } ?>


<div class='row'>

<h1><?= $sTitle ?></h1>


<form enctype="multipart/form-data" name="edit_article" id="edit_article" action="#" method="POST">

<input type="hidden" name="id" value="<?= $oArticle->GetId(); ?>" />


<div class="row  my-3">
	<span class="label_col"><label for="title" class="f_label" style="<?= strlen($response['msg']['title']) > 1 ? "color:red;" : ""; ?>">Title<span class="red"> *</span></label></span>
	<span class="input_col"><input type="text" id="title" class="form-control" name="title" value="<?= $oArticle->GetTitle(); ?>" /></span>
</div>

<div class="row my-3">
	<span class="label_col"><label for="desc_short" class="f_label" style="<?= strlen($response['msg']['desc_short']) > 1 ? "color:red;" : ""; ?>">Short Desc<span class="red"> *</span></label></span>
	<span class="input_col"><textarea id="desc_short" class="tinyMCEeditor" name="desc_short" /><?= $oArticle->GetDescShort(); ?></textarea></span>
</div>

<div class="row my-3">
	<span class="label_col"><label class="f_label">Full Description</label></span>

	<? $desc_full = $oArticle->GetDescFull(); ?>

	<span class="input_col"><textarea id="desc_long" class="tinyMCEeditor" name="desc_long" /><?= $desc_full; ?></textarea></span>
</div>

<div class="row my-3">
	<div class="col">
		<span class="label_col"><label for="meta_desc" class="f_label" style="<?= strlen($response['msg']['meta_desc']) > 1 ? "color:red;" : ""; ?>">Meta Desc</label></span>
	        <span class="input_col"><input type="text" id="meta_desc" class="form-control" maxlength="254" style="width: 400px;" name="meta_desc" value="<?= $oArticle->GetMetaDesc(); ?>" /></span>
	</div>
	<div class="col">
		<span class="label_col"><label for="meta_keywords" class="f_label" style="<?= strlen($response['msg']['meta_keywords']) > 1 ? "color:red;" : ""; ?>">Meta Keywords</label></span>
		<span class="input_col"><input type="text" id="meta_keywords" class="form-control"  maxlength="254" style="width: 400px;" name="meta_keywords" value="<?= $oArticle->GetMetaKeywords(); ?>" /></span>
	</div>
</div>




<div class="row">
	<span class="my-3">
  		 <button class="btn btn-primary rounded-pill px-3" type="submit" name="save" value="save">Save</button>

		<? if (is_numeric($oArticle->GetId())) { ?>
      <button class="btn btn-primary rounded-pill px-3" type="button" onclick="javascript: go('./article-publisher?&id=<?= $oArticle->GetId() ?>'); return false;" name="new">Publish</button>
		<? } ?>
	</span>
</div>



<div class="row" style="width: 800px;"><hr /></div>


<? if (is_numeric($oArticle->GetId())) { ?>


<div class="row">

	<h2>Attached Images : <?= (count($oArticle->aImage) == 0) ? "0 images attached" : ""; ?></h2>
	<div id="image_msg"></div>

	<div class="row">
	<?
	if (count($oArticle->aImage) >= 1) {
		foreach($oArticle->aImage as $oImage) {
			?>
			<div id="img_<?= $oImage->GetId() ?>" style="float: left; padding-right: 6px;">
			<a class="p_small" title="Remove Image" href="javascript: void(null);" onclick="javascript: RemoveImage('ARTICLE',<?= $oArticle->GetId() ?>,<?= $oImage->GetId() ?>)">[REMOVE]</a>
			<br />
			<?= $oImage->GetHtml("_s",$oArticle->GetTitle()) ?>
			</div>
			<?
		}
	}
	?>
	</div>

	<h2>Upload Images :</h2>

	<div class="row">
		<!-- MULTIPLE FILE UPLOAD -->

		<script type="text/javascript">
		<!--
			var gFiles = 0;
			function addFile() {
				var tr = document.createElement('tr');
				tr.setAttribute('id', 'file-' + gFiles);
				var td = document.createElement('td');
				td.innerHTML = '<input type="file" size="30" name="file[]"><span onclick="removeFile(\'file-' + gFiles + '\')" style="cursor:pointer;">Remove</span>';
				tr.appendChild(td);
				document.getElementById('files-root').appendChild(tr);
				gFiles++;
			}
			function removeFile(aId) {
				var obj = document.getElementById(aId);
				obj.parentNode.removeChild(obj);
			}
		-->
		</script>

		<span onclick="addFile()" style="cursor:pointer;cursor:hand;">Add More +</span>

		<input type="hidden" name="mode" value="misc" />
		<input type="hidden" name="action" value="upload" />
		<input type="hidden" name="upload" value="1" />

		<input type="hidden" name="MAX_FILE_SIZE" value="<?= IMAGE_MAX_UPLOAD_SIZE; ?>" />
		<table>
		<tbody id="files-root">
			<tr><td><input class="form-control" type="file" name="file[]" size="30"></td></tr>
		</table>
		<div class="col-1 my-3">
			<input class="btn btn-primary rounded-pill px-3"  type="submit" name="do_file_upload" value="Upload Image">
		</div>

		<p style="font-size:8pt;">allowed extensions are: <strong>JPG, JPEG PNG, GIF</strong>; max size per file: <strong>5mb</strong>; max number of files per upload <strong><?php echo $max_uploads; ?></strong></p>
		<?php
		if ($is_upload && $error) {
			print '<strong>Error: '.$errstr.'</strong><br />';
		} else if ($is_upload) {
			foreach ($files as $file) {
				$image = _URL_.'uploads/'.$file;
				print '<input type="text" size="'.strlen($image).'" value="'.$image.'"><br />';
				print '<img src="'.$image.'">';
			}
		}
		?>

	</div>
</div>



<div class="row my-3">
	<h2>Attached Profiles :</h2>

	<?

	foreach($oArticle->GetAttachedProfile() as $oProfile) {

		if ($oProfile->GetType() == PROFILE_COMPANY) {
			$oProfile->LoadTemplate("profile_summary_01_sm.php");
		} else { /* assume everything else is a placement profile varient */
			$oProfile->LoadTemplate("profile_summary_01_sm.php");
		}

		?>
		<div class="border" style="float: left; width: 250px;">
    		<div style="float: right; padding-right: 10px;"><a class="p_small" title="Remove Profile" href="/article-editor/?&id=<?= $oArticle->GetId() ?>&profile_id=<?= $oProfile->GetId() ?>&profile_type=<?= $oProfile->GetType(); ?>&remove_profile=1">[REMOVE]</a></div>
    		<div style="height: 200px;">
    		<?= $oProfile->Render(); ?>
			</div>
		</div>
		<?
	}

?>

</div>


<div class="row my-3">

	<h2>Attach Profiles :</h2>

	<div class="row">
		<span class="label_col"><label class="f_label">Company</label></span>
		<span class="input_col"><?= $sCompDDList ?></span>
	</div>

	<div class="row">
		<span class="label_col"><label class="f_label">Placement</label></span>
		<span class="input_col"><div id="placement_list"><?= $sPlacementDDList ?></div></span>
	</div>

	<div class="row">
		<span class="label_col"><label class="f_label">&nbsp;</label></span>
		<span class="input_col">
			<input class="btn btn-primary rounded-pill px-3" type="submit" onclick="javascript: return validateAttachProfile();" title="attach profile" name="attach_profile" value="Attach" class="sub_col_but" />
		</span>
	</div>

</div>



<div class="row my-3">
	<h2>Attached Articles :</h2>

	<?
	if ($oArticle->oArticleCollection->Count() >= 1) {
		$oArticle->oArticleCollection->LoadTemplate("article_search_result_list_02.php");
		print $oArticle->oArticleCollection->Render();
	}
	?>

</div>


<div class="row my-3">

	<h2>Attach Articles :</h2>

	<div class="row my-3">
    	<div class="">
    		Article Url:
    		<input class="form-control" type="text" id="search_phrase" value="<?= $_REQUEST['search_phrase'] ?>" />
    		<input class="btn btn-primary rounded-pill px-3" type="submit" onclick="javascript: ArticleSearch('search',<?= $oArticle->GetId()  ?>,'article_search_result_list_01.php'); return false;" name="article_search" value="Search" />
    		<input type="hidden" name="web_0" value="on" />
    	</div>
    </div>

	<div id="article_search_msg"></div>
	<div id="article_search_result"></div>

	</div>

	<div class="row">

	<h2>Attached To :</h2>

	<p class="p_small">Shows other articles this article is attached to and where these are published</p>

	<div id="article_deattach_msg" class="alert alert-success" style="display: none;" role="alert">

	<table cellspacing="2" cellpadding="4" border="0" class="table table-striped">
		<tr>
			<th>Id</th>
			<th>Article Title</th>
			<th>Publised to Urls</th>
			<th>De-attach</th>
		</tr>
		<?php
		$prev_id = null;
		foreach ($oArticle->GetAttachedTo() as $row) {
			$url = "<a href='/article-editor?id=".$row['id']."' target='_new' title='View / Edit this Article'>".$row['title']."</a>";
			$deattach_link = "<input class=\"btn btn-primary rounded-pill px-3\" type=\"submit\" onclick=\"javascript: ArticleDeattach(".$row['id'].",". $oArticle->GetId() ."); return false;\" name=\"deattach\" value=\"Deattach\" />";
		?>
		<tr id="deattach_row<?= $row['id']; ?>">
			<td valign="top"><?= $row['id']; ?></td>
			<td valign="top"><?= $url; ?></td>
			<td><?= $row['published_url']; ?></td>
			<td><?= $deattach_link; ?></td>
		</tr>
		<?php
			$prev_id = $row['id'];
		}
		?>
	</table>

</div>

<? } // end is published check ?>


</form>

</div>


</div>
</div>

<?
print $oFooter->Render();
?>
