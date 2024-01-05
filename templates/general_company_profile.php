<div class="row formgroup my-2">

	<div class="row formgroup my-2">
		<span class="label_col"><label for="duration" style="<?= strlen($response['msg'][PROFILE_FIELD_COMP_GENERAL_DURATION]) > 1 ? "color:red;" : ""; ?>">Duration / Dates</label></span>
		<span class="input_col"><textarea  class="form-control" id="duration" name="duration" /><?= stripslashes($_POST[PROFILE_FIELD_COMP_GENERAL_DURATION]); ?></textarea></span>
	</div>

	<div class="row formgroup my-2">
		<span class="label_col"><label for="job_info" style="<?= strlen($response['msg'][PROFILE_FIELD_COMP_GENERAL_PLACEMENT_INFO]) > 1 ? "color:red;" : ""; ?>">Placement / Job Info</label></span>
		<span class="input_col"><textarea  class="form-control" id="job_info" name="job_info" /><?= stripslashes($_POST[PROFILE_FIELD_COMP_GENERAL_PLACEMENT_INFO]); ?></textarea></span>
	</div>

	<div class="row formgroup my-2">
		<span class="label_col"><label for="costs" style="<?= strlen($response['msg'][PROFILE_FIELD_COMP_GENERAL_COSTS]) > 1 ? "color:red;" : ""; ?>">Costs / Pay</label></span>
		<span class="input_col"><textarea  class="form-control" id="costs" name="costs" /><?= stripslashes($_POST[PROFILE_FIELD_COMP_GENERAL_COSTS]); ?></textarea></span>
	</div>

</div>
