<?php

$response = $this->Get('VALIDATION_ERRORS');
$oProfile = $this->Get('COMPANY_PROFILE');

?>


<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_TEACHING_DURATION_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_TEACHING_DURATION_LABEL]) > 1 ? "color:red;" : ""; ?>">Duration(s)<span class="red"> *</span></label></span>
	<span class="input_col">
	<div class="row">
	<div class="col-6">from: <?= $this->Get('DURATION_FROM'); ?></div>
	<div class="col-6">to: <?= $this->Get('DURATION_TO'); ?></div>
	<br /><span class="p_small grey">Approx job/course durations eg. 3 weeks to 3 months </span>
	</div>
	</span>
</div>

<div class="row formgroup my-2">
	<div class="col-6">
	<span class="label_col"><label for="<?= PROFILE_FIELD_TEACHING_NO_TEACHERS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_TEACHING_NO_TEACHERS]) > 1 ? "color:red;" : ""; ?>">Number of Teachers / Jobs</label></span>
	<span class="input_col">
	<?= $this->Get('NUMBER_OF_TEACHERS'); ?>	
	</span>
	</div>

	<div class="col-6">
	<span class="label_col"><label for="<?= PROFILE_FIELD_TEACHING_CLASS_SIZE; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_TEACHING_CLASS_SIZE]) > 1 ? "color:red;" : ""; ?>">Class Size</label></span>
	<span class="input_col">
	<?= $this->Get('CLASS_SIZE'); ?>
	</span>
	</div>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_TEACHING_SALARY; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_TEACHING_SALARY]) > 1 ? "color:red;" : ""; ?>">Salary / Costs</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_TEACHING_SALARY; ?>" rows="3" name="<?= PROFILE_FIELD_TEACHING_SALARY; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_TEACHING_SALARY]); ?></textarea>
	<br /><span class="p_small grey">Specify approx salary / pay for teaching jobs or detail student costs for courses</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_TEACHING_BENEFITS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_TEACHING_BENEFITS]) > 1 ? "color:red;" : ""; ?>">Benefits</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_TEACHING_BENEFITS; ?>" rows="3"  name="<?= PROFILE_FIELD_TEACHING_BENEFITS; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_TEACHING_SALARY]); ?></textarea>
	<br /><span class="p_small grey">Any benefits offered eg live in accomodation, meals, training or support</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_TEACHING_QUALIFICATIONS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_TEACHING_QUALIFICATIONS]) > 1 ? "color:red;" : ""; ?>">Qualifications</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_TEACHING_QUALIFICATIONS; ?>" rows="3"  name="<?= PROFILE_FIELD_TEACHING_QUALIFICATIONS; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_TEACHING_QUALIFICATIONS]); ?></textarea>
	<br /><span class="p_small grey">Qualifications awarded or required</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_TEACHING_REQUIREMENTS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_TEACHING_REQUIREMENTS]) > 1 ? "color:red;" : ""; ?>">Requirements / Nationalities</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_TEACHING_REQUIREMENTS; ?>" rows="3"  name="<?= PROFILE_FIELD_TEACHING_REQUIREMENTS; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_TEACHING_REQUIREMENTS]); ?></textarea>
	<br /><span class="p_small grey">Who are your courses / jobs open to, what requirements must be met?</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_TEACHING_HOW_TO_APPLY; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_TEACHING_HOW_TO_APPLY]) > 1 ? "color:red;" : ""; ?>">How to Apply</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_TEACHING_HOW_TO_APPLY; ?>" rows="3" name="<?= PROFILE_FIELD_TEACHING_HOW_TO_APPLY; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_TEACHING_HOW_TO_APPLY]); ?></textarea></span>
</div>
