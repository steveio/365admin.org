<?php 

$oCProfile = $this->Get('oCProfile');
//$oListing = $this->Get('oListing');

?>
<form enctype="multipart/form-data" name="" action="<? $_SERVER['PHP_SELF'] ?>" method="POST">


<div class="five">
	<?php if ($oAuth->oUser->isAdmin) { ?> 	
	<div class="left-align"><a class="more-link" href="/<?= ROUTE_DASHBOARD; ?>" title="Admin Dashboard Link">Back to Admin Dashboard</a></div>
	<?php } ?>
	<div class="right-align"><p>Need Help?  <a href="/<?= ROUTE_CONTACT; ?>">Contact Us</a></p></div>
</div>

<div class="five left-align">

	<div class="left-align five">
	<? if (is_object($oCProfile->GetImage(0,LOGO_IMAGE))) { ?>
		<div class="pad-t pad-b"><?= $oCProfile->GetImage(0,LOGO_IMAGE)->GetHtml("",$oCProfile->GetTitle()) ?></div>
	<? } ?>
	</div>
	
	<h1><?= $oCProfile->GetTitle(); ?></h1>

	<a class="more-link" href="<?= $oBrand->GetWebsiteUrl(); ?>/<?= $oCProfile->GetProfileUrl() ?>" title="View Company Profile" target="_new">View Company Profile</a>
	<br />
	<a class="more-link" href="/<?= $oCProfile->GetProfileUrl() ?>/edit" title="Edit Company Profile">Edit Company Profile</a>
	<br />

	<?php if ($oAuth->oUser->isAdmin) { ?>
		<br />	 	
		<a class="more-link" href="/<?= $oCProfile->GetProfileUrl() ?>/delete" title="Delete Company and Profiles"  onclick="javascript: return confirm('This will delete (archive) company and all placements - you can restore it later if necessary.  Are you sure?')">Delete Company and Profiles</a>
		<br /><br />
	<?php } ?>

	<?php if ($this->Get('PROFILE_QUOTA') >= 1) { ?>	
		<a class="more-link" href="<?= "/".ROUTE_PLACEMENT ?>/add" title="Add <?= $this->Get('PLACEMENT_TITLE_SINGULAR'); ?>">Add <?= $this->Get('PLACEMENT_TITLE_SINGULAR'); ?></a>
		
		<h2><?= $this->Get('PLACEMENT_TITLE_PLURAL'); ?> (<?= $this->Get('PROFILE_COUNT'); ?> of <?= $this->Get('PROFILE_QUOTA'); ?>)</h2>
	<?php } ?>



	<div class="left-align five pad3-t">
	<table cellpadding='2' cellspacing='3' border='0'>
	<?
	$i = 1;
	$aProfile = $this->Get('PROFILE_ARRAY');
	if (is_array($aProfile) && count($aProfile) >= 1) {
	?>
	        <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th><b>Title</th>
                <th>Description</th>
                <th>View</th>
                <th>Edit</th>
                <th>Delete</th>
	        </tr>
	        <? foreach ($aProfile as $oProfile) {  ?>
                <? $class = ($class == "hi") ? "" : "hi"; ?>
                <tr class='<?= $class ?>'>
                        <td valign="top"><b><?= $i++ ?></b></td>
                        <? if (is_object($oProfile->GetImage(0))) { ?>
                        <td valign="top"><?= $oProfile->GetImage(0)->GetHtml("_sf",""); ?></td>
                        <? } else { ?>
                        <td valign="top">(no image)</td>
                        <? } ?>
                        <td valign="top" width='120px' class='smalltext'><?= $oProfile->GetTitle() ?></td>
                        <td valign="top" width='240px' class='smalltext'><?= $oProfile->GetDescShort(220) ?></td>
                        <td valign="top"><a class="more-link p_small" href="<?= $this->Get('WEBSITE_URL'); ?>/<?= $oProfile->GetProfileUrl() ?>" target="_new">view</a></td>
                        <td valign="top"><a class="more-link p_small" href="/<?= ROUTE_PLACEMENT ."/". $oProfile->GetUrlName() ?>/edit/">edit</a></td>
                        <td valign="top"><a class="more-link p_small" href="/<?= ROUTE_PLACEMENT ."/". $oProfile->GetUrlName() ?>/delete/" onclick="javascript: return confirm('This will delete the placement - you can restore it later if necessary.  Are you sure?')">delete</a></td>
                </tr>
	        <? } ?>
	<? } ?>
	</table>
	</div>

</div>

</form>
