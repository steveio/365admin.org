<?php 

$oProfile = $this->Get("oProfile");
$oReviewTemplate = $this->Get("oReviewTemplate");


?>

<?
if($oProfile->GetListingType() < BASIC_LISTING) {
?>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
    (adsbygoogle = window.adsbygoogle || []).push({
        google_ad_client: "ca-pub-9874604497476880",
        enable_page_level_ads: true
    });
    </script>
        
<?php 
}
?>



<div class="container">
<div class="align-items-center justify-content-center">


<div class="row">
<div class="col-12 my-3">
<div class="col-8 sharethis-inline-share-buttons" style="display: block; float: right;"></div>
</div>
</div>


<div style="margin: 12px 0px 16px 0px;">
<? if ((strlen($this->Get('banner_img')) > 1) && ($oProfile->GetListingType() >= BASIC_LISTING)) { ?>
	<div style="div-center" class=""><?= $this->Get('banner_img'); ?></div>
<? } elseif ((strlen($this->Get('logo_img')) > 1) && ($oProfile->GetListingType() >= BASIC_LISTING)) { ?>
	<div class="div-center" style=""><?= $this->Get('logo_img'); ?></div>
<? }?>
</div>

<h1><?= $oProfile->GetTitle(); ?></h1>


<div class="row my-2">
    <div id="review-overallrating" class="col-3"></div>
    <div class="col-2"><?php if (is_object($oReviewTemplate) &  $oReviewTemplate->Get('HASREVIEWRATING') == true) {
        print "( ".$oReviewTemplate->Get('COUNT'). " Reviews ) ";
    } ?>
    </div>
</div>


<?php 

/*
print_r("<pre>");
print_r($oProfile->GetCamperAgeFromLabel());
print_r($oProfile);
print_r("</pre>");
die();
*/

?>

<div class="row my-2">
	<div class="col-12">
		<?php 
		if (count($oProfile->GetActivityArray()) > 1 && count($oProfile->GetActivityArray()) < 3) {
			$label = "Activities: "; ?>
			<b><?= $label; ?></b> <?= $oProfile->GetActivityTxt(); ?><br/><?
		}
		?>
		<?php 
		if (count($oProfile->GetCountryArray()) >= 1) {
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
		
		<? } // end summer camp ?>
	</div>
</div>



<div class='lead'>
<p class="lead"><strong><?= $oProfile->GetDescShortPlaintext(); ?></strong></p>
</div>

<?php
if (is_array($oProfile->GetAllImages()) && count($oProfile->GetAllImages()) >= 1) { ?>
<div class="profile-image span4 pull-right">
<ul class="unstyled"><?
        $i = 0;
        foreach($oProfile->GetAllImages() as $oImage) {

        // exclude profile body (desc_full) embedded images
        if (substr_count($oProfile->GetDescLong(),$oImage->GetId()) >= 1)
        {
            continue;
        }

        if ($i++ == 4) break;
            if (strlen($oImage->GetHtml("_lf","")) > 1) {
                print "<li style='margin-bottom: 10px;'>".$oImage->GetHtml("_lf","")."</li>";
            } else {
                print "<li style='margin-bottom: 10px;'>".$oImage->GetHtml("_mf","")."</li>";
            }
	} ?>
</ul>
</div><?
}
?>


<p><?= $oProfile->GetDescLongClean();?></p>


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

<? if ($oProfile->GetDuration() != "") { ?>
    <h3>Duration / Dates</h3>
    <p><?= nl2br($oProfile->GetDuration()) ?></p>
    <? } ?> <? if ($oProfile->GetCosts() != "") { ?>
    <h3>Costs / Pay</h3>
    <p><?= nl2br($oProfile->GetCosts()) ?></p>
<? } ?> 

<? if ($oProfile->GetListingType() >= ENHANCED_LISTING) { ?>
    <? if (strlen($oProfile->GetPlacementInfo()) > 1) { ?>
    <h3>Placement Info</h3>
    <p><?= nl2br(stripslashes($oProfile->GetPlacementInfo())); ?></p>
    <? } ?>
<? } ?> 

<? if ($oProfile->GetListingType() >=  BASIC_LISTING) { ?>
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
	<? } ?> 
<? } ?> 

</div>



