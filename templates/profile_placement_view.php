<?php 

$oProfile = $this->Get("oProfile");
$oReviewTemplate = $this->Get("oReviewTemplate");
$aRelatedProfile = $this->Get('aRelatedProfile');
$aRelatedArticle = $this->Get('aRelatedArticle');

?>


<div class="container">
<div class="align-items-center justify-content-center">


<div class="row">
    <div class="col-12 my-3">
    <div class="col-8 sharethis-inline-share-buttons" style="display: block; float: right;"></div>
    </div>
</div>

<? if (strlen($this->Get('logo_img')) > 1) { ?>
	<div><?= $this->Get('logo_img') ?></div>
<? } ?>

<h1><?= $oProfile->GetTitle(); ?></h1>

<div class="row my-3">
    <div id="review-overallrating" class="col-3"></div>
    <div class="col-2"><?php if (is_object($oReviewTemplate) &  $oReviewTemplate->Get('HASREVIEWRATING') == true) {
        print "( ".$oReviewTemplate->Get('COUNT'). " Reviews ) ";
    } ?>
    </div>
</div>

<? if (in_array($oProfile->GetProfileType(),array(PROFILE_VOLUNTEER, PROFILE_TOUR))) { ?>
<div class="row my-3">
	<div class="col-12 summary-details">
		<b>Company :</b> <a href="<?= $oProfile->GetCompanyProfileUrl(); ?>" title="Find out more about <?= $oProfile->GetCompanyName(); ?>" style="color: #DD6900;"><?= $oProfile->GetCompanyName(); ?></a><br/>
		<?php 
		if (count($oProfile->GetActivityArray()) > 1 && count($oProfile->GetActivityArray()) < 3) {
			$label = "Activities: "; ?>
			<b><?= $label; ?></b> <?= $oProfile->GetActivityTxt(); ?><br/><?
		}
		?>
		<?php 
		if (count($oProfile->GetCountryArray()) > 1) {
		    if (count($oProfile->GetCountryArray()) > 3) { ?>
		    <b>Location:</b> Multiple Destinations<br/><?
		    } else { ?>
			<b>Countries:</b> <?= $oProfile->GetCountryTxt(); ?><br/><?
		    }
		}  
		?>
		<? if (strlen($oProfile->GetLocation()) > 1) { ?>
			<b>Location :</b> <?= $oProfile->GetLocation(); ?><br/>
		<? } ?>
		<? if (is_numeric($oProfile->GetDurationFromId())) { ?>
			<b>Duration:</b> <?= $oProfile->GetDurationFromLabel(); ?> to <?= $oProfile->GetDurationToLabel(); ?><br />
		<? } ?>
		<?php if (is_numeric($oProfile->GetPriceFromId()) && $oProfile->GetPriceFromLabel() != "0") { ?>
			<b>Approx Costs:</b> <?= $oProfile->GetPriceFromLabel(); ?> to <?= $oProfile->GetPriceToLabel(); ?>
			<?= $oProfile->GetCurrencyLabel(); ?><br />
		<?php } ?>

		<?php if (method_exists($oProfile,'GetGroupSizeLabel') && strlen($oProfile->GetGroupSizeLabel()) >1) { ?>
			<b>Group Size</b> <?= $oProfile->GetGroupSizeLabel() ?><br />
		<?php } ?>

		<? if (method_exists($oProfile, "GetCode") && strlen($oProfile->GetCode()) > 1) { ?>
			<b>Tour Code :</b> <?= $oProfile->GetCode(); ?>
		<? } ?>
	</div>
</div>
<?php } ?>

