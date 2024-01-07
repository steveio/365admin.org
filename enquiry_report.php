<?php

include_once("./includes/header_fullwidth.php");
include_once("./includes/footer.php");



if (!$oAuth->oUser->isValidUser) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");


$oEnquiry = new Enquiry();

// only admin approves / rejects enquiries, companies have a read-only view
if ($oAuth->oUser->isAdmin) {

	$oCompany = new Company($db);
	$d['company_select_ddlist'] = $oCompany->getCompanyNameDropDown($_REQUEST['p_company'],null,'p_company',true);

	$aApprove = array();
	$aReject  = array();

	$iApproved = 0;
	$iRejected = 0;

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
	
	if (count($aApprove) >= 1) {
		foreach($aApprove as $id) {
			$oEnquiry->SetStatus($id,1);
			$iApproved++;
		}
	}

	if (count($aReject) >= 1) {
		foreach($aReject as $id) {
			$oEnquiry->SetStatus($id,3);
			$iRejected++;
		}
	}

	$strMessage = "";
	if ($iApproved >= 1)
	    $strMessage = "Approved ".$iApproved." row(s) \n";
    if ($iRejected >= 1)
        $strMessage .= "Rejected ".$iRejected." row(s) ";

}
 

/* filtering by company */
$company_id = NULL;
if (!$oAuth->oUser->isAdmin) {
	$company_id = $oAuth->oUser->company_id; /* non admin can see only their own enquiries */ 
} elseif (is_numeric($_REQUEST['p_company'])) {
	$company_id = $_REQUEST['p_company']; /* admin is viewing report filtered by company */
}


$aOptions = array();
$aOptions['report_date_from'] = null;
$aOptions['report_date_to'] = null;
$aOptions['report_status'] = array(0,1,2,3,4,5,6,7);
$aOptions['company_id'] = $company_id;
//$aOptions['limit'] = 25;


if (isset($_REQUEST['report_status']) && $_REQUEST['report_status'] != "ALL")
{
    switch($_REQUEST['report_status'])
    {
        case 0 : // pending
            $aOptions['report_status'] = array(0);
            break;
        case 1 : // approved
            $aOptions['report_status'] = array(1);
            break;
        case 2 : // sent (including auto-response codes)
            $aOptions['report_status'] = array(2,5,7);
            break;
        case 3 : // rejected
            $aOptions['report_status'] = array(3);
            break;
        case 4 : // failed
            $aOptions['report_status'] = array(4,6);
            break;
    }
}

$strDateRange = isset($_REQUEST['daterange']) ? $_REQUEST['daterange'] : date("d-m-Y",strtotime("-1 month"))." - ".date("d-m-Y");
$aDate = explode(" - ", $strDateRange);
$aOptions['report_date_from'] = preg_replace("/\//","-",$aDate[0]);
$aOptions['report_date_to'] = preg_replace("/\//","-",$aDate[1]);


$aEnquiry = $oEnquiry->GetAll($aOptions);


print $oHeader->Render();

?>

<div class="container">
<div class="align-items-center justify-content-center">


<form enctype="multipart/form-data" name="process_enquiry" id="process_enquiry" action="" method="POST">

<div class="row my-3">

	<div class="col-6">
		<label for="daterange">Date range:</label>
	    <input type="text" name="daterange" value="<?= $strDateRange; ?>" />
	</div>

	<div class="col-6">
        <label for="daterange">By Status:</label>
        <select id="" name="report_status">
        	<option value="ALL">ALL</option>
        	<option value="0" <?= ($_REQUEST['report_status'] == "0") ? "selected" : ""; ?>>PENDING</option>
        	<option value="1" <?= ($_REQUEST['report_status'] == "1") ? "selected" : ""; ?>>APPROVED</option>
        	<option value="2"<?= ($_REQUEST['report_status'] == "2") ? "selected" : ""; ?>>SENT</option>
        	<option value="3"<?= ($_REQUEST['report_status'] == "3") ? "selected" : ""; ?>>REJECTED</option>
        	<option value="4"<?= ($_REQUEST['report_status'] == "3") ? "selected" : ""; ?>>FAILED</option>
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



