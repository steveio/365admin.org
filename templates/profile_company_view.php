<?php 

$oProfile = $this->Get("oProfile");
$aPlacement = $this->Get("aPlacement");
$oReviewTemplate = $this->Get("oReviewTemplate");

?>


<div class="container">
<div class="align-items-center justify-content-center">


<div class="row">
<div class="col-12 my-3">
<div class="col-8 sharethis-inline-share-buttons" style="display: block; float: right;"></div>
</div>
</div>


<div>
<? if ((strlen($this->Get('banner_img')) > 1) && ($oProfile->GetListingType() >= BASIC_LISTING)) { ?>
	<div style="div-center" class=""><?= $this->Get('banner_img'); ?></div>
<? } elseif ((strlen($this->Get('logo_img')) > 1) && ($oProfile->GetListingType() >= BASIC_LISTING)) { ?>
	<div class="div-center" style=""><?= $this->Get('logo_img'); ?></div>
<? }?>
</div>

<h1><?= $oProfile->GetTitle(); ?></h1>


<div class="row my-2">
    <div id="review-overallrating" class="col-3"></div>
    <div class="col-lg-6 col-sm-12"><?php if (is_object($oReviewTemplate) &  $oReviewTemplate->Get('HASREVIEWRATING') == true) {
        print "( ".$oReviewTemplate->Get('COUNT'). " Reviews ) ";
    } ?>
    </div>
</div>


<div class="row my-2">
	<div class="col-sm-12 col-lg-6">
		<?php
		
		if (count($oProfile->GetActivityArray()) >= 1) {
			$label = "Activities: ";
			$value = (count($oProfile->GetActivityArray()) > 6) ? "Multiple Program Types" : $oProfile->GetActivityTxt(); ?>
			<b><?= $label; ?></b> <?= $value; ?><br/><?
		}
		?>
		<?php 
		if (is_array($oProfile->GetCountryArray()) && count($oProfile->GetCountryArray()) >= 1) {
		    $strCountryLabel = (count($oProfile->GetCountryArray()) == 1) ? "Country: " : "Countries: ";
		    if (count($oProfile->GetCountryArray()) > 3) { ?>
		    <b>Location: </b> Multiple Destinations<br/><?
		    } else { ?>
			<b><?= $strCountryLabel; ?></b> <?= $oProfile->GetCountryTxt(); ?><br/><?
		    }
		}  
		?>
		<? if (strlen($oProfile->GetLocation()) > 1) { ?>
			<b>Location: </b> <?= $oProfile->GetLocation(); ?><br/>
		<? } ?>

		<? if ($oProfile->GetProfileType() == PROFILE_SUMMERCAMP) { ?>

    		<? if (count($oProfile->GetCampTypeLabels()) > 1) { ?>
    			<b>Camp Type: </b> <?= implode(" / ", $oProfile->GetCampTypeLabels()); ?><br/>
    		<? } ?>
		
		<? } ?>

		<? if ($oProfile->GetProfileType() == PROFILE_TEACHING) { ?>

    		<? if (strlen($oProfile->GetNoTeachersLabel()) > 1) { ?>
    			<b>Number of Teachers: </b> <?= $oProfile->GetNoTeachersLabel(); ?><br/>
    		<? } ?>

    		<? if (strlen($oProfile->GetClassSizeLabel()) > 1) { ?>
    			<b>Class Size: </b> <?= $oProfile->GetClassSizeLabel(); ?><br/>
    		<? } ?>

		<? } ?>

	</div>
</div>



<div class='lead'>
<p class="lead"><strong><?= $oProfile->GetDescShortPlaintext(); ?></strong></p>
</div>


<? include("./templates/profile_images_view.php"); ?>	

<div class="article-body">
<p><?= $oProfile->GetDescLongClean();?></p>
</div>


<div class="row">

<? if ($oProfile->GetListingType() >= BASIC_LISTING) { ?>
	<? if (strlen(trim($oProfile->GetVideo())) > 1) { ?>
		<div class="span12">
		<h3>Video</h3>
		<hr />
			<?= $oProfile->GetVideo() ?>	
		</div>
	<? } ?>
<? } ?>