<? if ($oProfile->GetProfileType() == PROFILE_JOB) { /* JOB */ ?>
<div class="row my-3">
	<div class="col-12 summary-details">

	<b>Company :</b> <?= $oProfile->GetCompanyName(); ?><br/>
	<b>Country :</b> <?= $oProfile->GetCountryTxt(); ?><br/>
	<? if (strlen($oProfile->GetLocation()) > 1) { ?>
		<b>Location :</b> <?= $oProfile->GetLocation(); ?><br/>
	<? } ?>
	<? if (is_numeric($oProfile->GetDurationFromId())) { ?>
		<b>Duration:</b> <?= $oProfile->GetDurationFromLabel();
		if ($oProfile->GetDurationFromId() != $oProfile->GetDurationToId())
		{ ?>
		    to <?= $oProfile->GetDurationToLabel(); ?><?
		} ?><br />
	<? } ?>
	<? if (strlen($oProfile->GetContractTypeLabel()) > 1) { ?>
		<b>Contract :</b> <?= $oProfile->GetContractTypeLabel(); ?><br/>
	<? } ?>
	<? if (strlen($oProfile->GetStartDateExact()) > 1) { ?>
		<b>Start Date :</b> <?= $oProfile->GetStartDateExact(); ?><br/>
	<? } elseif(strlen($oProfile->GetStartDateMultiple()) > 1) { ?>
		<b>Start Dates :</b> <?= $oProfile->GetStartDateMultiple(); ?><br/>
	<? } ?>
	<? if (strlen($oProfile->GetClosingDate()) > 1) { ?>
		<b>Apply By :</b> <?= $oProfile->GetClosingDate(); ?><br/>
	<? } ?>
	<? if (strlen($oProfile->GetJobOptionsLabels()) > 1) { ?>
		<b>Benefits :</b> <?= $oProfile->GetJobOptionsLabels(); ?><br/>
	<? } ?>
	<? if (strlen($oProfile->GetReference()) > 1) { ?>
	<b>Job Ref :</b> <?= $oProfile->GetReference(); ?><br/>
	<? } ?>


	</div>
</div>
<? } ?>

<div class="row">

	<p class="lead"><?= $oProfile->GetDescShortPlainText(); ?></p>			

