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

<div class='lead'>
<p class="lead"><strong><?= $oProfile->GetDescShortPlaintext(); ?></strong></p>

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
<? } ?> <? if ($oProfile->GetListingType() >= ENHANCED_LISTING) { ?>
<? if (strlen($oProfile->GetPlacementInfo()) > 1) { ?>
<h3>Placement Info</h3>
<p><?= nl2br(stripslashes($oProfile->GetPlacementInfo())); ?></p>
<? } ?>
<? } ?> 
<? if ($oProfile->GetListingType() >  BASIC_LISTING) { ?>
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