<? if ($oProfile->GetListingType() >= ENHANCED_LISTING) { ?>
    <? if (strlen($oProfile->GetPlacementInfo()) > 1) { ?>
    <h3>Placement Info</h3>
    <p><?= nl2br(stripslashes($oProfile->GetPlacementInfo())); ?></p>
    <? } ?>
<? } ?> 

<? if ($oProfile->GetListingType() >=  BASIC_LISTING) { ?>
	<? if ($oProfile->GetAddress() != "" || $oProfile->GetTel() != "") { ?>
	<h3>Contact Info</h3>
	<? if ($oProfile->GetListingType() >= ENHANCED_LISTING) { ?>
		<? if ($oProfile->GetTel() != "") { ?> 
			<p>Tel: <?= $oProfile->GetTel() ?></p>
		<? } ?>
		<? if ($oProfile->GetFax() != "") { ?> <br />
			Fax: <?= $oProfile->GetFax() ?> 
		<? } ?> 
	<? } ?> 
	<? if ($oProfile->GetAddress() != "") { ?>
		<p>Address : <?= $oProfile->GetAddress() ?></p>
	<? }
	}  ?> 
<? } ?> 

</div>



<? if ($oProfile->GetProfileType() == PROFILE_VOLUNTEER_PROJECT) { ?>
<div class="row">
	<h2>Project Info</h2>

	<div class="row my-3">
    	<? if (method_exists($oProfile, 'GetDurationFromLabel') && $oProfile->GetDurationFromLabel() != "") { ?>
    	<div class="col-6">
    		<h3>Program Duration: </h3>
    		<?= $oProfile->GetDurationFromLabel() ?> - <?= $oProfile->GetDurationToLabel() ?>
		</div>
    	<? } ?>

    	<? if (method_exists($oProfile, 'GetPriceFromLabel') && $oProfile->GetPriceFromLabel() != "") { ?>
    	<div class="col-6">
    		<h3>Program Costs: </h3>
    		<?= $oProfile->GetPriceFromLabel(); ?> - <?= $oProfile->GetPriceToLabel(); ?> <?= $oProfile->GetCurrencyLabel(); ?>
    	</div>
    	<? } ?>
    </div>

	<div class="row my-3">
    	<div class="col-6">
    	<? if ($oProfile->GetFounded() != "") { ?>
    		<h3>Founded / Established: </h3>
    		<?= $oProfile->GetFounded(); ?>
    	<? } ?>
    	</div>

    	<? if ($oProfile->GetOrgTypeLabel() != "") { ?>
    	<div class="col-6">
    		<h3>Org Type: </h3>
    		<?= $oProfile->GetOrgTypeLabel() ?>
    	</div>
    	<? } ?>
    </div>
    <div class="row my-3">
    	<? if ($oProfile->GetNoPlacementsLabel() != "") { ?>
    	<div class="col-6">
    		<h3>Team Size / Num Volunteers: </h3>
    		<?= $oProfile->GetNoPlacementsLabel() ?>
    	</div>
    	<? } ?>

    	<? if ($oProfile->GetAwards() != "") { ?>
    	<div class="col-6">
    		<h3>Awards / Certification: </h3>
    		<?= $oProfile->GetAwards() ?>
    	</div>
    	<? } ?>
    </div>

	<div class="row my-3">

    	<? if ($oProfile->GetSpeciesLabelsTxt() != "") { ?>
    	<div class="col-6">
        	<h3>Species: </h3>
    		<?= $oProfile->GetSpeciesLabelsTxt(); ?>    	
		</div>
		<? } ?>

    	<? if ($oProfile->GetHabitatsLabelsTxt() != "") { ?>
    	<div class="col-6">
        	<h3>Habitats: </h3>
    		<?= $oProfile->GetHabitatsLabelsTxt(); ?>    	
		</div>
		<? } ?>

	</div>

	<div class="row my-3">

		<? if ($oProfile->GetSupport() != "") { ?>
    	<div class="col-6">
    		<h3>Volunteer Support: </h3>
    		<?= $oProfile->GetSupport() ?>
    	</div>
    	<? } ?>

    	<? if ($oProfile->GetSafety() != "") { ?>
    	<div class="col-6">
    		<h3>Volunteer Safety: </h3>
    		<?= $oProfile->GetSafety() ?>
    	</div>
    	<? } ?>

	</div>

</div>
<? } ?>



