<?php

require_once("./classes/ImageBrowser.class.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isValidUser || !$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");


$oImageBrowser = new ImageBrowser();


$aReport = $oImageBrowser->GetReport($_REQUEST);


/*
print_r("<pre>");
print_r($_REQUEST);
print_r("</pre>");
*/

print $oHeader->Render();

?>

<div class="container">
<div class="align-items-center justify-content-center">

<h1>Image Browser</h1>


<form name="report_filter" enctype="multipart/form-data" action="" method="POST">
<!-- 
<div class="row my-3">

	<div class="col-12">
        <h4>Filter: </h4>
		<input type="hidden" name="filter_report" value="1" />

	<div class="row my-3">
	<div class="col-6">
        <label for="company_name">Company:</label>
        <select id="" class="form-select" name="company_name">
        	<option value="ALL">ALL</option><?
        	//foreach($aCompany as $aRow)
        	//{
        	//    $val = $aRow['company'];
        	?>
        	<option value="<?= $val; ?>" <?= ($_REQUEST['company_name'] == $val) ? "selected" : ""; ?>><?= $val; ?></option><?
        	//} ?>
        </select>
	</div>
	<div class="col-6">
        <label for="origin_type">Link Type:</label>
        <select id="" class="form-select" name="origin_type">
        	<option value="ALL">ALL</option>
        	<option value="<?= LINK_ORIGIN_COMPANY; ?>" <?= ($_REQUEST['origin_type'] == LINK_ORIGIN_COMPANY) ? "selected" : ""; ?>>Company</option>
        	<option value="<?= LINK_ORIGIN_COMPANY_URL; ?>" <?= ($_REQUEST['origin_type'] == "0") ? "selected" : ""; ?>>Company : URL</option>
        	<option value="<?= LINK_ORIGIN_COMPANY_APPLY; ?>" <?= ($_REQUEST['origin_type'] == LINK_ORIGIN_COMPANY_APPLY) ? "selected" : ""; ?>>Company : APPLY URL</option>
        	<option value="<?= LINK_ORIGIN_PLACEMENT; ?>" <?= ($_REQUEST['origin_type'] == LINK_ORIGIN_PLACEMENT) ? "selected" : ""; ?>>Placement</option>
        	<option value="<?= LINK_ORIGIN_PLACEMENT_URL; ?>" <?= ($_REQUEST['origin_type'] == LINK_ORIGIN_PLACEMENT_URL) ? "selected" : ""; ?>>Placement : URL</option>
        	<option value="<?= LINK_ORIGIN_PLACEMENT_APPLY; ?>" <?= ($_REQUEST['origin_type'] == LINK_ORIGIN_PLACEMENT_APPLY) ? "selected" : ""; ?>>Placement : APPLY URL</option>
        </select>
	</div>
	</div>

	<div class="row my-3">
	<div class="col-6">
        <label for="http_status">HTTP Status:</label>
        <select id="" class="form-select" name="http_status">
        	<option value="ALL">ALL</option>
        	<option value="OK" <?= ($_REQUEST['http_status'] == "OK") ? "selected" : ""; ?>>OK</option>
        	<option value="ERROR" <?= ($_REQUEST['http_status'] == "ERROR") ? "selected" : ""; ?>>Error</option><?
        	
        	foreach($aHttpStatus as $aRow)
        	{
        	    $val = $aRow['status'];
        	?>
        	<option value="<?= $val; ?>" <?= ($_REQUEST['report_status'] == $val) ? "selected" : ""; ?>><?= $val; ?></option><?
        	} ?>
        </select>
	</div>
	</div>
	<div class="row my-3">
	<div class="col-1">
		<button class="btn btn-primary rounded-pill px-3" type="button" name="report_filter" value="go" onClick="this.form.submit()">submit</button>
	</div>
	</div>
	</div>
</div>
-->

<div class="row my-3">

<table id="report" class="display" cellspacing="2" cellpadding="4" border="0" class="table table-striped">	
    
	<thead>
    	<tr>
    	<th>Id</th>
    	<th>Link To</th>
    	<th>Title</th>
    	<th>Type</th>	
    	<th>Filename</th>
    	<th>Preview</th>
    	</tr>
	</thead>
	
	<tbody>
    <?
    if (is_array($aReport))
    {
        foreach($aReport as $aRow) { 
        
            if ($aRow['link_to'] == "") {
                $aRow['link_to'] = "ARTICLE (Unsaved)";
            }
            
            $img_o_url = "http://www.oneworld365.org".$aRow['filepath'].$aRow['id'].$aRow['ext'];
            $img_mf_url = "http://www.oneworld365.org".$aRow['filepath'].$aRow['id']."_mf".$aRow['ext'];
            $img_path = $aRow['filepath'].$aRow['id'].$aRow['ext'];
            
            if ($aRow['image_type'] == "IMAGE") 
            {
                $img_url = $img_mf_url;
            } else {
                $img_url = $img_o_url;
            }

            ?>
        	<tr> 
        	<td class=""><?= $aRow['img_id']; ?></td>
			<td class=""><?= $aRow['link_to']; ?></td>
			<td class=""><?= $aRow['title']; ?></td>
			<td class=""><?= $aRow['image_type']; ?></td>
			<td class=""><a href="<?= $img_url; ?>" target="_new"><?= $img_path; ?></a></td>
			<td class=""><a href="<?= $img_url; ?>" target="_new"><img src="<?= $img_url; ?>" alt="" /></a></td>
        	</tr><?
        } 
    } ?>
    </tbody>
    </table>

</form>

</div>


<script>

$(document).ready(function() {

    $('#report').DataTable({
    	"pageLength": 500,
    	"bSort" : false
    });

});

</script>

</div>
</div>

<?
print $oFooter->Render();

?>
