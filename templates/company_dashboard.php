<?php

$oCProfile = $this->Get('oCProfile');
//$oListing = $this->Get('oListing');

?>
<form enctype="multipart/form-data" name="" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">


<div class="row">
	<div><p>Need Help?  <a href="/<?= ROUTE_CONTACT; ?>">Contact Us</a></p></div>
</div>

<div class="row">

	<h1><?= $oCProfile->GetTitle(); ?></h1>

	<div class="col-12">
		<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.open('<?= $oCProfile->GetProfileUrl() ?>'); return false;">View Company Profile</button>
		<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.open('/company/<?= $oCProfile->GetUrlName() ?>/edit'); return false;">Edit Company Profile</button>
		<?php if ($oCProfile->GetListingType() >= BASIC_LISTING) { ?>
		<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.open('<?= "/".ROUTE_PLACEMENT ?>/add'); return false;">Add Placement</button>
		<?php } ?>

    	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/enquiry-report'; return false;">Enquiries</button>
    	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/review-report'; return false;">Comments / Reviews</button>

	</div>

	<div class="col">
	<? if (is_object($oCProfile->GetImage(0,LOGO_IMAGE))) { ?>
		<div class="my-3"><?= $oCProfile->GetImage(0,LOGO_IMAGE)->GetHtml("",$oCProfile->GetTitle()) ?></div>
	<? } ?>
	</div>
</row>

<div class="row my-3">

	<?php if ($this->Get('PROFILE_QUOTA') >= 1) { ?>			
		<h2><?= $this->Get('PLACEMENT_TITLE_PLURAL'); ?> (<?= $this->Get('PROFILE_COUNT'); ?> of <?= $this->Get('PROFILE_QUOTA'); ?>)</h2>
	<?php } ?>

	<div class="col-12">
	<table id="report" cellpadding='0' cellspacing='0' border='0' class='table table-striped'>
	<?
	$i = 1;
	$aProfile = $this->Get('PROFILE_ARRAY');
	if (is_array($aProfile) && count($aProfile) >= 1) {
	?>
		<thead>
	        <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th><b>Title</th>
                <th>Description</th>
                <th>View</th>
                <th>Edit</th>
                <th>Delete</th>
	        </tr>
	    </thead>
	    <tbody>
	        <? foreach ($aProfile as $oProfile) {  ?>
                <tr class='<?= $class ?>'>
                        <td><b><?= $i++ ?></b></td>
                        <? if (is_object($oProfile->GetImage(0))) { ?>
                        <td><?= $oProfile->GetImage(0)->GetHtml("_sf",""); ?></td>
                        <? } else { ?>
                        <td>(no image)</td>
                        <? } ?>
                        <td><?= $oProfile->GetTitle() ?></td>
                        <td><?= strip_tags($oProfile->GetDescShort(220)); ?></td>

						<td><button class="btn btn-primary rounded-pill px-3" type="button" onclick="javascript: window.open('<?= $oProfile->GetProfileUrl() ?>'); return false;">View</button></td>
						<td><button class="btn btn-primary rounded-pill px-3" type="button" onclick="javascript: window.open('/<?= ROUTE_PLACEMENT ."/". $oProfile->GetUrlName() ?>/edit/'); return false;">Edit</button></td>
						<td><button class="btn btn-primary rounded-pill px-3" type="button" onclick="javascript: if(confirm('This will delete the placement - you can restore it later if necessary.  Are you sure?') = true) { window.location = '/<?= ROUTE_PLACEMENT ."/". $oProfile->GetUrlName() ?>/delete/'; return false;">Delete</button></td>
                </tr>
	        <? } ?>
	     </tbody>
	<? } ?>
	</table>

<script>

$(document).ready(function() {
	
    $('#report').DataTable({
    	"pageLength": 100,
    	"bSort" : true
    });

});

</script>

	</div>

</div>


</form>
