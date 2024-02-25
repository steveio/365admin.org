<?php

require_once("./classes/LinkChecker.class.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isValidUser || !$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");



$oLinkChecker = new LinkChecker();
$aReport = $oLinkChecker->GetReport();


print $oHeader->Render();

?>

<div class="container">
<div class="align-items-center justify-content-center">

<h1>Link Status Report</h1>


<form name="report_filter" enctype="multipart/form-data" action="" method="POST">


<div class="row my-3">

<table id="report" class="display" cellspacing="2" cellpadding="4" border="0" class="table table-striped">	
    
	<thead>
    	<tr>
    	<th>Date</th>
    	<th>Url</th>
    	<th>HTTP Status</th>
    	<th>Origin</th>	
    	<th>Link Type</th>	
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
        	$css_class = "";
        	if (substr_count($aRow['http_status'], "200") >= 1)
        	{
        	   $css_class = "alert alert-success";
        	} elseif (substr_count($aRow['http_status'], "30") >= 1)
        	{
        	    $css_class = "alert alert-warning";
        	} else {
        	    $css_class = "alert alert-danger";
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
        	<td class="col-1"><?= $sType; ?></td>
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
