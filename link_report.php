<?php

require_once("./classes/LinkChecker.class.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isValidUser || !$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");


$oLinkChecker = new LinkChecker();
$aReport = $oLinkChecker->GetReport($_REQUEST);

$aHttpStatus = $oLinkChecker->GetHTTPStatusCode();
$aCompany = $oLinkChecker->GetCompanyName();

if (count($_REQUEST) == 0)
{
    $_REQUEST['company_name'] = "ALL";
    $_REQUEST['origin_type'] = "ALL";
    $_REQUEST['http_status'] = "ALL";
}

/*
print_r("<pre>");
print_r($_REQUEST);
print_r("</pre>");
*/

print $oHeader->Render();

?>

<div class="container">
<div class="align-items-center justify-content-center">

<h1>Link Status Report</h1>


<form name="report_filter" enctype="multipart/form-data" action="" method="POST">

<div class="row my-3">

	<div class="col-12">
        <h4>Filter: </h4>

		<input type="hidden" name="filter_report" value="1" />

        <label for="company_name">Company:</label>
        <select id="" name="company_name">
        	<option value="ALL">ALL</option><?
        	foreach($aCompany as $aRow)
        	{
        	    $val = $aRow['company'];
        	?>
        	<option value="<?= $val; ?>" <?= ($_REQUEST['company_name'] == $val) ? "selected" : ""; ?>><?= $val; ?></option><?
        	} ?>
        </select>

        <label for="origin_type">Link Type:</label>
        <select id="" name="origin_type">
        	<option value="ALL">ALL</option>
        	<option value="<?= LINK_ORIGIN_COMPANY; ?>" <?= ($_REQUEST['origin_type'] == LINK_ORIGIN_COMPANY) ? "selected" : ""; ?>>Company</option>
        	<option value="<?= LINK_ORIGIN_COMPANY_URL; ?>" <?= ($_REQUEST['origin_type'] == "0") ? "selected" : ""; ?>>Company : URL</option>
        	<option value="<?= LINK_ORIGIN_COMPANY_APPLY; ?>" <?= ($_REQUEST['origin_type'] == LINK_ORIGIN_COMPANY_APPLY) ? "selected" : ""; ?>>Company : APPLY URL</option>
        	<option value="<?= LINK_ORIGIN_PLACEMENT; ?>" <?= ($_REQUEST['origin_type'] == LINK_ORIGIN_PLACEMENT) ? "selected" : ""; ?>>Placement</option>
        	<option value="<?= LINK_ORIGIN_PLACEMENT_URL; ?>" <?= ($_REQUEST['origin_type'] == LINK_ORIGIN_PLACEMENT_URL) ? "selected" : ""; ?>>Placement : URL</option>
        	<option value="<?= LINK_ORIGIN_PLACEMENT_APPLY; ?>" <?= ($_REQUEST['origin_type'] == LINK_ORIGIN_PLACEMENT_APPLY) ? "selected" : ""; ?>>Placement : APPLY URL</option>
        </select>

        <label for="http_status">HTTP Status:</label>
        <select id="" name="http_status">
        	<option value="ALL">ALL</option>
        	<option value="OK" <?= ($_REQUEST['http_status'] == "OK") ? "selected" : ""; ?>>OK</option>
        	<option value="ERROR" <?= ($_REQUEST['http_status'] == "ERROR") ? "selected" : ""; ?>>Error</option><?
        	/*
        	foreach($aHttpStatus as $aRow)
        	{
        	    $val = $aRow['status'];
        	?>
        	<option value="<?= $val; ?>" <?= ($_REQUEST['report_status'] == $val) ? "selected" : ""; ?>><?= $val; ?></option><?
        	}*/ ?>
        </select>

		<button class="btn btn-primary rounded-pill px-3" type="button" name="report_filter" value="go" onClick="this.form.submit()">submit</button>
	</div>
</div>

<div class="row my-3">

<table id="report" class="display" cellspacing="2" cellpadding="4" border="0" class="table table-striped">	
    
	<thead>
    	<tr>
    	<th>Date</th>
    	<th>Url</th>
    	<th>HTTP Status</th>
    	<th>Origin</th>	
    	<th>Link Type</th>	
	<th>Status</th>
    	</tr>
	</thead>
	
	<tbody>
    <?
    if (is_array($aReport))
    {
        foreach($aReport as $aRow) { ?>
        	<tr> 
        	<td class="col-1"><?= $aRow['report_date']; ?></td>
        	<td class="col-5"><a href="<?= $aRow['url']; ?>" target="_new"><?= $aRow['url']; ?></a></td>
        	<?php
		$status = 1;
        	$css_class = "";
        	if (substr_count($aRow['http_status'], "200") >= 1)
        	{
		   $status = 0;
        	   $css_class = "alert alert-success";
        	} elseif (substr_count($aRow['http_status'], "30") >= 1)
        	{
        	    $css_class = "alert alert-warning";
        	} else {
        	    $css_class = "alert alert-danger";
        	}
        	?>
		<?
		if (trim($aRow['http_status']) == "") {
			$aRow['http_status'] = "DNS / Connect";
		}
		?>
        	<td class="col-1 <?= $css_class; ?>"><?= $aRow['http_status']; ?></td>
        	<td class="5"><a href="<?= $aRow['origin_url']; ?>" target="_new"><?= $aRow['origin_url']; ?></a></td>
        	<?php 

        	switch($aRow['origin_type'])
        	{
        	    case LINK_ORIGIN_COMPANY_URL :
        	        $sType = "COMPANY URL";
        	        break;
        	    case LINK_ORIGIN_COMPANY_APPLY :
        	        $sType = "COMPANY APPLY";
        	        break;
        	    case LINK_ORIGIN_PLACEMENT_URL :
        	        $sType = "PLACEMENT URL";
        	        break;
        	    case LINK_ORIGIN_PLACEMENT_APPLY :
        	        $sType = "PLACEMENT APPLY";
        	        break;        	        
        	}

        	?>
        	<td class="col-1"><?= $sType; ?></td><?
		$label = ($status == 0) ? "OK" : "ERROR"; ?>
		<td class="<?= $css_class; ?>"><?= $label; ?></td>
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
    	"bSort" : true
    });

});

</script>

</div>
</div>

<?
print $oFooter->Render();

?>
