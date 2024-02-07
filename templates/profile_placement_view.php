<?php 

$oProfile = $this->Get("oProfile");


?>


<div class="container">
<div class="align-items-center justify-content-center">


    <div class="span12" style="margin: 20px;">
	<div class="pull-right sharethis-inline-share-buttons"></div>

	<? if (strlen($this->Get('company_logo')) >1) { ?>
		<div style=""><?= $this->Get('company_logo') ?></div>
	<? } ?>

	<h1><?= $oProfile->GetTitle(); ?></h1>

	<div id="review-overallrating" style="margin-bottom: 10px;"></div>


	<p>
	<? if (in_array($this->Get('profile_type'),array(PROFILE_VOLUNTEER, PROFILE_TOUR))) { ?>
		<b>Company :</b> <a href="<?= $this->Get('company_url'); ?>" title="Find out more about <?= $this->Get('company_name'); ?>" style="color: #DD6900;"><?= $this->Get('company_name'); ?></a><br/>
		<?php 
		if (count($oProfile->GetActivityArray()) > 1 && count($oProfile->GetActivityArray()) < 3) {
			$label = "Activities: "; ?>
			<b><?= $label; ?></b> <?= $this->Get('activity_txt'); ?><br/><?
		}
		?>
		<?php 
		if (count($oProfile->GetCountryArray()) > 1 && count($oProfile->GetCountryArray()) < 3) {
			$label = "Countries: "; ?>
			<b><?= $label; ?></b> <?= $this->Get('country_txt') ?><br/><?
		} 
		?>
		<? if (strlen($this->Get('location')) > 1) { ?>
			<b>Location :</b> <?= $this->Get('location'); ?><br/>
		<? } ?>
		<? if (is_numeric($oProfile->GetDurationFromId())) { ?>
			<b>Duration:</b> <?= $oProfile->GetDurationFromLabel(); ?> to <?= $oProfile->GetDurationToLabel(); ?><br />
		<? } ?>
		<?php if (is_numeric($oProfile->GetPriceFromId())) { ?>
			<b>Approx Costs:</b> <?= $oProfile->GetPriceFromLabel(); ?> to <?= $oProfile->GetPriceToLabel(); ?>
			<?= $oProfile->GetCurrencyLabel(); ?><br />
		<?php } ?>

		<? if (strlen($this->Get('code')) > 1) { ?>
			<b>Tour Code :</b> <?= $this->Get('code'); ?>
		<? } ?>
	<? } ?>

	<? if ($this->Get('profile_type') == PROFILE_JOB) { /* JOB */ ?>
		<b>Job Ref :</b> <?= $this->Get('reference'); ?><br/>
		<b>Company :</b> <?= $this->Get('company_name'); ?><br/>
		<b>Country :</b> <?= $this->Get('country_txt'); ?><br/>
		<? if (strlen($this->Get('location')) > 1) { ?>
			<b>Location :</b> <?= $this->Get('location'); ?><br/>
		<? } ?>
		<? if (strlen($this->Get('contract_type_label')) > 1) { ?>
			<b>Contract :</b> <?= $this->Get('contract_type_label'); ?><br/>
		<? } ?>
		<? if (strlen($this->Get('start_dt_exact')) > 1) { ?>
			<b>Start Date :</b> <?= $this->Get('start_dt_exact'); ?><br/>
		<? } elseif(strlen($this->Get('start_dt_multiple')) > 1) { ?>
			<b>Start Dates :</b> <?= $this->Get('start_dt_multiple'); ?><br/>
		<? } ?>
		<? if (strlen($this->Get('closing_dt')) > 1) { ?>
			<b>Apply By :</b> <?= $this->Get('closing_dt'); ?><br/>
		<? } ?>
	<? } ?>
	
	</p>
	
	<? if ($this->Get('profile_type') == PROFILE_JOB) { /* JOB */ ?>
	<h3>Job Description</h3>
	<? } ?>

	<div class='lead' style='padding-bottom: 20px;'>
	
	<p class="lead"><strong><?= $oProfile->GetDescShort(); ?></strong></p>			


    <div class="profile-image-container span12">
            <?php  
            if (is_array($oProfile->GetAllImages()) && count($oProfile->GetAllImages()) >= 1) 
            {
                $arrImage = $oProfile->GetAllImages();
	    $iImgCount = count($arrImage);
                $iDisplay = 3; ?>
                <?php
                for($i=0; $i<=$iDisplay; $i++) 
                {
                    $oImage = isset($arrImage[$i]) ? $arrImage[$i] : null;
                    if (is_null($oImage)) continue;

                    ?>      
                    <div class="<?= ($iImgCount == 1) ? "profile-image-single" : "profile-image"; ?>">
                    <?
                    if (strlen($oImage->GetHtml("_lf","")) > 1) { ?>
                        <?= $oImage->GetHtml("_lf",""); ?><?php
                    } else { ?>
                        <?= $oImage->GetHtml("_mf",""); ?><?php
                    } ?>
		</div><?
                } ?>
                <?php
                if (count($arrImage) > $iDisplay +1) { ?>
		<div id="image-viewall-lnk" class="pull-right"><h5><a href="#" id="image-viewall">View All >></a></h5></div> 
                    <div id="image-all" class="hide">
                    <ul class="unstyled"><?php
                    for($i=$iDisplay; $i<count($arrImage); $i++) 
                    {
                        $oImage = isset($arrImage[$i]) ? $arrImage[$i] : null;
                        if (is_null($oImage)) continue;
                        if (strlen($oImage->GetHtml("_lf","")) > 1) { ?>
                            <li style="margin-bottom: 10px;"><?= $oImage->GetHtml("_lf",""); ?></li><?php
                        } else { ?>
                            <li style="margin-bottom: 10px;"><?= $oImage->GetHtml("_mf",""); ?></li><?php
                        }
                    } ?>
                    </ul>
                    </div><?php 
                }
            } ?>
            <script>
        	$(document).ready(function(){ 
    			$('#image-viewall').click(function(e) {
 				   e.preventDefault();
 			       $('#image-all').show();
 			       $('#image-viewall-lnk').hide();
 			       return false;
	 			});
        	}); 
            </script>
    </div>

	<p><?= $oProfile->GetDescLong(); ?></p>

	<? if (strlen(trim($this->Get('video1'))) > 1) { ?>
		<div class='span12'>
		<h3>Video</h3>
			<?= $this->Get('video1') ?>
		</div>
	<? } ?>		
	
	<? if ($this->Get('profile_type') == PROFILE_VOLUNTEER) { /* VOLUNTEER */ ?>
					
		<? if (strlen($this->Get('duration_txt')) >= 1) { ?>
			<h3>Duration</h3>
			<p><?= $this->Get('duration_txt') ?></p>
		<? } ?>
			
		<? if (strlen($this->Get('start_dates')) >= 1) { ?>
			<h3>Start Dates</h3>
			<p><?= nl2br($this->Get('start_dates')) ?></p>
		<? } ?>
		

		<? if (strlen($this->Get('benefits')) >= 1) { ?>
			<h3>Costs / Benefits</h3>
			<p><?= nl2br($this->Get('benefits')) ?></p>
		<? } ?>

		<? if (strlen($this->Get('requirements')) >= 1) { ?>
			<h3>Requirements</h3>
			<p><?= nl2br($this->Get('requirements')) ?></p>
		<? } ?>

	<? } ?>
	
	
	<? if ($this->Get('profile_type') == PROFILE_TOUR) { /* TOUR */ ?>

		<? if (strlen($this->Get('itinery')) >= 1) { ?>
					
			<h3>Itinerary</h3>
			<p><?= $this->Get('itinery'); ?></p>
		<? } ?>	
		
			<div>
			<?php if (is_array($this->Get('REFDATA_TRAVEL_ARRAY')) && count($this->Get('REFDATA_TRAVEL_ARRAY')) >= 1) { ?>
				<div>
				<h3>Travel</h3>
				<ul class='select_list'>
				<?php 
				foreach($this->Get('REFDATA_TRAVEL_ARRAY') as $li) {
					print $li;
				}
				?>
				</ul>
				</div>
			<?php } ?>
			
			<?php if (is_array($this->Get('REFDATA_ACCOM_ARRAY')) && count($this->Get('REFDATA_ACCOM_ARRAY')) >= 1) { ?>
				<div>
				<h3>Accomodation</h3>
				<ul class='select_list'>
				<?php 
				foreach($this->Get('REFDATA_ACCOM_ARRAY') as $li) {
					print $li;
				}
				?>
				</ul>
				</div>
			<?php } ?>
			
			<?php if (is_array($this->Get('REFDATA_MEALS_ARRAY')) && count($this->Get('REFDATA_MEALS_ARRAY')) >= 1) { ?>				
				<div>
				<h3>Meals</h3>
				<ul class='select_list'>
				<?php 
				foreach($this->Get('REFDATA_MEALS_ARRAY') as $li) {
					print $li;
				}
				?>
				</ul>
				</div>
			<?php } ?>
			</div>
		

		<div>
		<?  if (strlen($this->Get('tour_price')) > 1) { ?>
			<h3>Tour Price</h3>
			<p><?= $this->Get('tour_price'); ?></p>
		<? } ?>

		<?php if (strlen($this->Get('included')) > 1) { ?>
			<h3>Included in Price</h3>
			<p><?= nl2br($this->Get('included')) ?></p>
		<?php } ?>

		<?php if (strlen($this->Get('local_payment')) > 1) { ?>
			<h3>Local Payment</h3>
			<p><?= $this->Get('local_payment') ?></p>
		<?php } ?>
			
		<?php if (strlen($this->Get('not_included')) > 1) { ?>				
			<h3>Not Included in Price</h3>
			<p><?= nl2br($this->Get('not_included')); ?></p>
		<?php } ?>
			
		<?php if (strlen($this->Get('dates')) > 1) { ?>
			<h3>Start Dates</h3>
			<p><?= nl2br($this->Get('dates')) ?></p>
		<?php } ?>

		<?php if (strlen($this->Get('grp_size')) > 1) { ?>
			<h3>Group Size</h3>
			<p><?= $this->Get('grp_size') ?></p>
		<?php } ?>
			
		</div>
		

			
	<? } // end profile tour ?>

	<? if ($this->Get('profile_type') == PROFILE_JOB) { /* JOB */ ?>
			<h3>Salary / Pay</h3>
			<p><?= $this->Get('job_salary') ?></p>

			<? if (strlen($this->Get('job_benefits')) > 1) { ?>
				<h3>Benefits</h3>
				<p><?= $this->Get('job_benefits') ?></p>
			<? } ?>

			<? if ($this->Get('live_in') == "t" || $this->Get('meals_inc') == "t" || $this->Get('pickup_inc') == "t") { ?>
			<h3>Extras</h3>
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="40px" align="left" valign="top"><input type="checkbox" name="live_in" id="live_in" class="text_input" disabled <?= ($this->Get('live_in') == "t") ? "checked" : "";  ?>  /><label for="live_in" class="checkbox_label">Live In</label></td>
					<td width="40px" align="left" valign="top"><input type="checkbox" name="meals_inc" id="meals_inc" class="text_input" disabled <?= ($this->Get('meals_inc') == "t") ? "checked" : "";  ?> /><label for="meals" class="checkbox_label">Meals</label></td>
					<td width="40px" align="left" valign="top"><input type="checkbox" name="pickup_inc" id="pickup_inc" class="text_input" disabled <?= ($this->Get('pickup_inc') == "t") ? "checked" : "";  ?> /><label for="pickup_inc" class="checkbox_label">(Airport) Pickup</label></td>	
				</tr>
			</table>
			<? } ?>


			<? if (strlen($this->Get('experience')) > 1) { ?>
				<h3>Experience Required</h3>
				<p><?= $this->Get('experience') ?></p>
			<? } ?>
			
	<? } ?>

	</div>
