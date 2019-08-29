<div class="five">
	<div class="left-align">	
	<?php if (strlen($this->Get('VIEW_URL')) > 1) { ?>
	<a class="more-link" href="<?= $this->Get('VIEW_URL'); ?>" title="View Profile">View Profile on <?= $oBrand->GetName(); ?></a> <br />
	<?php } ?>
	<?php if (is_object($oAuth->oUser)) { ?>
	<a class="more-link" href="/<?= ROUTE_DASHBOARD; ?>" title="Admin Dashboard Link">Back to Dashboard</a>
	<?php } ?>
	</div>
	<div class="right-align">Need Help?  <a href="/<?= ROUTE_CONTACT; ?>">Contact Us</a></div>
</div>