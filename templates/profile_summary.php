<?php 

$oProfile = $this->Get('oProfile');
if (!is_object($oProfile)) return;

if ($oProfile->GetGeneralType() == PROFILE_PLACEMENT) 
{
    $sProfileUrl = "/company/".$oProfile->GetCompUrlName()."/".$oProfile->GetUrlName();
} else {
    $sProfileUrl = "/company/".$oProfile->GetCompUrlName();
}

$strCompanyLogoHtml = $this->Get('strCompanyLogoHtml');


?>

<div class="col-sm-12 col-md-3 col-lg-3 my-2">

    	<div class="col-sm-12 img-container"><?
    	if (strlen($strCompanyLogoHtml) < 1 && is_object($oProfile->GetCompanyLogo()))
    	{
    	    $strCompanyLogoHtml = $oProfile->GetCompanyLogo()->GetHtml('',$oProfile->GetCompUrlName(),'','width: 240px; height: 180px;');
    	    $strCompanyLogoHtmlSmall = $oProfile->GetCompanyLogo()->GetHtml('_sm', $oProfile->GetCompUrlName());
    	}

    	if (is_object($oProfile->GetImage(0))) // profile has 1 or more image
    	{ 
    	    if ($oProfile->GetImage(0)->GetHtml("_lf",'')) // try fetch large fixed-aspect image  
    	    {
    	        $img_url = $oProfile->GetImage(0)->GetUrl("_lf");
    	    } elseif ($oProfile->GetImage(0)->GetHtml("_mf",'')) // older profiles may only have medium size
    	    {
    	        $img_url = $oProfile->GetImage(0)->GetUrl("_mf");
    	    } ?>
    		<div class="col-12 profile-image">
        		<a title="<?= $oProfile->GetTitle() ?>" href="<?= $sProfileUrl;  ?>" class="">
				<img class="img-fluid rounded mb-3" src="<?= $img_url;  ?>" alt="<?= $oProfile->GetTitle() ?>" />
        		</a>
        	</div>
        	<div class="brand-overlay">
        		<?= $strCompanyLogoHtmlSmall; ?>
        	</div><?

        } else { // no images, display company profile logo
        	if (strlen($strCompanyLogoHtml) > 1) {
        	?>
        	<div style="min-height: 190px;">
        		<a title="<?= $oProfile->GetCompanyName() ?>" href="<?= $oProfile->GetCompanyProfileUrl() ?>" target="_new" class="">
        		<?= $strCompanyLogoHtml; ?>
        		</a>
        	</div><?php 
        	} 
    	} ?>
    	</div>

        <div class="col-lg-10 col-sm-12">
       	    <h4><a href="<?= $sProfileUrl;  ?>" title="<?= $oProfile->GetTitle(); ?>" target="_new"><?= $oProfile->GetTitle(); ?></a></h4>    

            <?php if ($oProfile->GetReviewCount() >= 1) { ?>
            <input type="hidden" id="rateYo-<?= $oProfile->GetId() ?>-rating" value="<?= $oProfile->GetRating(); ?>" />
            <div class="row m-2">
                <div id="rateYo-<?= $oProfile->GetId() ?>" class="rating col-4"></div>
                <?php  $reviewLabel = ($oProfile->GetReviewCount() == 1) ? "Review" : "Review"; ?>
                <div class="col-6 small">( <?= $oProfile->GetReviewCount(). " ".$reviewLabel." ) "; ?></div>
            </div><?
            } ?>

        	<ul class="profile-details small">
        	<? if ($oProfile->GetGeneralType() == PROFILE_PLACEMENT) { ?>
        		<li><?= $oProfile->GetCompanyName(); ?></li>
        	<? } ?>
        	<? if (strlen($oProfile->GetLocationLabel()) > 1) { ?> 
        		<li><?= htmlUtils::convertToPlainText($oProfile->GetLocationLabel()); ?></li> 
        	<? } ?>
        	<? if (is_numeric($oProfile->GetDurationFromId())) { ?>
        		<li><?= $oProfile->GetDurationFromLabel(); ?> to <?= $oProfile->GetDurationToLabel(); ?></li>
        	<? } ?>
        	<?php if (is_numeric($oProfile->GetPriceFromId())) { ?>
        		<li><?= $oProfile->GetPriceFromLabel(); ?> to <?= $oProfile->GetPriceToLabel(); ?>
        		<?= $oProfile->GetCurrencyLabel(); ?></li>
        	<?php } ?>
        	</ul>
        </div>
</div>
<?php if ($oProfile->GetReviewCount() >= 1) { ?>
<script>

$(document).ready(function(){
    $("#rateYo-"+<?= $oProfile->GetId() ?>).rateYo({
    	 rating: <?= $oProfile->GetRating(); ?>,
    	 starWidth: "16px",
    	 fullStar: true,
    	 readOnly: true
    });
});

</script>
<?php } ?>
