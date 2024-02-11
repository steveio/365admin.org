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

<div class="col-6 featured-proj">

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
    	<div class="overlay-brand">
    		<a title="<?= $oProfile->GetCompanyName() ?>" href="<?= $oProfile->GetCompanyProfileUrl() ?>" target="_new" class="">
    		<?= $oProfile->GetCompanyLogo()->GetHtml("_sm") ?>
    		</a>
    	</div>
    	<?php } ?>
	</div>
    <div class="col-8 details">
    	<h3><a href="<?= "/company/".$oProfile->GetCompUrlName()."/".$oProfile->GetUrlName() ?>" title="" target="_new"><?= $oProfile->GetTitle(); ?></a></h3>
    
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