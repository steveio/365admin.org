<?php


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
        case 2 : // approved & sent (including auto-response codes)
            $aOptions['report_status'] = array(1,2,5,7);
            break;
        case 3 : // rejected
            $aOptions['report_status'] = array(3);
            break;
        case 4 : // failed
            $aOptions['report_status'] = array(4,6);
            break;
    }
    
}

if ($oAuth->oUser->isAdmin)
{
    $strDateRange = isset($_REQUEST['daterange']) ? $_REQUEST['daterange'] : date("d-m-Y",strtotime("-1 months"))." - ".date("d-m-Y",strtotime("+1 day"));
} else {
    $strDateRange = isset($_REQUEST['daterange']) ? $_REQUEST['daterange'] : date("d-m-Y",strtotime("-3 year"))." - ".date("d-m-Y",strtotime("+1 day"));
}
$aDate = explode(" - ", $strDateRange);

$t = strtotime($aDate[0]);
$startDate = date('Y-m-d',$t);

$t = strtotime($aDate[1]);
$endDate = date('Y-m-d',$t);


$aOptions['report_date_from'] = $startDate;
$aOptions['report_date_to'] = $endDate;



if (isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == "report_approve")
{
    $aEnquiry = $oEnquiry->GetAll($aOptions);
} else if ($_REQUEST['report_type'] == "report_stats")
{
    $aResultCompany = $oEnquiry->GetStatsCompany($aOptions);
    $aResultPlacement = $oEnquiry->GetStatsPlacement($aOptions);
}

print $oHeader->Render();

?>
    
<div class="container">
<div class="align-items-center justify-content-center">

<h1>Enquiry Report</h1>   

<form enctype="multipart/form-data" name="enquiry_report" id="enquiry_report" action="" method="POST">

<div class="row my-3">

	<div class="col-12 my-3">
		<label for="approve">Approve / Reject:</label>
	    <input type="radio" id="report_approve" name="report_type" value="report_approve" <?= (!isset($_REQUEST['report_type']) || $_REQUEST['report_type'] == "report_approve") ? "checked" : ""; ?> />
		<label for="approve">Stats:</label>
	    <input type="radio" id="report_stats" name="report_type" value="report_stats" <?= (!isset($_REQUEST['report_type']) || $_REQUEST['report_type'] == "report_stats") ? "checked" : ""; ?> />

	</div>

	<div class="col-6">
		<label for="daterange">Date range:</label>
	    <input type="text" name="daterange" value="<?= $strDateRange; ?>" />
	</div>

	<div class="col-6">
        <label for="daterange">By Status:</label>
        <select id="" name="report_status">
        	<option value="ALL" <?= ($_REQUEST['report_status'] == "ALL") ? "selected" : ""; ?>>ALL</option>
        	<option value="0" <?= ($_REQUEST['report_status'] == "0") ? "selected" : ""; ?>>PENDING</option>
        	<option value="2"<?= ($_REQUEST['report_status'] == "2") ? "selected" : ""; ?>>APPROVED (SENT)</option>
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

<button class="btn btn-primary rounded-pill px-3" type="submit" name="report_filter" value="submit">submit</button>

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

});

</script>

