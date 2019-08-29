<!-- BEGIN Tabbed Panel -->
<div id="<?= $this->Get("ID"); ?>" class="page_content col <?= $this->Get("COLS"); ?>">
<div class="">


	<span class="crnr tl"></span>
	<span class="crnr tr"></span>
	<ul class="tabs clear">
	<? 
	foreach ($this->Get("TABS") as $oTab) { 
	?>
	  <li id="<?= $oTab->GetId() ?>_LI" class="<?= $oTab->GetActive() ? "tab active" : "tab"; ?>">
		<span id="<?= $oTab->GetId() ?>_SP" class="<?= $oTab->GetActive() ? "tab active-text" : "tab"; ?>">
			<a id="<?= $oTab->GetId() ?>" href="<?= $oTab->GetLink(); ?>" title="<?= $oTab->GetDesc(); ?>">
			<?= $oTab->GetTitle(); ?>
			</a>
		</span>
		<span class="crnr tl"></span><span class="crnr tr"></span>
	  </li>
	<? } // end foreach ?>
	</ul>
</div>

<div>
<ul class="sub-nav">
  <li class="active last"><span class="active-text"><?= $this->Get("TITLE"); ?></span></li>
</ul>
</div>

<div class="pad border page_content-content clear">
	<?= $this->Get("CONTENT"); ?>
</div>

</div>	
<!-- END Tabbed Panel -->
