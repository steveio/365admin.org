<?php 

$oProfile = $this->Get('oProfile');
if (!is_object($oProfile)) return;

$bHideProfileDetails = $this->Get('bHideProfileDetails');
$bDisplayPromoImg = $this->Get('bDisplayPromoImg');
$sImgSize = (strlen($this->Get('sImgSize')) >= 1) ? $this->Get('sImgSize') : "_lf";

?>

<div class="col-sm-12 col-md-3 col-lg-3 my-2">

    	<div class="col-sm-12 img-container"><?    
        if ($bDisplayPromoImg && is_object($oProfile->GetImageByType(2)))
    	{  ?>
            <div class="col-12 profile-image">
                    <a title="<?= $oProfile->GetDescShortPlaintext(120) ?>" href="<?= $oProfile->GetProfileUrl();  ?>" class="">
                            <img class="img-fluid rounded mb-3" src="<?= $oProfile->GetImageByType(2)->GetUrl("_mf");  ?>" alt="<?= $oProfile->GetTitle() ?>" />
                    </a>
            </div><?
    	} elseif (is_object($oProfile->GetImage(0))) // profile has 1 or more image
    	{ 
    	    if ($oProfile->GetImage(0)->GetHtml($sImgSize) != false) // try fetch large fixed-aspect image  
    	    { ?>
    	    	<a title="<?= $oProfile->GetDescShortPlaintext(120) ?>" href="<?= $oProfile->GetProfileUrl();  ?>" class=""><?
    	        $img_url = $oProfile->GetImage(0)->GetUrl($sImgSize); ?>
    	        </a><?
    	    } ?>
    		<div class="col-12 profile-image">
        		<a title="<?= $oProfile->GetDescShortPlaintext(120) ?>" href="<?= $oProfile->GetProfileUrl();  ?>" class="">
				<img class="img-fluid rounded mb-3" src="<?= $img_url;  ?>" alt="<?= $oProfile->GetTitle() ?>" />
        		</a>
        	</div><?
        	if (is_object($oProfile->GetImageByType(1))) 
        	{
        	?>
        	<div class="brand-overlay">
        		<?= $oProfile->GetImageByType(1)->GetHtml("_sm"); ?>
        	</div><?
        	}
    	} elseif (is_object($oProfile->GetImageByType(1))) { ?>
            <div class="col-12 profile-image">
                    <a title="<?= $oProfile->GetTitle() ?>" href="<?= $oProfile->GetProfileUrl();  ?>" class="">
                            <img class="img-fluid rounded mb-3" src="<?= $oProfile->GetImageByType(1)->GetUrl("");  ?>" alt="<?= $oProfile->GetTitle() ?>" />
                    </a>
            </div><?
    	} ?>
    	</div>

        <div class="col-lg-10 col-sm-12">
        <? if (!$bHideProfileDetails) { ?>
       	    <h4><a href="<?= $oProfile->GetProfileUrl(); ?>" title="<?= $oProfile->GetDescShortPlaintext(120); ?>" target="_new"><?= $oProfile->GetTitle(); ?></a></h4>
       	<? } ?>    

            <?php if ($oProfile->GetReviewCount() >= 1) { ?>
            <input type="hidden" id="rateYo-<?= $oProfile->GetId() ?>-rating" value="<?= $oProfile->GetRating(); ?>" />
            <div class="row m-2">
                <div id="rateYo-<?= $oProfile->GetId() ?>" class="rating col-4"></div>
                <?php  $reviewLabel = ($oProfile->GetReviewCount() == 1) ? "Review" : "Review"; ?>
                <div class="col-6 small">( <?= $oProfile->GetReviewCount(). " ".$reviewLabel." ) "; ?></div>
            </div><?
            } ?>

		<? if (!$bHideProfileDetails) { ?>
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
        	<?php if (is_numeric($oProfile->GetPriceFromId()) && $oProfile->GetPriceFromLabel() != "0") { ?>
        		<li><?= $oProfile->GetPriceFromLabel(); ?> to <?= $oProfile->GetPriceToLabel(); ?>
        		<?= $oProfile->GetCurrencyLabel(); ?></li>
        	<?php } ?>
        	</ul>
        <? } ?>
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
