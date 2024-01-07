<?php

require_once("./conf/config.php");
require_once("./init.php");
require_once("./conf/brand_config.php");

include_once("./includes/header.php");
include_once("./includes/footer.php");


include_once("/www/vhosts/oneworld365.org/htdocs/classes/review.class.php");



if (!$oAuth->oUser->isValidUser || !$oAuth->oUser->isAdmin) AppError::StopRedirect($sUrl = $_CONFIG['url']."/login",$sMsg = "ERROR : You must be authenticated.  Please login to continue.");

if (!is_numeric($_REQUEST['id']))
{
    die("Invalid request");
}

$id = $_REQUEST['id'];

$oReview = new Review();

$aResponse = array();
$aResponse['error'] = '';
$aResponse['msg'] = '';

if (isset($_POST['review-edit']))
{
    $aParams = array();
    foreach($_POST as $k => $v)
    {
        if (strstr($k, 'review-') !== false)
        {
            $k = str_replace('review-', '', $k);
            $aParams[$k] = htmlentities($v,ENT_QUOTES,"UTF-8");
        }
    }
    Validation::AddSlashes($aParams);
    $oReview->SetFromArray($aParams, false);
    $oReview->SetId($id);

    if (!$oReview->Validate($aResponse))
    {
        $aResponse['status'] = 1;
        $aResponse['error'] = 'Failed validation';

    }
    if (!$oReview->Update($aResponse))
    {
        $aResponse['status'] = 1;
        $aResponse['error'] = 'An error occured during update';
    } else {
        
        $aResponse['status'] = 0;
        $aResponse['msg'] = 'Success: updated review / comment.';
    }
}

$oReview = new Review();
$oReview->GetById($id);


print $oHeader->Render();

?>



<div class="container">
<div class="align-items-center justify-content-center">


<form enctype="multipart/form-data" name="edit_review" id="edit_review" action="#" method="POST">


<h1>Edit Review / Comment</h1>


<? if (isset($aResponse['error']) && strlen($aResponse['error']) >= 1) { ?>
    <div class="alert alert-warning" role="alert">
    <?= $aResponse['error']; ?></h3>
    </div>
<?php } ?>

<? if (isset($aResponse['msg']) && strlen($aResponse['msg']) >= 1) { ?>
    <div class="alert alert-success" role="alert">
    <?= $aResponse['msg']; ?></h3>
    </div>
<?php } ?>


<div id='row'>


    <p>Linked to:  <b><?= $oReview->GetLinkedContentDesc(); ?></b></p>
    
    <p>Submitted: <?= $oReview->GetDate() ."  IP Address: ".$oReview->GetIpAddr(); ?>
	 <div id="" class="" style="">

		<div id="review-error" class="span12 text-error"></div>
		<div id="review-msg" class="span12 text-success"></div>

		<div id="review-add-form" class="form-group row">

			<input type="hidden" id="id" name="id" value="<?= $oReview->GetId(); ?>" class="form-control" />
		
			<input type="hidden" id="review-link-id" name="review-link_id" value="<?= $oReview->GetLinkId(); ?>" class="form-control" />
			<input type="hidden" id="review-link-to" name="review-link_to" value="<?= $oReview->GetLinkTo(); ?>" class="form-control" />
		
		  	<div class="col-6">
				<label for="review-name">Name:</label>
				<input type="text" id="review-name" name="review-name"  maxlength="45" class="form-control" value="<?= $oReview->GetName(); ?>" />
			</div>
		
		  	<div class="col-6">
				<label for="review-email">Email:</label>
				<input type="text" id="review-email" name="review-email"  maxlength="50" class="form-control" value="<?= $oReview->GetEmail(); ?>" />
			</div>
		</div>
		
		<div class="row">
		  	<div class="col-4">
				<label for="review-nationality">Nationality:</label>
				<input type="text" id="review-nationality" name="review-nationality"  maxlength="32" class="form-control" value="<?= $oReview->GetNationality(); ?>" />
			</div>
			<div class="col-4">
				<label for="review-age">Age:</label>
				<input type="text" id="review-age" name="review-age"  maxlength="4" class="form-control" value="<?= ($oReview->GetAge() > 1) ? $oReview->GetAge() : ""; ?>" />
			</div>
			<div class="col-4">
				<label for="review-gender">Gender:</label>
				<select id="review-gender" name="review-gender" class="form-select">
					<option value="NULL"></option>
					<option value="M" <?= ($oReview->GetGender() == "M") ? "selected" : ""; ?> >Male</option>
					<option value="F" <?= ($oReview->GetGender() == "F") ? "selected" : ""; ?> >Female</option>
				</select>
			</div>
		</div>
	
		<div class="row my-3">
				<label for="review-title">Review Title:</label>
				<input type="text" id="review-title" name="review-title" maxlength="128" class="form-control" value="<?= $oReview->GetTitle(); ?>" />
		</div>
	
		<div class="row my-3">
				<label for="review-review">Review:</label>
				<textarea id="review-review" name="review-review" style="height: 200px;" class="form-control" /><?= $oReview->GetReview(); ?></textarea>
		</div>
		
		<div class="row my-3">
				<label for="review-rating">Rating (0-5):</label>
				<input type="text" id="review-rating" name="review-rating"  maxlength="4" class="form-control" value="<?= ($oReview->GetRating() > 1) ? $oReview->GetRating() : ""; ?>" />
		</div>

		<div class="row my-3">
			<label for="review-status">Status:</label>
			<select id="review-status" name="review-status" class="form-select">
				<option value="0" <?= ($oReview->GetStatus() == "0") ? "selected" : ""; ?> >Pending</option>
				<option value="1" <?= ($oReview->GetStatus() == "1") ? "selected" : ""; ?> >Approved</option>
				<option value="2" <?= ($oReview->GetStatus() == "2") ? "selected" : ""; ?> >Rejected</option>
			</select>
		</div>

		<div class="row my-3">    			
			<div class="col-3">				
				<button class="btn btn-primary rounded-pill px-3" id="review-btn" type="submit" value=" Submit " name="review-edit">submit</button>
				
			</div>
        </div>	
	</div>
	
</div>

</form>


</div>
</div>

<?
print $oFooter->Render();

?>