<? if ($oProfile->GetProfileType() == PROFILE_SUMMERCAMP) { ?>
<div class="row">

	<h2>Summer Camp Info</h2>

	<div class="row my-3">
    	<? if (method_exists($oProfile, 'GetDurationFromLabel') && $oProfile->GetDurationFromLabel() != "") { ?>
    	<div class="col-6">
    		<h3>Program Duration: </h3> 
    		<?= $oProfile->GetDurationFromLabel() ?> - <?= $oProfile->GetDurationToLabel() ?>
		</div>
    	<? } ?>

    	<? if (method_exists($oProfile, 'GetPriceFromLabel') && $oProfile->GetPriceFromLabel() != "") { ?>
    	<div class="col-6">
    		<h3>Program Costs: </h3> 
    		<?= $oProfile->GetPriceFromLabel(); ?> - <?= $oProfile->GetPriceToLabel(); ?> <?= $oProfile->GetCurrencyLabel(); ?>
    	</div>
    	<? } ?>
    </div>

	<div class="row my-3">
    	<? if ($oProfile->GetStateLabel() != "") { ?>
    	<div class="col-6">
    		<h3>State :</h3> 
    		<?= $oProfile->GetStateLabel() ?>
    	</div>
    	<? } ?>

        <? if (strlen($oProfile->GetCamperAgeFromLabel()) >= 1) { ?>    
    	<div class="col-6">
        	<h3>Camp Age Range: </h3> 
        	<?= $oProfile->GetCamperAgeFromLabel() ?> - <?= $oProfile->GetCamperAgeToLabel() ?> 
        </div>
        <? } ?>
	</div>

	<div class="row my-3">
        <? if (strlen($oProfile->GetCampGenderLabel()) >= 1) { ?>        
    	<div class="col-6">
        	<h3>Camp Gender: </h3> 
        	<?= $oProfile->GetCampGenderLabel(); ?> 
    	</div>
        <? } ?>

        <? if (strlen($oProfile->GetCampReligionLabel()) >= 1) { ?>
    	<div class="col-6">
        	<h3>Camp Affiliation: </h3> 
        	<?= $oProfile->GetCampReligionLabel(); ?> 
    	</div>
		<? } ?>
	</div>
	
    <div class="row my-3">
    	<b>Summer Camp Activities</b>
    	<div class="row my-3">
    		<?
    		$oCampActivity = new Refdata(REFDATA_ACTIVITY);
    		//$oCampActivity->SetOption(REFDATA_OPTION_CHECKBOXES_DISABLED, TRUE);
			$oColumnSort = new ColumnSort;
			$oColumnSort->SetElements($oCampActivity->GetCheckboxList(REFDATA_ACTIVITY_PREFIX,$oProfile->GetCampActivityList(),''));
			$oColumnSort->SetCols(3);
			$aElements = $oColumnSort->Sort();
    		?>
    
    		<div class="row">
    			<div class="col-3">
    				<ul class='select_list'>
    				<?php
    				foreach($aElements[1] as $idx => $val) {
    					print $val;
    				}
    				?>
    				</ul>
    			</div>
    			<div class="col-3">
    				<ul class='select_list'>
    				<?php
    				foreach($aElements[2] as $idx => $val) {
    					print $val;
    				}
    				?>
    				</ul>
    			</div>
    			<div class="col-3">
    				<ul class='select_list'>
    				<?php
    				foreach($aElements[3] as $idx => $val) {
    					print $val;
    				}
    				?>
    				</ul>
    			</div>
    		</div>
    	</div>
    </div>

    <div class="row my-3">
    	<b>Summer Camp Roles</b>
    	<div class="row my-3">
    		<?
    		    $oCampJobType = new Refdata(REFDATA_CAMP_JOB_TYPE);
        		//$oCampActivity->SetOption(REFDATA_OPTION_CHECKBOXES_DISABLED, TRUE);
    			$aRoles = $oCampJobType->GetCheckboxList(REFDATA_ACTIVITY_PREFIX,$oProfile->GetCampJobTypeList(),'');
    		?>
    
    		<div class="row">
    			<div class="col-3">
    				<ul class='select_list'>
    				<?php
    				foreach($aRoles as $idx => $val) {
    					print $val;
    				}
    				?>
    				</ul>
    			</div>
    		</div>
    	</div>
    </div>
    
    
   	<div class="row my-3">
   	
    	<div class="col">
    	<? if ($oProfile->GetSeasonDates() != "") { ?>
    		<h3>Season Dates: </h3>
    		<?= $oProfile->GetSeasonDates() ?>
    	<? } ?>
    	</div>

    	<div class="col">
    	<? if ($oProfile->GetRequirements() != "") { ?>
    		<h3>Requirements: </h3>
    		<?= $oProfile->GetRequirements() ?>
    	<? } ?>
    	</div>

    	<div class="col">
    	<? if ($oProfile->GetHowToApply() != "") { ?>
    		<h3>How to Apply: </h3>
    		<?= $oProfile->GetHowToApply() ?>
    	<? } ?>
    	</div>
   	
   	</div>

</div>
<? } ?>