</div>
	
<? if (strlen($strMessage) >= 1) { ?>
    <div class="alert alert-success" role="alert">
    <h3><img src="/images/icon_green_tick.png" border="0" /><?= $strMessage; ?></h3>
    </div>
<?php } ?>


<div class="row my-3">

<h1>Enquiry Report</h1>

</div>

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


<div class="row my-3">


<table id="report" class="display" cellspacing="2" cellpadding="4" border="0" class="table table-striped">	

<thead>
<tr>
	<th>date</th>
	<th>type</th>
	<th>about</th>
	<th>from</th>
	<th>country</th>
	<th>enquiry</th>
	<th>status</th>
<?php if ($oAuth->oUser->isAdmin) { ?>
	<th>approve</th>
	<th>reject</th>
	<th>bulk</th>
<?php } ?>
</tr>
</thead>

<tbody>
	
<? foreach($aEnquiry as $oEnquiry) { ?>
	<tr>
		<td width="" valign="top"><?= $oEnquiry->GetDate() ?></td>
		<td width="" valign="top"><?= $oEnquiry->GetEnquiryTypeLabel() ?></td>
		<td width="" valign="top">
			<? if (strlen($oEnquiry->GetPlacementName()) > 1) { ?>
			<a class="" href="http://www.oneworld365.org/company/<?= $oEnquiry->GetCompanyUrlName() ."/". $oEnquiry->GetPlacementUrlName() ?>" title="<?= $oEnquiry->GetPlacementName() ?>"><?= $oEnquiry->GetPlacementName() ?></a>
			<br/>
			<? } ?>
			<a class="" href="http://www.oneworld365.org/company/<?= $oEnquiry->GetCompanyUrlName(); ?>" title="<?= $oEnquiry->GetCompanyName(); ?>"><?= $oEnquiry->GetCompanyName(); ?></a>
		</td>
		<td width="" valign="top"><?= $oEnquiry->GetName() ."<br /> (".$oEnquiry->GetEmail().") <br />".$oEnquiry->GetIpAddr() ?></td>
		<td width="" valign="top"><?= $oEnquiry->GetCountryName() ?></td>
		<td width="" valign="top">
			<?= $oEnquiry->GetEnquiry() ?>
        	<? if ($oEnquiry->GetEnquiryType() == "BOOKING") { ?>
        		<br /><br />
        		Group Size: <?= $oEnquiry->GetGroupSize(); ?>, 
        		Budget: <?= $oEnquiry->GetBudget(); ?>, 
        		Dept Date: <?= $oEnquiry->GetDeptDate(); ?>
        	<? } ?>

		</td>
		<td width="" valign="top"><?= $oEnquiry->GetShortStatusLabel() ?></td>
<?php if ($oAuth->oUser->isAdmin) { ?>
    	<td width="20px"><input type="submit" onclick="javascript: return confirm('Are you sure you wish to approve this entry?');" name="enq_<?= $oEnquiry->GetId() ?>_approve" value="approve" /></td>
    	<td width="20px"><input type="submit" onclick="javascript: return confirm('Are you sure you wish to reject this entry?');" name="enq_<?= $oEnquiry->GetId() ?>_reject" value="reject" /></td>
    	<td width="20px" valign="top"><input type="checkbox" id="enq_<?= $oEnquiry->GetId(); ?>" name="enq_<?= $oEnquiry->GetId() ?>" value="bulk" /></td>
<?php } ?>
	</tr>

<? } ?>

</tbody>
</table>

</div>
</form>



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
    	"bSort" : true
    });

});

</script>


</div>
</div>
<!-- END Page Content Container -->

<?
print $oFooter->Render();

?>
