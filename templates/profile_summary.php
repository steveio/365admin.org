<?php 

$oProfile = $this->Get('oProfile');
$strCompanyLogoHtml = $this->Get('strCompanyLogoHtml');

if (!is_object($oProfile)) return;

/*
print_r("<pre>");
print_r($oProfile);
print_r("</pre>");
die(__FILE__."::".__LINE__);
*/
?>

<div class="col-6 featured-proj my-2">

    <div class="img-container">
    	<div class="float-end featured-proj-img"><?
    	if (is_object($oProfile->GetImage(0)) && $oProfile->GetImage(0)->GetHtml("_mf",'')) { ?>
    		<a title="<?= $oProfile->GetTitle() ?>" href="<?= "/company/".$oProfile->GetCompUrlName()."/".$oProfile->GetUrlName() ?>" class=""> 
    		<img class="img-responsive img-rounded" src="<?= $oProfile->GetImage(0)->GetUrl("_mf");  ?>" alt="<?= $oProfile->GetTitle() ?>" /> 		
    		</a>
    		<span class="frame-overlay"></span>
    	<? } ?>
    	</div>
    	<?php 
    	if (strlen($strCompanyLogoHtml) > 1) {
    	?>
    	<div class="overlay-img">
    		<a title="<?= $oProfile->GetCompanyName() ?>" href="<?= $oProfile->GetCompanyProfileUrl() ?>" target="_new" class="">
    		<?= $oProfile->GetCompanyLogo()->GetHtml("_sm") ?>
    		</a>
    	</div>
    	<?php } ?>
	</div>
    <div class="col-8 details">
    	<h3><a href="<?= "/company/".$oProfile->GetCompUrlName()."/".$oProfile->GetUrlName() ?>" title="" target="_new"><?= $oProfile->GetTitle(); ?></a></h3>

        <?php if ($oProfile->GetReviewCount() >= 1) { ?>
        <input type="hidden" id="rateYo-<?= $oProfile->GetId() ?>-rating" value="<?= $oProfile->GetRating(); ?>" />
        <div class="row my-2">
            <div id="rateYo-<?= $oProfile->GetId() ?>" class="rating col-4"></div>
            <div class="col-4 small">( <?= $oProfile->GetReviewCount(). " Reviews ) "; ?></div>
        </div><?
        } ?>
    
    	<p><?= $oProfile->GetDescShortPlaintext(160); ?></p>
    
    	<ul class="details">
    	 <? if (strlen($oProfile->GetLocationLabel()) > 1) { ?> 
    	<?= "Location: ". htmlUtils::convertToPlainText($oProfile->GetLocationLabel()); ?><br/> 
    	<? } ?>
    	<? if (is_numeric($oProfile->GetDurationFromId())) { ?>
    	<?= $oProfile->GetDurationFromLabel(); ?> to <?= $oProfile->GetDurationToLabel(); ?><br />
    	<? } ?>
    	<?php if (is_numeric($oProfile->GetPriceFromId())) { ?>
    	<?= $oProfile->GetPriceFromLabel(); ?> to <?= $oProfile->GetPriceToLabel(); ?>
    	<?= $oProfile->GetCurrencyLabel(); ?><br />
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