<? if ($oProfile->GetProfileType() == PROFILE_TEACHING) { ?>
<div class="row">
	<h2>Teaching Info</h2>
	<div class="row my-3">

    	<div class="col">
    	<? if ($oProfile->GetSalary() != "") { ?>
    		<h3>Salary / Costs: </h3>
    		<?= $oProfile->GetSalary(); ?>
    	<? } ?>
    	</div>

    	<div class="col">
    	<? if ($oProfile->GetBenefits() != "") { ?>
    		<h3>Benefits: </h3>
    		<?= $oProfile->GetBenefits(); ?>
    	<? } ?>
    	</div>

    	<div class="col">
    	<? if ($oProfile->GetQualifications() != "") { ?>
    		<h3>Qualifications: </h3>
    		<?= $oProfile->GetQualifications() ?>
    	<? } ?>
    	</div>

    	<div class="col">
    	<? if ($oProfile->GetRequirements() != "") { ?>
    		<h3>Requirements: </h3>
    		<?= $oProfile->GetRequirements() ?>
    	<? } ?>
    	</div>

    	<div class="col">
    	<? if ($oProfile->GetHowToApply() != "") { ?>
    		<h3>How to Apply: </h3>
    		<?= $oProfile->GetHowToApply() ?>
    	<? } ?>
    	</div>

	</div>

</div>
<? } ?>


<? if ($oProfile->GetProfileType() == PROFILE_SEASONALJOBS) { ?>
<div class="row">
	<h2>Job Info</h2>
	<div class="row my-3">

    	<? if ($oProfile->GetJobTypes() != "") { ?>
    	<div class="col">
    		<h3>Job Types: </h3>
    		<?= $oProfile->GetJobTypes(); ?>
    	</div>
    	<? } ?>

    	<? if ($oProfile->GetDurationFromLabel() != "") { ?>
    	<div class="col">
    		<h3>Job Duration(s): </h3> 
    		<?= $oProfile->GetDurationFromLabel() ?> - <?= $oProfile->GetDurationToLabel() ?> 
    	</div>
    	<? } ?>

    	<? if ($oProfile->GetPay() != "") { ?>
    	<div class="col">
    		<h3>Salary / Pay: </h3>
    		<?= $oProfile->GetPay(); ?>
    	</div>
    	<? } ?>

    	<? if ($oProfile->GetBenefits() != "") { ?>
    	<div class="col">
    		<h3>Benefits: </h3>
    		<?= $oProfile->GetBenefits(); ?>
    	</div>
    	<? } ?>

    	<? if ($oProfile->GetRequirements() != "") { ?>
    	<div class="col">
    		<h3>Requirements: </h3>
    		<?= $oProfile->GetRequirements() ?>
    	</div>
		<? } ?>

    	<? if ($oProfile->GetHowToApply() != "") { ?>
    	<div class="col">
    		<h3>How to Apply: </h3>
    		<?= $oProfile->GetHowToApply() ?>
    	</div>
    	<? } ?>

	</div>

</div>
<? } ?>