</div>
</div>


<div class="row-fluid">
<div class="booking-enquiry">	
	<? if ($this->Get('profile_type') == PROFILE_JOB) { ?>
		<h3>Apply / More Info</h3>
	<? } else { ?>
		<h2>Booking / Enquiry</h2>
	<? } ?>

	<div class="booking-enquiry-buttons">	
	
	<?
	/* defaults for the profile type being viewed */
	if (in_array($this->Get('profile_type'), array(PROFILE_VOLUNTEER,PROFILE_TOUR))) {			
		/* is this enquiry type enabled / disabled on the company profile? */
		if ($this->Get('comp_profile')->HasEnquiryOption(ENQUIRY_BOOKING)) {
			
			/* finally if apply/booking url is specified, button should redirect to external site */
			if (strlen($this->Get('apply_url')) > 1) {
				/* button links to external apply/booking page */
				?>
				<a class="btn btn-primary" href="#" onclick="javascript: travel('<?= $this->Get('apply_url') ?>','/outgoing/<?= $this->Get('comp_url_name'); ?>/<?= $this->Get('url_name') ?>/www');" title="Apply Online" >Apply Online</a>
			
				<?					
			} else {
				/* use our apply/booking enquiry form */
				?>
				<a class="btn btn-primary" href="<?= $aEnquiryUrl['BOOKING']; ?>" title="Book this placement">Booking Enquiry</a>			
				<?
			}
		}
	}
	if (in_array($this->Get('profile_type'), array(PROFILE_VOLUNTEER,PROFILE_TOUR)) && ! $this->Get('comp_profile')->HasEnquiryOption(ENQUIRY_BOOKING)) {
		if ($this->Get('comp_profile')->HasEnquiryOption(ENQUIRY_GENERAL)) {
		?>
		<a class="btn btn-primary" href="<?= $aEnquiryUrl['GENERAL']; ?>" title="Make an enquiry">Enquiry</a>
		<?
		}
	}
	if (in_array($this->Get('profile_type'), array())) {
		?>
		<a class="btn btn-primary"  href="<?= $aEnquiryUrl['BROCHURE']; ?>" title="Request a brochure">Brouchure Request</a>
		<?
	}		

	if (in_array($this->Get('profile_type'), array(PROFILE_JOB,PROFILE_VOLUNTEER))) {
		if ($this->Get('comp_profile')->HasEnquiryOption(ENQUIRY_JOB_APP)) {
			if (strlen($this->Get('apply_url')) > 1) {
				/* button links to external apply page */
				?>
				<a class="btn btn-primary" target="_blank"  href="<?= $this->Get('apply_url') ?>" onclick="javascript: travel('<?= $this->Get('apply_url') ?>','/outgoing/<?= $this->Get('comp_url_name'); ?>/<?= $this->Get('url_name') ?>/www');" title="Apply Online">Apply Online</a>
				<?					
			} else {				
				?>
				<a class="btn btn-primary"  href="<?= $aEnquiryUrl['JOB_APP'] ?>" title="Apply Online" target="_blank">Apply Online</a>			
				<?
			}
		}
	}
	?>

	<?
	if (strlen($this->Get('url')) > 1 && $this->Get('url') != "http://") {
	?>
	<a class="btn btn-primary" href="#" onclick="javascript: travel('<?= $this->Get('url'); ?>','/outgoing/<?= $this->Get('comp_url_name'); ?>/<?= $this->Get('url_name') ?>/www');">Visit Website</a>
	<? } ?>
	
	</div>