<?php 
if (isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == "report_approve")
{
?>    
        
    <form enctype="multipart/form-data" name="enquiry_result" id="enquiry_result" action="" method="POST">
    
    	
    <? if (strlen($strMessage) >= 1) { ?>
        <div class="alert alert-success my-3" role="alert">
        <?= $strMessage; ?>
        </div>
    <?php } ?>
    
    
    
    
    <?php if ($oAuth->oUser->isAdmin) { ?>
    <div class="row">
    <div style="clear: both;">
        <div style="float: right;">
    		Bulk Action:
        		<select name="bulk_action">
        			<option value="">select</option>
        			<option value="approve">approve selected</option>
        			<option value="reject">reject selected</option>
        		</select>
        		<input class="" type="button" name="go_batch" value="go" onClick="this.form.submit()" />
        </div>
    </div>
    </div>
    <?php } ?>
    
    
    <div class="row my-3">
    
    
    <table id="report" class="display" cellspacing="2" cellpadding="4" border="0" class="table table-striped">	
    
    <thead>
    <tr>
    	<th>date</th>
    	<th>type</th>
    	<th>about</th>
    	<th>from</th>
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
    			<br />
    			(<?= $oEnquiry->GetCompanyEmail() ?>)
    		</td>
    		<td width="" valign="top"><?= $oEnquiry->GetName() ."<br /> (".$oEnquiry->GetEmail().")<br />".$oEnquiry->GetCountryName()." <br />".$oEnquiry->GetIpAddr() ?></td>
    		<td width="" valign="top">
    			<?= $oEnquiry->GetEnquiry() ?>
            	<? if ($oEnquiry->GetEnquiryType() == "BOOKING") { ?>
            		<br /><br />
            		Group Size: <?= $oEnquiry->GetGroupSize(); ?>, 
            		Budget: <?= $oEnquiry->GetBudget(); ?>, 
            		Dept Date: <?= $oEnquiry->GetDeptDate(); ?>
            	<? } ?>
    
    		</td>
    		<td width="" valign="top"><?= $oEnquiry->GetShortStatusLabel() ?>
    		<? if (strlen($oEnquiry->GetDeliveryStatus()) > 1) { ?>
    			<?
    			$btn = ($oEnquiry->GetDeliveryStatus() == "sent") ? "success" : "danger";
    			?>
    			<button type="button" class="btn btn-outline-<?= $btn ?> btn-sm" data-bs-toggle="modal" data-bs-target="#myModal">
    		        Mail Log
    			</button>
    <div class="modal" id="myModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-body">
    <?= $oEnquiry->GetDeliveryLogMsg(); ?>
          </div>
        </div>
      </div>
    </div>
    		<? } ?>
    		</td>
    <?php if ($oAuth->oUser->isAdmin) { ?>
        	<td width="20px"><input type="submit" class="btn btn-primary" onclick="javascript: return confirm('Are you sure you wish to approve this entry?');" name="enq_<?= $oEnquiry->GetId() ?>_approve" value="approve" /></td>
        	<td width="20px"><input type="submit" class="btn btn-primary" onclick="javascript: return confirm('Are you sure you wish to reject this entry?');" name="enq_<?= $oEnquiry->GetId() ?>_reject" value="reject" /></td>
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

    $('#report').DataTable({
    	"pageLength": 100,
    	"bSort" : false 
    });

});

</script>
    
    
    </div>
    </div>

<?
} else if (isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == "report_stats")
{
?>    

        
    <form enctype="multipart/form-data" name="enquiry_result" id="enquiry_result" action="" method="POST">
    
    	
    <? if (strlen($strMessage) >= 1) { ?>
        <div class="alert alert-success my-3" role="alert">
        <?= $strMessage; ?>
        </div>
    <?php } ?>
    
    <div class="row my-3">

	<h3>Results: Company</h3>

    <? if (count($aResultCompany) < 1) { ?>
        <div class="alert alert-warning my-3" role="alert">
        0 Results 
        </div>    
    <? } else { ?>
    
        <table id="report_company" class="display" cellspacing="2" cellpadding="4" border="0" class="table table-striped">	
        
        <thead>
        <tr>
        	<th>Count</th>
        	<th>Company</th>
        	<th>Url</th>
        </tr>
        </thead>
        
        <tbody>
        	
        <? foreach($aResultCompany as $aRow) { ?>
        	<tr>
        		<td width="" valign="top"><?= $aRow['count'] ?></td>
        		<td width="" valign="top"><?= $aRow['company_name'] ?></td>
        		<td width="" valign="top"><?= $aRow['url'] ?></td>
        	</tr>    
        <? } ?>
        
        </tbody>
        </table>
	<? } ?>    
    </div>


    <div class="row my-3">

	<h3>Results: Placement</h3>

    <? if (count($aResultCompany) < 1) { ?>
        <div class="alert alert-warning my-3" role="alert">
        0 Results 
        </div>    
    <? } else { ?>
    
        <table id="report_placement" class="display" cellspacing="2" cellpadding="4" border="0" class="table table-striped">	
        
        <thead>
        <tr>
        	<th>Count</th>
        	<th>Placement</th>
        	<th>Company</th>
        	<th>Url</th>
        </tr>
        </thead>
        
        <tbody>
        	
        <? foreach($aResultPlacement as $aRow) { ?>
        	<tr>
        		<td width="" valign="top"><?= $aRow['count'] ?></td>
        		<td width="" valign="top"><?= $aRow['placement_name']; ?></td>
        		<td width="" valign="top"><?= $aRow['company_name']; ?></td>
        		<td width="" valign="top"><?= $aRow['url'] ?></td>
        	</tr>    
        <? } ?>
        
        </tbody>
        </table>
	<? } ?>    
    
    </div>
    
    </form>

<script>

$(document).ready(function() {

    $('#report_placement').DataTable({
    	"pageLength": 50,
    	"bSort" : false 
    });
    $('#report_company').DataTable({
    	"pageLength": 50,
    	"bSort" : false 
    });


});

</script>


<?php 
}
print $oFooter->Render();

?>
