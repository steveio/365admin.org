<?php

$response = $this->Get('VALIDATION_ERRORS');
$oProfile = $this->Get('COMPANY_PROFILE');

?>


<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_VOLUNTEER_DURATION_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_VOLUNTEER_DURATION_LABEL]) > 1 ? "color:red;" : ""; ?>">Duration(s)<span class="red"> *</span></label></span>
	<span class="input_col">
	from: <?= $this->Get('DURATION_FROM'); ?>
	to: <?= $this->Get('DURATION_TO'); ?>
	<br /><span class="p_small grey">Approx project durations eg. 3 weeks to 3 months </span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_VOLUNTEER_PRICE_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_VOLUNTEER_PRICE_LABEL]) > 1 ? "color:red;" : ""; ?>">Project Costs<span class="red"> *</span></label></span>
	<span class="input_col">
	from: <?= $this->Get('PRICE_FROM'); ?>
	to: <?= $this->Get('PRICE_TO'); ?>
	<?= $this->Get('CURRENCY'); ?>
	<br /><span class="p_small grey">Approx range of project cost</span>
	</span>
</div>


<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_VOLUNTEER_NO_PLACEMENTS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_VOLUNTEER_NO_PLACEMENTS]) > 1 ? "color:red;" : ""; ?>">Team Size / Num Volunteers</label></span>
	<span class="input_col">
	<?= $this->Get('NO_PLACEMENTS'); ?>
	<br /><span class="p_small grey">Approx number of volunteers / placements</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_VOLUNTEER_ORG_TYPE; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_VOLUNTEER_ORG_TYPE]) > 1 ? "color:red;" : ""; ?>">Organisation Type</label></span>
	<span class="input_col">
	<?= $this->Get('ORG_TYPE'); ?>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_VOLUNTEER_FOUNDED; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_VOLUNTEER_FOUNDED]) > 1 ? "color:red;" : ""; ?>">Founded / Established</label></span>
	<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_VOLUNTEER_FOUNDED; ?>" maxlength="32" class="form-control"  name="<?= PROFILE_FIELD_VOLUNTEER_FOUNDED; ?>" value="<?= $_POST[PROFILE_FIELD_VOLUNTEER_FOUNDED]; ?>" /></span>
</div>


<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_VOLUNTEER_SUPPORT; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_VOLUNTEER_SUPPORT]) > 1 ? "color:red;" : ""; ?>">Volunteer Support</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_VOLUNTEER_SUPPORT; ?>" name="<?= PROFILE_FIELD_VOLUNTEER_SUPPORT; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_VOLUNTEER_SUPPORT]); ?></textarea>
	<br /><span class="p_small grey">Support / assistance / training offered to volunteers before, during or after project</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_VOLUNTEER_SAFETY; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_VOLUNTEER_SAFETY]) > 1 ? "color:red;" : ""; ?>">Safety</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_VOLUNTEER_SAFETY; ?>" name="<?= PROFILE_FIELD_VOLUNTEER_SAFETY; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_VOLUNTEER_SAFETY]); ?></textarea>
	<br /><span class="p_small grey">Safety measures and/or in the field support available</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_VOLUNTEER_AWARDS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_VOLUNTEER_AWARDS]) > 1 ? "color:red;" : ""; ?>">Awards</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_VOLUNTEER_AWARDS; ?>" name="<?= PROFILE_FIELD_VOLUNTEER_AWARDS; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_VOLUNTEER_AWARDS]); ?></textarea>
	<br /><span class="p_small grey">Any awards or recognition your project / organisation has won</span>
	</span>
</div>

<div class="row formgroup my-2">
	<div class="row my-2">
		<span class="label_col">
			Species
			<br /><span class="p_small grey">List any animal species your organisation or projects work with</span>
		</span>
	</div>
	<div class="row">
		<span class="input_col" style="">
			<a id="expand_species_select">+ Expand</a> <a id="collapse_species_select">- Collapse</a> (<span id="species_selected"><?= $this->Get('SPECIES_LIST_SELECTED_COUNT'); ?></span> Selected)
		</span>
	</div>
	<span id="species_select" class="input_col" style="display: none;">

		<?
			$oColumnSort = new ColumnSort;
			$oColumnSort->SetElements($this->Get('SPECIES_LIST'));
			$oColumnSort->SetCols(3);
			$aElements = $oColumnSort->Sort();
		?>

		<div class="row">
			<div class="col-3">
				<ul class='select_list'>
				<?php
				foreach($aElements[1] as $idx => $val) {
					print $val;
				}
				?>
				</ul>
			</div>
			<div class="col-3">
				<ul class='select_list'>
				<?php
				foreach($aElements[2] as $idx => $val) {
					print $val;
				}
				?>
				</ul>
			</div>
			<div class="col-3">
				<ul class='select_list'>
				<?php
				foreach($aElements[3] as $idx => $val) {
					print $val;
				}
				?>
				</ul>
			</div>
		</div>

	</span>
</div>

<div class="row formgroup my-2">
	<div class="row">
		<span class="label_col">
			Habitats
			<br /><span class="p_small grey">List environments where your projects are situated</span>
		</span>
	</div>
	<div class="row">
		<span class="input_col" style="">
<			a id="expand_habitats_select">+ Expand</a> <a id="collapse_habitats_select">- Collapse</a> (<span id="habitats_selected"><?= $this->Get('HABITATS_LIST_SELECTED_COUNT'); ?></span> Selected)
		</span>
	</div>
	<span id="habitats_select" class="input_col" style="display: none;">
		<?
			$oColumnSort = new ColumnSort;
			$oColumnSort->SetElements($this->Get('HABITATS_LIST'));
			$oColumnSort->SetCols(3);
			$aElements = $oColumnSort->Sort();
		?>
		<div class="row">
			<div class="col-3">
				<ul class='select_list'>
				<?php
				foreach($aElements[1] as $idx => $val) {
					print $val;
				}
				?>
				</ul>
			</div>
			<div class="col-3">
				<ul class='select_list'>
				<?php
				foreach($aElements[2] as $idx => $val) {
					print $val;
				}
				?>
				</ul>
			</div>
			<div class="col-3">
				<ul class='select_list'>
				<?php
				foreach($aElements[3] as $idx => $val) {
					print $val;
				}
				?>
				</ul>
			</div>
		</div>

	</span>
</div>
