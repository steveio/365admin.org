<?php 

$oProfile = $this->Get("oProfile");
$oReviewTemplate = $this->Get("oReviewTemplate");

?>


<div class="container">
<div class="align-items-center justify-content-center">


    <div class="row">
        <div class="col-12 my-3">
        <div class="col-8 sharethis-inline-share-buttons" style="display: block; float: right;"></div>
        </div>
    </div>

	<? if (strlen($this->Get('logo_img')) > 1) { ?>
		<div style=""><?= $this->Get('logo_img') ?></div>
	<? } ?>

	<h1><?= $oProfile->GetTitle(); ?></h1>

    <div class="row my-2">
        <div id="review-overallrating" class="col-3"></div>
        <div class="col-2"><?php if (is_object($oReviewTemplate) &  $oReviewTemplate->Get('HASREVIEWRATING') == true) {
            print "( ".$oReviewTemplate->Get('COUNT'). " Reviews ) ";
        } ?>
        </div>
    </div>

    <div class="row my-2">

		<div class="col-12">
    		<b>Company :</b> <a href="<?= $oProfile->GetCompanyProfileUrl(); ?>" title="Find out more about <?= $oProfile->GetCompanyName(); ?>" style="color: #DD6900;"><?= $oProfile->GetCompanyName(); ?></a><br/>
    		<?php 
    		if (count($oProfile->GetActivityArray()) > 1 && count($oProfile->GetActivityArray()) < 3) {
    			$label = "Activities: "; ?>
    			<b><?= $label; ?></b> <?= $oProfile->GetActivityTxt(); ?><br/><?
    		}
    		?>
    		<?php 
    		if (count($oProfile->GetCountryArray()) > 1 && count($oProfile->GetCountryArray()) < 3) {
    			$label = "Countries: "; ?>
    			<b><?= $label; ?></b> <?= $oProfile->GetCountryTxt(); ?><br/><?
    		} 
    		?>
    		<? if (strlen($oProfile->GetLocation()) > 1) { ?>
    			<b>Location :</b> <?= $oProfile->GetLocation(); ?><br/>
    		<? } ?>
    		<? if (is_numeric($oProfile->GetDurationFromId())) { ?>
    			<b>Duration:</b> <?= $oProfile->GetDurationFromLabel(); ?> to <?= $oProfile->GetDurationToLabel(); ?><br />
    		<? } ?>
    		<?php if (is_numeric($oProfile->GetPriceFromId())) { ?>
    			<b>Approx Costs:</b> <?= $oProfile->GetPriceFromLabel(); ?> to <?= $oProfile->GetPriceToLabel(); ?>
    			<?= $oProfile->GetCurrencyLabel(); ?><br />
    		<?php } ?>
    
    		<? if (method_exists($oProfile, "GetCode") && strlen($oProfile->GetCode()) > 1) { ?>
    			<b>Tour Code :</b> <?= $oProfile->GetCode(); ?>
    		<? } ?>
		</div>
	</div>


</div>
</div>