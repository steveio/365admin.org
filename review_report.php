<?php


include_once("./includes/header.php");
include_once("./includes/footer.php");


if (!$oAuth->oUser->isValidUser) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");



$oReview = new Review();

if ($oAuth->oUser->isAdmin) {

    $oCompany = new Company($db);
    $d['company_select_ddlist'] = $oCompany->getCompanyNameDropDown($_REQUEST['p_company'],null,'p_company',true);
    
	$aApprove = array();
	$aReject  = array();

	foreach($_REQUEST as $k => $v) {
		if (preg_match("/enq_/",$k)) {
		    $aBits = explode("_",$k);
		    $id = $aBits[1];
		    $mode = isset($aBits[2]) ? $aBits[2] : null;
			if (isset($_REQUEST['bulk_action']) && $_REQUEST['bulk_action'] != "") {
				if ($_REQUEST['bulk_action'] == "approve") $aApprove[] = $id;
				if ($_REQUEST['bulk_action'] == "reject") $aReject[] = $id;
			} else {
			    if ($mode == "approve") $aApprove[] = $id;
			    if ($mode == "reject") $aReject[] = $id;
			}
		}
	}

	$iApproved = 0;
	$iRejected = 0;

	if (count($aApprove) >= 1) {
		foreach($aApprove as $id) {
			if ($oReview->SetStatus($id,1))
			    $iApproved++;
		}
	}
		
	if (count($aReject) >= 1) {
		foreach($aReject as $id) {
			if ($oReview->SetStatus($id,2))
			    $iRejected++;
		}
	}
	$strMessage = "";
	if ($iApproved >= 1)
        $strMessage = "Approved ".$iApproved." row(s) \n";
    if ($iRejected >= 1)
        $strMessage .= "Rejected ".$iRejected." row(s) ";

}

$aOptions = array();
$aOptions['report_date_from'] = "";
$aOptions['report_date_to'] = "";
if (isset($_REQUEST['report_status']) && $_REQUEST['report_status'] != "ALL")
    $aOptions['report_status'] = $_REQUEST['report_status'];

if (!isset($_REQUEST['report_all']))
{
    if ($oAuth->oUser->isAdmin)
    {
        $strDateRange = isset($_REQUEST['daterange']) ? $_REQUEST['daterange'] : date("d-m-Y",strtotime("-3 months"))." - ".date("d-m-Y");
    } else {
        $strDateRange = isset($_REQUEST['daterange']) ? $_REQUEST['daterange'] : date("d-m-Y",strtotime("-5 year"))." - ".date("d-m-Y");
    }
    $aDate = explode(" - ", $strDateRange);
    $aOptions['report_date_from'] = preg_replace("/\//","-",$aDate[0]);
    $aOptions['report_date_to'] = preg_replace("/\//","-",$aDate[1]);
}

/* filtering by company */
$company_id = NULL;
if (!$oAuth->oUser->isAdmin) {
    $company_id = $oAuth->oUser->company_id; /* non admin can see only their own enquiries */
} elseif (is_numeric($_REQUEST['p_company'])) {
    $company_id = $_REQUEST['p_company']; /* admin is viewing report filtered by company */
}

$aOptions['company_id'] = $company_id;


$aReport = $oReview->GetReport($aOptions);


print $oHeader->Render();

?>

<div class="container">
<div class="align-items-center justify-content-center">

<h1>Reviews</h1>


<form name="report_filter" enctype="multipart/form-data" action="" method="POST">

<div class="row my-3">

	<div class="col-12">

        <label for="daterange">Date range:</label>
        <input type="text" name="daterange" value="<?= $strDateRange; ?>" />
        
        <label for="daterange">Select all:</label>
        <input type="checkbox" id="" name="report_all" <?= (isset($_REQUEST['report_all'])) ? "checked" : ""; ?>/>
        
        <label for="daterange">By status:</label>
        <select id="" name="report_status">
        	<option value="ALL">ALL</option>
        	<option value="0" <?= ($_REQUEST['report_status'] == "0") ? "selected" : ""; ?>>PENDING</option>
        	<option value="1" <?= ($_REQUEST['report_status'] == "1") ? "selected" : ""; ?>>APPROVED</option>
        	<option value="2"<?= ($_REQUEST['report_status'] == "2") ? "selected" : ""; ?>>REJECTED</option>
        </select>
	</div>
</div>
	
<div class="row my-3">
	<div class="col-8">
    <? if ($oAuth->oUser->isAdmin) {  ?>
    	<label for="daterange">By Company:</label><?= $d['company_select_ddlist']; ?>
    <? } ?>
    </div>
</div>

<button class="btn btn-primary rounded-pill px-3" type="button" name="report_filter" value="go" onClick="this.form.submit()">submit</button>

<? if (strlen($strMessage) >= 1) { ?>
    <div class="alert alert-success" role="alert">
    <?= $strMessage; ?>
    </div>
<?php } ?>

<?php 
if ($oAuth->oUser->isAdmin) 
{
?>
<div class="row">
<div style="clear: both;">
    <div style="float: right;">
		<select name="bulk_action">
			<option value="">select</option>
			<option value="approve">approve selected</option>
			<option value="reject">reject selected</option>
		</select>
		<input type="button" name="go_batch" value="go" onClick="this.form.submit()" />
	</div>
</div>
</div>
<?php 
}
?>


<div class="row my-3">

<table id="report" class="display" cellspacing="2" cellpadding="4" border="0" class="table table-striped">	
    
	<thead>
    	<tr><?

	$aRow = $aReport[0];
    if (is_array($aRow))
    {
        $aKeys = array_keys($aRow);
        foreach($aKeys as $idx => $key) { ?>
        	<th><?= $key; ?></th><? 
        } 
    } ?>
    <?php 
    if ($oAuth->oUser->isAdmin) 
    {
    ?>
    	<th>edit</th>
    	<th>approve</th>
    	<th>reject</th>
    	<th>bulk</th>
    <?php 
    }
    ?>	
    	</tr>
	</thead>
	
	<tbody>
    
    <?
    if (is_array($aReport))
    {
        foreach($aReport as $aRow) { ?>
        	<tr><?php 
            foreach($aRow as $key => $value)
            { ?>
        		<td id="<?= $key; ?>" valign="top"><?= $value; ?></td><? 
            } ?>
            <?php 
            if ($oAuth->oUser->isAdmin) 
            {
            ?>
        	<td width="20px"><a href="../edit_review/?&id=<?= $aRow['post_id'] ?>" target="_new">edit</a></td>
        	<td width="20px"><input type="submit" onclick="javascript: return confirm('Are you sure you wish to approve this review?');" name="enq_<?= $aRow['post_id'] ?>_approve" value="approve" /></td>
        	<td width="20px"><input type="submit" onclick="javascript: return confirm('Are you sure you wish to reject this review?');" name="enq_<?= $aRow['post_id'] ?>_reject" value="reject" /></td>
        	<td width="20px" valign="top"><input type="checkbox" id="enq_<?= $aRow['post_id'] ?>" name="enq_<?= $aRow['post_id'] ?>" value="approve" /></td>
        	<?php 
            }
        	?>
        	</tr><?
        } 
    } ?>
    </tbody>
    </table>

</form>

</div>


<script>

$(document).ready(function() {

    $(function() {
      $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        locale: {
            format: 'DD-MM-YYYY'
        }
      }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      });
    });

    $('#report').DataTable({
    	"pageLength": 100,
    	"bSort" : false
    });

});

</script>

</div>
</div>

<?
print $oFooter->Render();

?>
