<?php

$response = $this->Get('VALIDATION_ERRORS');
$oProfile = $this->Get('COMPANY_PROFILE');

?>



<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SEASONALJOBS_JOB_TYPES; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SEASONALJOBS_JOB_TYPES]) > 1 ? "color:red;" : ""; ?>">Job Type(s)</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SEASONALJOBS_JOB_TYPES; ?>" class="form-control" name="<?= PROFILE_FIELD_SEASONALJOBS_JOB_TYPES; ?>" /><?= stripslashes($_POST[PROFILE_FIELD_SEASONALJOBS_JOB_TYPES]); ?></textarea>
	<br /><span class="p_small grey">Describe types of jobs available</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SEASONALJOBS_DURATION_FROM; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SEASONALJOBS_DURATION_FROM]) > 1 ? "color:red;" : ""; ?>">Job Duration(s)<span class="red"> *</span></label></span>
	<span class="input_col">
	from: <?= $this->Get('DURATION_FROM'); ?>
	to: <?= $this->Get('DURATION_TO'); ?>
	<br /><span class="p_small grey">Approx job duration eg. 3 weeks to 3 months </span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SEASONALJOBS_NO_STAFF; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SEASONALJOBS_NO_STAFF]) > 1 ? "color:red;" : ""; ?>">No Staff</label></span>
	<span class="input_col">
	<?= $this->Get('NO_STAFF'); ?>
	<br /><span class="p_small grey">Approx size of your organisation / number of staff</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SEASONALJOBS_PAY; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SEASONALJOBS_PAY]) > 1 ? "color:red;" : ""; ?>">Salary / Pay</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SEASONALJOBS_PAY; ?>" name="<?= PROFILE_FIELD_SEASONALJOBS_PAY; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_SEASONALJOBS_PAY]); ?></textarea></span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SEASONALJOBS_BENEFITS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SEASONALJOBS_BENEFITS]) > 1 ? "color:red;" : ""; ?>">Benefits</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SEASONALJOBS_BENEFITS; ?>" name="<?= PROFILE_FIELD_SEASONALJOBS_BENEFITS; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_SEASONALJOBS_BENEFITS]); ?></textarea>
	<br /><span class="p_small grey">Any benefits offered eg live in accomodation or meals</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SEASONALJOBS_REQUIREMENTS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SEASONALJOBS_REQUIREMENTS]) > 1 ? "color:red;" : ""; ?>">Requirements / Nationalities</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SEASONALJOBS_REQUIREMENTS; ?>" name="<?= PROFILE_FIELD_SEASONALJOBS_REQUIREMENTS; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_SEASONALJOBS_REQUIREMENTS]); ?></textarea></span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SEASONALJOBS_HOW_TO_APPLY; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SEASONALJOBS_HOW_TO_APPLY]) > 1 ? "color:red;" : ""; ?>">How to Apply</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SEASONALJOBS_HOW_TO_APPLY; ?>" name="<?= PROFILE_FIELD_SEASONALJOBS_HOW_TO_APPLY; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_SEASONALJOBS_HOW_TO_APPLY]); ?></textarea></span>
</div>