<? if ($oProfile->GetProfileType() == PROFILE_SUMMERCAMP) { ?>
<div class="row">

	<h2>Summer Camp Info</h2>

	<div class="row my-3">
    	<div class="col">
    	<? if ($oProfile->GetStateLabel() != "") { ?>
    		<b>State :</b> <?= $oProfile->GetStateLabel() ?>
    	<? } ?>
    	</div>
    
    	<div class="col">
        <? if (strlen($oProfile->GetCamperAgeFromLabel()) >= 1) { ?>
        	<b>Camp Age Range: </b> <?= $oProfile->GetCamperAgeFromLabel() ?> - <?= $oProfile->GetCamperAgeToLabel() ?> 
        <? } ?>
        </div>
        
    	<div class="col">
        <? if (strlen($oProfile->GetCampGenderLabel()) >= 1) { ?>
        	<b>Camp Gender: </b> <?= $oProfile->GetCampGenderLabel(); ?> 
        <? } ?>
    	</div>
    
    	<div class="col">
        <? if (strlen($oProfile->GetCampReligionLabel()) >= 1) { ?>
        	<b>Camp Affiliation: </b> <?= $oProfile->GetCampReligionLabel(); ?> 
        <? } ?>
    	</div>
    
    	<div class="col">
    	<? if ($oProfile->GetDurationFromLabel() != "") { ?>
    		<b>Program Duration: </b> <?= $oProfile->GetDurationFromLabel() ?> - <?= $oProfile->GetDurationToLabel() ?> 
    	<? } ?>
    	</div>
    
    	<div class="col">
    	<? if ($oProfile->GetPriceFromLabel() != "") { ?>
    		<b>Program Costs: </b> <?= $oProfile->GetPriceFromLabel(); ?> - <?= $oProfile->GetPriceToLabel(); ?> <?= $oProfile->GetCurrencyLabel(); ?> 
    	<? } ?>
    	</div>
	</div>
	
    <div class="row my-3">
    	<b>Summer Camp Activities</b>
    	<div class="row">
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
    	<b>Summer Camp Job/Roles</b>
    	<div class="row">
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


<div class="row my-3">
	<h2><?= $oProfile->GetCompanyName(); ?> Reviews</h2>
<?php 
print $oReviewTemplate->Render();
?>
</div>



<? 

$aPlacement = $this->Get("aPlacement");

if ($oProfile->GetListingType() >= BASIC_LISTING)
{
    $strRelatedProfileTitle = $oProfile->GetCompanyName() ." Programs";
} else {
    $strRelatedProfileTitle = "Related Opportunities"; 
}

if (is_array($aPlacement) && count($aPlacement) >= 1)
{
?>
<div class="row">
<div class="featured-proj-list">
	<h2><?= $strRelatedProfileTitle ?></h2>
	
	<div class="row my-3">
	<?php 
	
	$strCompanyLogoHtml = '';
	if (is_object($oProfile->GetCompanyLogo()))
	{
	   $strCompanyLogoHtml = $oProfile->GetCompanyLogo()->GetHtml('_sm');
	}
	$iLimit = 6;
	for($i=0;$i<$iLimit;$i++) 
	{
	   $oPlacementProfile = array_shift($aPlacement);

	   $oTemplate = new Template();
       $oTemplate->Set("oProfile", $oPlacementProfile);
       $oTemplate->Set("strCompanyLogoHtml", $strCompanyLogoHtml);
       $oTemplate->LoadTemplate("profile_summary.php");
       print $oTemplate->Render();
    } ?>
	</div>

	<?php 
	if (count($aPlacement) > $iLimit) 
	{
	?>
	<div id="profile-list-btn" class="my-2">
		<a href="#" class="btn btn-primary rounded-pill px-3" id="profile-list-viewall">View All Programs</a>
	</div>

	<div id="profile-list-more" class="row my-3" style="display: none;">
	<?php 
	for($i=$iLimit;$i<count($aPlacement);$i++) 
	{
	   $oPlacementProfile = array_shift($aPlacement);

	   $oTemplate = new Template();
       $oTemplate->Set("oProfile", $oPlacementProfile);
       $oTemplate->Set("strCompanyLogoHtml", $strCompanyLogoHtml);
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
 



if (!$oProfile->GetListingType() <= BASIC_LISTING) {
    
$oRelatedArticle = $this->Get("oRelatedArticle");

    
if (is_array($oRelatedArticle->GetArticleCollection()->Get()) && count($oRelatedArticle->GetArticleCollection()->Get()) >= 1) {

$aArticle = $oRelatedArticle->GetArticleCollection()->Get();
$limit = 4;
    
?>

    <div class="row-fluid " style="my-3">
        <h3>Related Articles</h3>
        <div class="col-12"><?


            for ($i=0;$i<$limit;$i++) {
                    if (is_object($aArticle[$i])) {
                            $aArticle[$i]->SetImgDisplay(FALSE);
                            $aArticle[$i]->LoadTemplate("article_related.php");
                            print $aArticle[$i]->Render();
                    }
            } ?>
    </div>
    </div><?php
}
}
?>


</div>