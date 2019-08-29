<?php

include_once("./includes/header.php");
include_once("./includes/footer.php");
include_once("/www/vhosts/oneworld365.org/htdocs/classes/review.class.php");

//include("./footer_new.php");


if (!$oAuth->oUser->isValidUser) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");



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



if ($oAuth->oUser->isAdmin) {
    
    $iPage = isset($_REQUEST['Page']) ? $_REQUEST['Page'] : 0;
    $iPageSize = 30;
    $iStart = ($iPage > 1) ? (($iPage -1) * $iPageSize) : 0;

	$aReviewPending = $oReview->Get(null,null,0);
	if (!$aReviewPending) $aReviewPending = array();
	$aReviewProcessed = $oReview->Get(null,null,null,$iPageSize, $iStart);
	
	$oPager = new PagedResultSet();
	$oPager->SetResultsPerPage(30);
	$oPager->GetByCount($oReview->GetTotalReviews(),"Page");
	
}


print $oHeader->Render();

?>

<!-- BEGIN Page Content Container -->
<div class="page_content content-wrap clear">
<div class="row pad-tbl clear">



<? if ($oAuth->oUser->isAdmin) {  ?>

	<form enctype="multipart/form-data" name="process_enquiry" id="process_enquiry" action="#" method="POST">

	<div id='row800'>
	
	<h1>Reviews (Pending)</h1>
	
	<table cellspacing="2" cellpadding="4" border="0" width="800px">	
	
	<tr>
		<th>name</th>
		<th>email</th>
		<th>nationality</th>
		<th>age</th>
		<th>gender</th>
		<th>rating</th>
		<th colspan=3>&nbsp;</th>		
	</tr>
		
	<? foreach($aReviewPending as $oReview) { 
		$strDetails = "";
		if ($oReview->GetLinkTo() == 'COMPANY')
		{
		  $oCompany = new Company($db);
		  $objCompany = $oCompany->GetById($oReview->GetLinkId(),"title,url_name");
		  $strDetails = "Company: ".$objCompany->title;
		} elseif ($oReview->GetLinkTo() == 'PLACEMENT') {
                  $oProfile = new PlacementProfile();
                  $objProfile = $oProfile->GetProfileById($oReview->GetLinkId(),$key = "PLACEMENT_ID");
                  $strDetails = "Placement: ".$objProfile->company_name." : ".$objProfile->title;
		} elseif ($oReview->GetLinkTo() == 'ARTICLE') {
		    $oArticle = new Article();
		    $oArticle->GetById($oReview->GetLinkId());
		    $strDetails = "Article: ".$oArticle->GetTitle();
		}

	?>
		<? $class = ($class == "hi") ? "" : "hi"; ?>
		<tr class='<?= $class ?>'>
			<td width="80px" valign="top"><?= $oReview->GetName() ?></td>
			<td width="80px" valign="top"><?= $oReview->GetEmail() ?></td>
			<td width="80px" valign="top"><?= $oReview->GetNationality() ?></td>
			<td width="20px" valign="top"><?= $oReview->GetAge() ?></td>
			<td width="20px" valign="top"><?= $oReview->GetGender() ?></td>
			<td width="60px" valign="top"><?= $oReview->GetRating() ?></td>
			
			<td width="20px"><input type="submit" name="enq_<?= $oReview->GetId() ?>" value="approve" /></td>
			<td width="20px"><input type="submit" onclick="javscript: return confirm('Are you sure you wish to reject this review?');" name="enq_<?= $oReview->GetId() ?>" value="reject" /></td>
			<td width="20px" valign="top"><input type="checkbox" id="enq_<?= $oReview->GetId() ?>" name="enq_<?= $oReview->GetId() ?>" value="approve" /></td>
		</tr>
		<tr class='<?= $class ?>'>
                        <td>For:</td>
                        <td colspan="6"><?= $strDetails; ?></td>
		</tr>
		<tr class='<?= $class ?>'>
			<td>Title:</td>
			<td colspan="6" width="200px" valign="top"><?= html_entity_decode($oReview->GetTitle()); ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr class='<?= $class ?>'>
			<td>Review:</td>
			<td colspan="6" width="200px" valign="top"><?= html_entity_decode($oReview->GetReview()) ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		
	<? } ?>
	<tr class="hi">
		<td colspan="9" align="right">
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
<? } ?>


<hr />


<div id='row800' style='margin-top: 40px;'>
<?php 
$iPage = isset($_REQUEST['Page']) ? $_REQUEST['Page'] : 1;

$strPage = "Page ".$iPage." of ".$oPager->GetNumPages();
?> 
<h1>Reviews (<?= $strPage; ?>)</h1>



<table cellspacing="2" cellpadding="4" border="0" width="800px">

	<tr>
		<th>name</th>
		<th>email</th>
		<th>nationality</th>
		<th>age</th>
		<th>gender</th>
		<th>title</th>
		<th>rating</th>
		<th>status</th>		
	</tr>
<? 
foreach($aReviewProcessed as $oReview) {
                $strDetails = "";
                if ($oReview->GetLinkTo() == 'COMPANY')
                {
                  $oCompany = new Company($db);
                  $objCompany = $oCompany->GetById($oReview->GetLinkId(),"title,url_name");
                  $strDetails = "Company: ".$objCompany->title;
                } elseif ($oReview->GetLinkTo() == "PLACEMENT") {
                  $oProfile = new PlacementProfile();
                  $objProfile = $oProfile->GetProfileById($oReview->GetLinkId(),$key = "PLACEMENT_ID");
                  $strDetails = "Placement: ".$objProfile->company_name." : ".$objProfile->title;
                } elseif ($oReview->GetLinkTo() == 'ARTICLE') {
                    $oArticle = new Article();
                    $oArticle->GetById($oReview->GetLinkId());
                    $strDetails = "Article: ".$oArticle->GetTitle();
                }

?>
	<? $class = ($class == "hi") ? "" : "hi"; ?>
		<tr class='<?= $class; ?>'>
			<td width="80px" valign="top"><?= $oReview->GetName() ?></td>
			<td width="80px" valign="top"><?= $oReview->GetEmail() ?></td>
			<td width="80px" valign="top"><?= $oReview->GetNationality() ?></td>
			<td width="20px" valign="top"><?= $oReview->GetAge() ?></td>
			<td width="20px" valign="top"><?= $oReview->GetGender() ?></td>
			<td width="160px" valign="top"><?= html_entity_decode($oReview->GetTitle()) ?></td>
			<td width="60px" valign="top"><?= $oReview->GetRating() ?></td>
			<td width="60px" valign="top"><?= $oReview->GetStatusLabel() ?></td>
		</tr>
		<tr>
		<td>For:</td>
		<td colspan="7"><?= $strDetails ?></td>
		</tr>
		<tr class='hi'>
			<td>&nbsp;</td>
			<td colspan="6" width="200px" valign="top"><?= html_entity_decode($oReview->GetReview()) ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	
	<?
} 
?>
</table>

<div id="pager" class="row800">
<?= $oPager->RenderHTML(); ?>
</div>

</div>

<? if ($oAuth->oUser->isAdmin) {  ?>
</form>
<? } ?>

</div>
</div>
<!-- END Page Content Container -->

<?
print $oFooter->Render();

?>
