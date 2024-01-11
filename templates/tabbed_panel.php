

<!-- BEGIN Tabbed Panel -->
<div id="<?= $this->Get("ID"); ?>" >

	<div class="row mb-3">
		<div class="col">

		<ul class="nav nav-tabs">
		<?
		foreach ($this->Get("TABS") as $oTab) {
		?>
		  <li id="<?= $oTab->GetId() ?>_LI" class="<?= $oTab->GetActive() ? "nav-link tab active" : "tab nav-link"; ?>">
				<a id="<?= $oTab->GetId() ?>" href="<?= $oTab->GetLink(); ?>" title="<?= $oTab->GetDesc(); ?>">
				<?= $oTab->GetTitle(); ?>
				</a>
		  </li>
		<? } // end foreach ?>
		</ul>

		</div>
	</div>

</div>



<div class="row">
<?= $this->Get("CONTENT"); ?>
</div>


</div>
</div>