<? if ($oProfile->GetProfileType() == PROFILE_COURSES) { ?>
<div class="row">

	<h2>Language School / Courses Info</h2>

	<div class="row my-3">
    	<? if (method_exists($oProfile, 'GetDurationFromLabel') && $oProfile->GetDurationFromLabel() != "") { ?>
    	<div class="col-6">
    		<h3>Program Duration: </h3> 
    		<?= $oProfile->GetDurationFromLabel() ?> - <?= $oProfile->GetDurationToLabel() ?>
		</div>
    	<? } ?>

    	<? if (method_exists($oProfile, 'GetPriceFromLabel') && $oProfile->GetPriceFromLabel() != "") { ?>
    	<div class="col-6">
    		<h3>Program Approx Costs / Fees: </h3> 
    		<?= $oProfile->GetPriceFromLabel(); ?> - <?= $oProfile->GetPriceToLabel(); ?> <?= $oProfile->GetCurrencyLabel(); ?>
    	</div>
    	<? } ?>
    </div>
	
    <div class="row my-3">
    	<b>Langages</b>
    	<div class="row my-3">
    		<?
    		$oLanguages = new Refdata(REFDATA_LANGUAGES);
    		$oLanguages->SetDisplaySelectedOnly(true);
			$oColumnSort = new ColumnSort;
			$oColumnSort->SetElements($oLanguages->GetCheckboxList(REFDATA_LANGUAGES_PREFIX,$oProfile->GetLanguages(),''));
			$oColumnSort->SetCols(3);
			$aElements = $oColumnSort->Sort();
    		?>
    
    		<div class="row">
    			<div class="col-3">
    				<ul class='select_list'>
    				<?php
    				foreach($aElements[1] as $idx => $val) {
    					print $val;
    				}
    				?>
    				</ul>
    			</div>
    			<?
    			if (is_array($aElements[2]) && count($aElements[2]) >= 1)
    			{
    			?>
    			<div class="col-3">
    				<ul class='select_list'>
    				<?php
    				foreach($aElements[2] as $idx => $val) {
    					print $val;
    				}
    				?>
    				</ul>
    			</div>
    			<?
    			}
    			if (is_array($aElements[3]) && count($aElements[3]) >= 1)
    			{
    			?>
    			<div class="col-3">
    				<ul class='select_list'>
    				<?php
    				foreach($aElements[3] as $idx => $val) {
    					print $val;
    				}
    				?>
    				</ul>
    			</div>
    			<?
    			}
    			?>
    		</div>
    	</div>
    </div>


	<div class="row my-3">
	
		<?
        $oCourseType = new Refdata(REFDATA_COURSE_TYPE);
        $oCourseType->SetDisplaySelectedOnly(true);
        $aRoles = $oCourseType->GetCheckboxList(REFDATA_COURSE_TYPE_PREFIX,$oProfile->GetCourseType(),'');
        if (is_array($aRoles) && count($aRoles) >= 1)
        {
		?>
    	<div class="col-sm-12 col-md-3 col-lg-3 my-3">
        	<b>Course Types</b>
        	<div class="col-12 my-3">        
        		<div class="row">
        			<div class="">
        				<ul class='select_list'>
        				<?php
        				foreach($aRoles as $idx => $val) {
        					print $val;
        				}
        				?>
        				</ul>
        			</div>
        		</div>
        	</div>
        </div>
        <?
        }
        ?>

		<?
	    $oCourses = new Refdata(REFDATA_COURSES);
	    $oCourses->SetDisplaySelectedOnly(true);
	    $aCourses = $oCourses->GetCheckboxList(REFDATA_COURSES_PREFIX,$oProfile->GetCourses(),'');
	    
	    if (is_array($aCourses) && count($aCourses) >= 1)
	    {
		?>

        <div class="col-sm-12 col-md-3 col-lg-3 my-3">
        	<b>Other Study / Vocational Courses</b>
        	<div class="col-12 my-3">        
        		<div class="row">
        			<div class="">
        				<ul class='select_list'>
        				<?php
        				foreach($aCourses as $idx => $val) {
        					print $val;
        				}
        				?>
        				</ul>
        			</div>
        		</div>
        	</div>
        </div>
        <?
	    }
        ?>

		<?
	    $oAccomodation = new Refdata(REFDATA_ACCOMODATION);
	    $oAccomodation->SetDisplaySelectedOnly(true);
	    $aAccomodation = $oAccomodation->GetCheckboxList(REFDATA_ACCOMODATION_PREFIX,$oProfile->GetAccomodation(),'');
	    if (is_array($aAccomodation) && count($aAccomodation) >= 1)
	    {
        ?>
    	<div class="col-sm-12 col-md-3 col-lg-3 my-3">
        	<b>Accomodation Options</b>
        	<div class="row my-3">        
        		<div class="row">
        			<div class="">
        				<ul class='select_list'>
        				<?php
        				foreach($aAccomodation as $idx => $val) {
        					print $val;
        				}
        				?>
        				</ul>
        			</div>
        		</div>
        	</div>
        </div>
        <?
	    }
        ?>
	
	</div>


   	<div class="row my-3">

    	<div class="col">
    	<? if ($oProfile->GetStartDates() != "") { ?>
    		<h3>Start Dates / Term Times: </h3>
    		<?= $oProfile->GetStartDates() ?>
    	<? } ?>
    	</div>

    	<div class="col">
    	<? if ($oProfile->GetQualifications() != "") { ?>
    		<h3>Qualification / Certification / Awards: </h3>
    		<?= $oProfile->GetQualifications() ?>
    	<? } ?>
    	</div>
   	
   	</div>

   	<div class="row my-3">

    	<div class="col">
    	<? if ($oProfile->GetRequirements() != "") { ?>
    		<h3>Requirements: </h3>
    		<?= $oProfile->GetRequirements() ?>
    	<? } ?>
    	</div>

    	<div class="col">
    	<? if ($oProfile->GetPreparation() != "") { ?>
    		<h3>Preparation / Course Guidelines: </h3>
    		<?= $oProfile->GetPreparation() ?>
    	<? } ?>
    	</div>
   	
   	</div>

   	<div class="row my-3">

    	<div class="col">
    	<? if ($oProfile->GetHowToApply() != "") { ?>
    		<h3>How to Apply: </h3>
    		<?= $oProfile->GetHowToApply() ?>
    	<? } ?>
    	</div>

   	</div>


</div>
<? } ?>


