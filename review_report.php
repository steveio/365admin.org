<?php

include_once("./includes/header_fullwidth.php");
include_once("./includes/footer.php");
include_once("/www/vhosts/oneworld365.org/htdocs/classes/review.class.php");

//include("./footer_new.php");


if (!$oAuth->oUser->isValidUser || !$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");



$oReview = new Review();

if ($oAuth->oUser->isAdmin) {

	$aApprove = array();
	$aReject  = array();

	foreach($_REQUEST as $k => $v) {
		if (preg_match("/enq_/",$k)) {
			$id = preg_replace("/enq_/","",$k);
			if (isset($_REQUEST['go_batch'])) {
				if ($_REQUEST['bulk_action'] == "approve") $aApprove[] = $id;
				if ($_REQUEST['bulk_action'] == "reject") $aReject[] = $id;
			} else {
				if ($v == "approve") $aApprove[] = $id;
				if ($v == "reject") $aReject[] = $id;
			}
		}
	}
	
	if (count($aApprove) >= 1) {
		foreach($aApprove as $id) {
			if (DEBUG) Logger::Msg("Approve : id = ".$id);
			$oReview->SetStatus($id,1);			
		}
	}
		
	if (count($aReject) >= 1) {
		foreach($aReject as $id) {
			if (DEBUG) Logger::Msg("Reject : id = ".$id);
			$oReview->SetStatus($id,2);			
		}
	}

}



$aReport = $oReview->GetReport();




print $oHeader->Render();

?>

<!-- BEGIN Page Content Container -->
<div class="page_content content-wrap clear">
<div class="row pad-tbl clear">


<script>

$(document).ready(function() {
    $('#report').DataTable({
    	"pageLength": 100,
    	"bSort" : false
    });
});

</script>



<div id='' style='margin-top: 40px;'>

<h1>Reviews</h1>

<table id="report" class="display" cellspacing="2" cellpadding="4" border="0" width="1200px">

	<thead>
	<tr><?
$aRow = array_shift($aReport);

$aKeys = array_keys($aRow);
foreach($aKeys as $idx => $key) { ?>
	<th><?= $key; ?></th><? 
} ?>
	<th>edit</th>
	<th>approve</th>
	<th>reject</th>
	<th>bulk</th>	
	</tr>
	</thead>
	
	<tbody>

<?
foreach($aReport as $aRow) { ?>
	<tr><?php 
    foreach($aRow as $key => $value)
    { ?>
		<td id="<?= $key; ?>" valign="top"><?= $value; ?></td><? 
    } ?>
	<td width="20px"><a href="../edit_review/?&id=<?= $aRow['post_id'] ?>" target="_new">edit</a></td>
	<td width="20px"><input type="submit" name="enq_<?= $aRow['post_id'] ?>" value="approve" /></td>
	<td width="20px"><input type="submit" onclick="javscript: return confirm('Are you sure you wish to reject this review?');" name="enq_<?= $aRow['post_id'] ?>" value="reject" /></td>
	<td width="20px" valign="top"><input type="checkbox" id="enq_<?= $aRow['post_id'] ?>" name="enq_<?= $aRow['post_id'] ?>" value="approve" /></td>

	</tr><?
} ?>
</tbody>
</table>

<table>
	<tr class="hi">
		<td colspan="" align="right">
			<select name="bulk_action">
				<option value="approve">approve selected</option>
				<option value="reject">reject selected</option>
			</select>
			<input type="submit" name="go_batch" value="go" onClick="this.form.submit()" />
		
		</td>	
	</tr>
</table>


</div>

</form>

</div>
</div>
<!-- END Page Content Container -->

<?
print $oFooter->Render();

?>
