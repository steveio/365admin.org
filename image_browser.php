<?php

require_once("./classes/ImageBrowser.class.php");


include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isValidUser || !$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");


$oImageBrowser = new ImageBrowser();

$aCompany = $oImageBrowser->GetCompanyName();
$aPlacement = $oImageBrowser->GetPlacementName();

$iPageSize = 300;
$iPageSizePerSeg = 100;
$iPageNum = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$_REQUEST['page_size'] = $iPageSizePerSeg;

$oPager = new PagedResultSet();
$oPager->SetResultsPerPage($iPageSize);
$oPager->GetByCount($oImageBrowser->GetCount(),"page");


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

<div class="row my-3">

	<div class="col-12">
        <h4>Filter: </h4>
		<input type="hidden" name="filter_report" value="1" />


	<div class="row my-3">
	<div class="col-12">
        <label for="company">Company:</label>
        <select id="" class="form-select" name="company_id">
        	<option value="ALL">ALL</option><?
        	foreach($aCompany as $aRow)
        	{
        	?>
        	<option value="<?= $aRow['id'] ?>" <?= ($_REQUEST['company_id'] == $aRow['id']) ? "selected" : ""; ?>><?= $aRow['title']; ?></option><?
        	} ?>
        </select>
	</div>
	<div class="col-12">
        <label for="placement">Placement:</label>
        <select id="" class="form-select" name="placement_id">
        	<option value="ALL">ALL</option><?
        	foreach($aPlacement as $aRow)
        	{
        	?>
        	<option value="<?= $aRow['id'] ?>" <?= ($_REQUEST['placement_id'] == $aRow['id']) ? "selected" : ""; ?>><?= $aRow['title']; ?></option><?
        	} ?>
        </select>
	</div>

	<!-- 
	<div class="col-6">
        <label for="origin_type">Linked To:</label>
        <select id="" class="form-select" name="link_to">
        	<option value="ALL">ALL</option>
        	<option value="ARTICLE" <?= ($_REQUEST['link_to'] == 'ARTICLE') ? "selected" : ""; ?>>Article</option>
        	<option value="COMPANY" <?= ($_REQUEST['link_to'] == 'COMPANY') ? "selected" : ""; ?>>Company</option>
        	<option value="PLACEMENT" <?= ($_REQUEST['link_to'] == 'PLACEMENT') ? "selected" : ""; ?>>Placement</option>
        </select>
	</div>
	 -->

	</div>

	<div class="row my-3">
	<div class="col-1">
		<button class="btn btn-primary rounded-pill px-3" type="button" name="report_filter" value="go" onClick="this.form.submit()">submit</button>
	</div>
	</div>
	</div>
</div>


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
            
            $img_url = "http://www.oneworld365.org".$aRow['filepath'].$aRow['id'].$aRow['ext'];
            $img_sf_url = "http://www.oneworld365.org".$aRow['filepath'].$aRow['id']."_sf".$aRow['ext'];
            $img_mf_url = "http://www.oneworld365.org".$aRow['filepath'].$aRow['id']."_mf".$aRow['ext'];
            $img_lf_url = "http://www.oneworld365.org".$aRow['filepath'].$aRow['id']."_lf".$aRow['ext'];
            $img_path = $aRow['filepath'].$aRow['id'].$aRow['ext'];
            
            if ($aRow['image_type'] == "IMAGE") 
            {
                $img_src = $img_mf_url;
            } else {
                $img_src = $img_o_url;
            }

            ?>
        	<tr> 
        	<td class=""><?= $aRow['img_id']; ?></td>
			<td class=""><?= $aRow['link_to']; ?></td>
			<td class=""><a href="<?= $aRow['url']; ?>" target="_new"><?= $aRow['title']; ?></a></td>
			<td class=""><?= $aRow['image_type']; ?></td>
			<td class=""><a href="<?= $img_url; ?>" target="_new"><?= $img_path; ?></a>
				<br /><br />{ <a href="<?= $img_url; ?>" target="_new">full</a> | <a href="<?= $img_lf_url; ?>" target="_new">large</a> | <a href="<?= $img_mf_url; ?>" target="_new">med</a> }
			</td>
			<td class=""><a href="<?= $img_url; ?>" target="_new"><img src="<?= $img_src; ?>" alt="" /></a></td>
        	</tr><?
        } 
    } ?>
    </tbody>
    </table>


<div class="row">
		<div id="pager" class="row pagination pagination-large pagination-centered">	
		<?= $oPager->RenderHTML(); ?>
		</div>
</div>

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