<div id="buttons" class="buttons row my-5">
<div class="booking-enquiry my-3">
<h2>Contact / Enquiry</h2>
<? 
$aEnquiryButtonHtml = $this->Get('aEnquiryButtonHtml');
if (is_array($aEnquiryButtonHtml))
{
    foreach($aEnquiryButtonHtml as $k => $v) {
        print $v;
    } 
} ?>
</div>
</div>

<? if ($oProfile->GetListingType() == FREE_LISTING) { ?>
<div class="row my-3 alert alert-success" role="alert">
  <div class="row">
  <div class="col-1">
    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-bag-check" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
      <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
    </svg>  
  </div>
  <div class="col-6">
  	<div class="row">
      <h3>Update Listing</h3>
      <p>Apply to update this profile.  <br />Learn more about <a href="/advertising" title="One World 365 Advertising Opportunities" target="_new">advertising opportunities</a> or <a href="/contact" title="One World 365 - Contact Us" target="_new">contact us</a> for details.
    </div>
    <div class="row">
    <div class="col-4">
 	  <button class="btn btn-success rounded-pill px-3" type="button" onclick="go('<?= ADMIN_SYSTEM."/update/".$oProfile->GetUrlName() ?>'); return false;">Update Listing</button>
 	</div>
 	</div>     
  </div>
 
  </div>  
</div>
<?php } ?>


