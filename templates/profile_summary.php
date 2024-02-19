<?php 

$oProfile = $this->Get('oProfile');
$displayRelatedProfile = $this->Get('displayRelatedProfile');


$strCompanyLogoHtml = $this->Get('strCompanyLogoHtml');

if (!is_object($oProfile)) return;
?>

<div class="col-6 py-2 my-2">

    	<div class="float-end"><?
    	if (is_object($oProfile->GetImage(0)) && $oProfile->GetImage(0)->GetHtml("_mf",'')) { ?>
    		<a title="<?= $oProfile->GetTitle() ?>" href="<?= "/company/".$oProfile->GetCompUrlName()."/".$oProfile->GetUrlName() ?>" class=""> 
    		<img class="img-responsive img-rounded" src="<?= $oProfile->GetImage(0)->GetUrl("_mf");  ?>" alt="<?= $oProfile->GetTitle() ?>" /> 		
    		</a>
        	<?php 
        	if (strlen($strCompanyLogoHtml) < 1 && is_object($oProfile->GetCompanyLogo()))
        	{
        	    $strCompanyLogoHtml = $oProfile->GetCompanyLogo()->GetHtml('_sm');
        	}
        	if (strlen($strCompanyLogoHtml) > 1) {
        	?>
        	<div class="overlay-img">
        		<a title="<?= $oProfile->GetCompanyName() ?>" href="<?= $oProfile->GetCompanyProfileUrl() ?>" target="_new" class="">
        		<?= $strCompanyLogoHtml; ?>
        		</a>
        	</div>
        	<?php } ?>

    	<? } ?>
    	</div>

        <div class="col-8">
        	<h3><a href="<?= "/company/".$oProfile->GetCompUrlName()."/".$oProfile->GetUrlName() ?>" title="" target="_new"><?= $oProfile->GetTitle(); ?></a></h3>    

            <?php if ($oProfile->GetReviewCount() >= 1) { ?>
            <input type="hidden" id="rateYo-<?= $oProfile->GetId() ?>-rating" value="<?= $oProfile->GetRating(); ?>" />
            <div class="row my-2">
                <div id="rateYo-<?= $oProfile->GetId() ?>" class="rating col-4"></div>
                <?php  $reviewLabel = ($oProfile->GetReviewCount() == 1) ? "Review" : "Review"; ?>
                <div class="col-4 small">( <?= $oProfile->GetReviewCount(). " ".$reviewLabel." ) "; ?></div>
            </div><?
            } ?>
        
        	<p><?= $oProfile->GetDescShortPlaintext(160); ?></p>
        
        	<ul class="details small">
        	<? if ($displayRelatedProfile != "COMPANY") { ?>
        	<?= $oProfile->GetCompanyName(); ?><br/> 
        	<? } ?>
        	<? if (strlen($oProfile->GetLocationLabel()) > 1) { ?> 
        	<?= "Location: ". htmlUtils::convertToPlainText($oProfile->GetLocationLabel()); ?><br/> 
        	<? } ?>
        	<? if (is_numeric($oProfile->GetDurationFromId())) { ?>
        	<?= "Duration: ". $oProfile->GetDurationFromLabel(); ?> to <?= $oProfile->GetDurationToLabel(); ?><br />
        	<? } ?>
        	<?php if (is_numeric($oProfile->GetPriceFromId())) { ?>
        	<?=    $oProfile->GetPriceFromLabel(); ?> to <?= $oProfile->GetPriceToLabel(); ?>
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