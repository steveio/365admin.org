<?php 

$oProfile = $this->Get("oProfile");

?>


<div class="container">
<div class="align-items-center justify-content-center">


<div class="pull-right" style="margin: 20px;">
<div class="sharethis-inline-share-buttons"></div>
</div>



<div style="margin: 12px 0px 16px 0px;">
<? if ((strlen($this->Get('banner_img')) > 1) && ($oProfile->GetListingType() >= BASIC_LISTING)) { ?>
	<div style="div-center" class=""><?= $this->Get('banner_img'); ?></div>
<? } elseif ((strlen($this->Get('logo_img')) > 1) && ($oProfile->GetListingType() >= BASIC_LISTING)) { ?>
	<div class="div-center" style=""><?= $this->Get('logo_img'); ?></div>
<? }?>
</div>

<h1><?= $oProfile->GetTitle(); ?></h1>

<div class='lead'>

<div id="review-overallrating" style="margin-bottom: 10px;"></div>

<p class="lead"><strong><?= strip_tags(htmlUtils::stripLinks($oProfile->GetDescShort())); ?></strong></p>

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


<? if ($oProfile->GetListingType() <=  BASIC_LISTING) { ?>
	<p><?= htmlUtils::stripLinks(html_entity_decode( Article::convertCkEditorFont2Html($oProfile->GetDescLong(),"h3") )); ?></p>
<?php } else { ?>
	<p><?= html_entity_decode( Article::convertCkEditorFont2Html($oProfile->GetDescLong(),"h3")); ?></p>
<?php } ?>


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




<div id="buttons" class="buttons row-fluid">
<div class="booking-enquiry">
<h2>Contact / Enquiry</h2>
<? for($i=0;$i<$iRows;$i++) { ?>

	<? for($j=0;$j<2;$j++) {
		print "<span style='padding-right: 7px;'>".array_shift($aButtonHtml)."</span>";
	} ?>	 		 
<? } ?>
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



<? 

$aPlacement = $this->Get("aPlacement");

if ($oProfile->GetListingType() >= BASIC_LISTING)
{
    $strRelatedProfileTitle = $oProfile->GetCompanyName() ." Programs <a href=\"#\" id=\"related-viewall\">( View All )</a>";

} else {
    $strRelatedProfileTitle = "Related Opportunities"; 
    if (is_array($aPlacement) && count($aPlacement) > 6) $aPlacement = array_slice($aPlacement, 0, 6);
}

if (is_array($aPlacement) && count($aPlacement) >= 1)
{
?>
<div class="row-fluid">
<div class="search-result span12 pull-left">
	<h3><?= $strRelatedProfileTitle ?></h3>
	<div id="related-visible"><?
	
	if ((is_array($aPlacement)) && (count($aPlacement) >= 1)) {
		$i = 0;
		foreach ($aPlacement as $p) {
	
				if ($i==6) { ?>
				</div>
				<div id="related-more" style="display: none;"><?
				}
				$i++;
				$oRelatedProfile = new PlacementProfile();
				$oRelatedProfile->SetFromArray($p);
				$oRelatedProfile->GetCountryInfo();
				$oRelatedProfile->GetImages();
				$aImageDetails = $oRelatedProfile->GetImageUrlArray();	

				?>
        <div class="span4 featured-proj" style="height: 160px;">


		<div class="img-container" style="width: 40%;  float: left;">
			<div class="featured-proj-img span12">
			<? if (strlen($aImageDetails['MEDIUM']['URL']) > 1) { ?>

      			<a title="<?= $oProfile->GetTitle() ?>" href="<?= "/company/".$oRelatedProfile->GetCompUrlName()."/".$oRelatedProfile->GetUrlName() ?>" class=""> 
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
		<div class="span6 details" style="float: right; width: 56%;">
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


    	</div><?
	}
	?>
	</div>
</div>
</div>
<script>

$(document).ready(function(){
	$('#related-viewall').click(function(e) {
	   e.preventDefault();
           $('#related-more').show();
           return false;
       });       	
});
</script>
<? 
}
} 

if (!$oProfile->GetListingType() <= BASIC_LISTING) {

    
$oRelatedArticle = $this->Get("oRelatedArticle");

    
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


<? if(($oAuth->oUser->isAdmin) || ($oAuth->oUser->company_id == $oProfile->GetId())) { ?>
<div class="pull-left span12">
<h2>Admin</h2>
<p><a href="<?= $_CONFIG['url'] ?>/company/<?= $oProfile->GetUrlName() ?>/edit">Edit Company</a></p>
</div>
<? } ?>

</div>