<? if ($oAuth->oUser->isAdmin) { ?>
<div class="row my-3 alert alert-primary d-flex align-items-center" role="alert">
  <div class="row">
  <h3>Admin</h3>
	<div class="col-3">Added: <?= $oProfile->GetAddedDate(); ?></div>
   	<div class="col-3">Last Updated: <?= $oProfile->GetLastUpdated(); ?></div>
   	<div class="col-3">Last Indexed: <?= $oProfile->GetLastIndexedSolr(); ?></div> 
	<div class="col-3 float-end alert alert-danger" role="alert">
    <button class="btn btn-danger rounded-pill px-3" type="button" onclick="deleteProfile('<?= ADMIN_SYSTEM."/company/".$oProfile->GetUrlName() ?>/delete'); return false;">Delete Profile</button>
	</div>

  </div>
</div>
<? } ?>


<div class="row my-3">
	<h2><?= $oProfile->GetCompanyName(); ?> Reviews</h2>
<?php 
print $oReviewTemplate->Render();
?>
</div>



<?

if ($oProfile->GetListingType() >= BASIC_LISTING)
{
    $strTemplate = "profile_summary.php";
    $strRelatedProfileTitle = $oProfile->GetCompanyName() ." Programs";
} else {
    $strTemplate = "profile_summary_small.php";
    $strRelatedProfileTitle = "Related Opportunities"; 
}

if (is_array($aPlacement) && count($aPlacement) >= 1)
{
?>
<div class="row">
<div class="col-12">
	<h2><?= $strRelatedProfileTitle ?></h2>
	
	<div class="row my-3">
	<? 

	$iLimit = 8;
	$displayMore = (count($aPlacement) > $iLimit) ? true : false;

	for($i=0;$i<$iLimit;$i++) 
	{
	   $oPlacementProfile = array_shift($aPlacement);

	   $oTemplate = new Template();
           $oTemplate->Set("oProfile", $oPlacementProfile);
           $oTemplate->Set("displayRelatedProfile","COMPANY");
           $oTemplate->Set("strCompanyLogoHtml", $strCompanyLogoHtml);
           $oTemplate->Set("sImgSize", "_mf");
           $oTemplate->LoadTemplate("profile_summary.php");
           print $oTemplate->Render();
    } ?>
	</div>

	<?
	if ($displayMore)
	{
	?>
	<div id="profile-list-btn" class="my-2">
		<a href="#" class="btn btn-primary rounded-pill px-3" id="profile-list-viewall">View All Programs</a>
	</div>
	<div id="profile-list-more" class="row my-3" style="display: none;">
	<?php 
	$count = count($aPlacement);
	for($i=0;$i<$count;$i++) 
	{
	   $oPlacementProfile = array_shift($aPlacement);

	   $oTemplate = new Template();
       $oTemplate->Set("oProfile", $oPlacementProfile);
       $oTemplate->Set("strCompanyLogoHtml", $strCompanyLogoHtml);
       $oTemplate->Set("sImgSize", "_mf");
       $oTemplate->LoadTemplate("profile_summary.php");
       print $oTemplate->Render();
    } ?>
	</div><?php 
	}
	?>

</div>
</div>

<script>
$(document).ready(function(){
    $('#profile-list-viewall').click(function(e) {
        e.preventDefault();
        $('#profile-list-more').show();
        $('#profile-list-btn').hide();
        return false;
    });
});
</script>

<? 
} // end programs
 



if (!$oProfile->GetListingType() <= BASIC_LISTING) 
{
    
    $aRelatedArticle = $this->Get("aRelatedArticle");
        
    if (is_array($aRelatedArticle) && count($aRelatedArticle) >= 1) 
    { ?>
    <div class="row" style="my-3">
        <h3>Related Articles</h3>
        <div class="row"><?
        foreach($aRelatedArticle as $oArticle)
        {
                if (is_object($oArticle)) 
                {
                    $oArticle->initTemplate();
                    $oArticle->oTemplate->Set('CSS_CLASS_COL','col-sm-12 col-lg-4 col-md-4');
                    $oArticle->oTemplate->Set('IMG_FORMAT', '_lf');
                    $oArticle->LoadTemplate("article_summary.php");
                    print $oArticle->Render();
                }
        } ?>
        </div>
    </div><?php
    }
}
?>


</div>