</div>
</div>


<div class="row">
<div class="col12">
	<h2><?= $oProfile->GetCompanyName(); ?> Reviews</h2>
<?php 

$oReviewTemplate = $this->Get("oReviewTemplate");
print $oReviewTemplate->Render();

?>
</div>
</div>


<?php 
$aRelatedProfile = $this->Get("aRelatedProfile");

print_r($aRelatedProfile);
die();

if (count($aRelatedProfile) >= 1) { 
?>

<div class="row-fluid">
<div class="search-result span12 pull-left">
	<h3>Related Opportunities</h3><?php 
	foreach($aRelatedProfile as $oRelatedProfile) { 
	    $aImageDetails = $oRelatedProfile->GetImageUrlArray(); ?>


<div class="span4 featured-proj" style="height: 160px;">
        <div class="img-container" style="width: 40%;  float: left;">
                <div class="featured-proj-img span12">
                <? if (strlen($aImageDetails['MEDIUM']['URL']) > 1) { ?>

                <a title="<?= $oRelatedProfile->GetTitle() ?>" href="<?= "/company/".$oRelatedProfile->GetCompUrlName()."/".$oRelatedProfile->GetUrlName() ?>" class="">
                <img class="img-responsive img-rounded" src="<?= $aImageDetails['MEDIUM']['URL']  ?>" alt="<?= $oRelatedProfile->GetTitle() ?>" />
                </a>
                        <span class="frame-overlay"></span>
                <? } ?>
                </div>
                <div class="overlay-brand">
                        <a title="<?= $oRelatedProfile->GetCompanyName() ?>" href="<?= $oRelatedProfile->GetCompanyProfileUrl() ?>" target="_new" class="">
                        <?= $oRelatedProfile->GetCompanyLogoUrl() ?></div>
                        </a>
        </div>
        <div class="span6 details" style="float: right; width: 56%">
                <h3><a href="<?= "/company/".$oRelatedProfile->GetCompUrlName()."/".$oRelatedProfile->GetUrlName() ?>" title="" target="_new"><?= $oRelatedProfile->GetTitle(); ?></a></h3>


                <ul class="details span12" style="width: 100%; margin-bottom: 4px;">
                 <? if (strlen($oRelatedProfile->GetLocationLabel()) > 1) { ?>
                <?= $oRelatedProfile->GetLocationLabel(); ?><br/>
                <? } ?>
                <? if (is_numeric($oRelatedProfile->GetDurationFromId())) { ?>
                <?= $oRelatedProfile->GetDurationFromLabel(); ?> to <?= $oRelatedProfile->GetDurationToLabel(); ?><br />
                <? } ?>
                <?php if (is_numeric($oRelatedProfile->GetPriceFromId())) { ?>
                <?= $oRelatedProfile->GetPriceFromLabel(); ?> to <?= $oRelatedProfile->GetPriceToLabel(); ?>
                <?= $oRelatedProfile->GetCurrencyLabel(); ?><br />
                <?php } ?>
                </ul>
        </div>
</div>

<?php
    }
	?>
</div>
</div><?php 
}


if (!$oProfile->GetListingType() <= BASIC_LISTING) {

if (is_array($oRelatedArticle->GetArticleCollection()->Get())) {
?>

    <div class="row-fluid " style="margin-top: 10px;">
        <h3>Related Articles</h3>
        <div class="span12"><?

            $aArticle = $oRelatedArticle->GetArticleCollection()->Get();
            $limit = 4;

            for ($i=0;$i<$limit;$i++) {
                    if (is_object($aArticle[$i])) {
                            $aArticle[$i]->SetImgDisplay(FALSE);
                            $aArticle[$i]->LoadTemplate("related_list.php");
                            print $aArticle[$i]->Render();
                    }
            } ?>
    </div>
    </div><?php
}
}
?>


</div>
</div>