<? include("./templates/profile_images_view.php"); ?>

	<div class="article-body">
	<p><?= $oProfile->GetDescLongClean();?></p>
	</div>

	<? if (strlen(trim($oProfile->GetVideo())) > 1) { ?>
		<div class='col-12'>
		<h3>Video</h3>
			<?= $oProfile->GetVideo() ?>
		</div>
	<? } ?>		

	<div class="row">
	<? if ($oProfile->GetProfileType() == PROFILE_VOLUNTEER) { /* VOLUNTEER */ ?>
					
		<? if (strlen($oProfile->GetDurationText()) >= 1) { ?>
			<h3>Duration</h3>
			<p><?= $oProfile->GetDurationText() ?></p>
		<? } ?>
			
		<? if (strlen($oProfile->GetStartDates()) >= 1) { ?>
			<h3>Start Dates</h3>
			<p><?= nl2br($oProfile->GetStartDates()) ?></p>
		<? } ?>
		

		<? if (strlen($oProfile->GetBenefits()) >= 1) { ?>
			<h3>Costs / Benefits</h3>
			<p><?= nl2br($oProfile->GetBenefits()) ?></p>
		<? } ?>

		<? if (strlen($oProfile->GetRequirements()) >= 1) { ?>
			<h3>Requirements</h3>
			<p><?= nl2br($oProfile->GetRequirements()) ?></p>
		<? } ?>

	<? } ?>

	<? if ($oProfile->GetProfileType() == PROFILE_JOB) { /* JOB */ ?>
		<h3>Salary / Pay</h3>
		<p><?= $oProfile->GetSalary(); ?></p>

		<? if (strlen($oProfile->GetBenefits()) > 1) { ?>
			<h3>Benefits</h3>
			<p><?= $oProfile->GetBenefits() ?></p>
		<? } ?>

		<? if (strlen($oProfile->GetExperience()) > 1) { ?>
			<h3>Experience Required</h3>
			<p><?= $oProfile->GetExperience(); ?></p>
		<? } ?>
		
	<? } ?>

	<? if ($oProfile->GetProfileType() == PROFILE_TOUR) { /* TOUR */ ?>

		<? if (strlen($oProfile->GetItinery()) >= 1) { ?>					
			<h3>Itinerary</h3>
			<p><?= $oProfile->GetItinery(); ?></p>
		<? } ?>	
		
		<div class="row">
		<?php if (is_array($oProfile->GetTravelOptions()) && count($oProfile->GetTravelOptions()) >= 1) { ?>
			<div class="col-4">
			<h3>Travel</h3>
			<ul class='select_list'>
			<?php 
			foreach($oProfile->GetTravelOptions() as $li) {
				print $li;
			}
			?>
			</ul>
			</div>
		<?php } ?>

		<?php if (is_array($oProfile->GetAccomOptions()) && count($oProfile->GetAccomOptions()) >= 1) { ?>
			<div class="col-4">
			<h3>Accomodation</h3>
			<ul class='select_list'>
			<?php 
			foreach($oProfile->GetAccomOptions() as $li) {
				print $li;
			}
			?>
			</ul>
			</div>
		<?php } ?>

		<?php if (is_array($oProfile->GetMealOptions()) && count($oProfile->GetMealOptions()) >= 1) { ?>				
			<div class="col-4">
			<h3>Meals</h3>
			<ul class='select_list'>
			<?php 
			foreach($oProfile->GetMealOptions() as $li) {
				print $li;
			}
			?>
			</ul>
			</div>
		<?php } ?>
		</div>

		<div>
		<?  if (strlen($oProfile->GetPrice()) > 1) { ?>
			<h3>Tour Price</h3>
			<p><?= $oProfile->GetPrice(); ?></p>
		<? } ?>
			
		<?php if (strlen($oProfile->GetDates()) > 1) { ?>
			<h3>Start Dates</h3>
			<p><?= nl2br($oProfile->GetDates()) ?></p>
		<?php } ?>

		<?php if (strlen($oProfile->GetRequirements()) >1) { ?>
			<h3>Requirements</h3>
			<p><?= $oProfile->GetRequirements() ?></p>
		<?php } ?>

			
	<? } // end profile tour ?>


	<?php 
	$aButtonHtml = $this->Get('aEnquiryButtonHtml');
	if (is_array($aButtonHtml) && count($aButtonHtml) >= 1)
	{ ?>
        <div id="buttons" class="buttons row my-5">
        <div class="booking-enquiry">
    	<? if ($oProfile->GetProfileType() == PROFILE_JOB) { ?>
    		<h3>Apply / More Info</h3>
    	<? } else { ?>
    		<h2>Booking / Enquiry</h2>
    	<? } ?>
        <? 
        if (is_array($aButtonHtml))
        {
            foreach($aButtonHtml as $k => $v) {
                print $v;
            }
        } ?>    
        </div>
        </div>
    <? } ?>
</div>


<div class="row my-3">
	<h2><?= $oProfile->GetCompanyName(); ?> Reviews</h2>
<?php 
print $oReviewTemplate->Render();
?>
</div>


<?
if (is_array($aRelatedProfile) && count($aRelatedProfile) >= 1)
{ ?>
<div class="row my-3">
	<h2>Related Opportunities</h2>
	<?php 
	foreach($aRelatedProfile as $oProfile) 
	{
	   $oTemplate = new Template();
       $oTemplate->Set("oProfile", $oProfile);
       $oTemplate->LoadTemplate("profile_summary.php");
       print $oTemplate->Render();
    } ?>
</div><?
}
?>



<?
if (is_array($aRelatedArticle) && count($aRelatedArticle) >= 1)
{ ?>
<div class="row" style="my-3">
    <h3>Related Articles</h3>
    <div class="row"><?
    foreach($aRelatedArticle as $oArticle)
    {
            if (is_object($oArticle)) 
            {
                $oArticle->LoadTemplate("article_summary.php");
                print $oArticle->Render();
            }
    } ?>
    </div>
</div><?php
}
?>


<!--  END Placement Profile -->
</div>
</div>
