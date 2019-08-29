

	<div class="row pad4-t pad3-b">
	<h2>Placement Info</h2>
	<div class="left-align pad3-r">
	<img src="/images/icon_info.png" alt="" border="0"  />
	</div>
	<span class="p_small grey">Provide a summary of the project or the opportunity you are offering.<br />
	If you are advertising multiple jobs provide a brief summary of the range of opportunities (you can post ads for specific positions later).
	</span>

	</div>

	<div class="row">
		<span class="label_col"><label for="duration" style="<?= strlen($response['msg'][PROFILE_FIELD_COMP_GENERAL_DURATION]) > 1 ? "color:red;" : ""; ?>">Duration / Dates</label></span>
		<span class="input_col"><textarea id="duration" name="duration" style="width: 360px; height: 90px;" /><?= stripslashes($_POST[PROFILE_FIELD_COMP_GENERAL_DURATION]); ?></textarea></span>
	</div> 

	<div class="row">
		<span class="label_col"><label for="job_info" style="<?= strlen($response['msg'][PROFILE_FIELD_COMP_GENERAL_PLACEMENT_INFO]) > 1 ? "color:red;" : ""; ?>">Placement / Job Info</label></span>
		<span class="input_col"><textarea id="job_info" name="job_info" style="width: 360px; height: 90px;" /><?= stripslashes($_POST[PROFILE_FIELD_COMP_GENERAL_PLACEMENT_INFO]); ?></textarea></span>
	</div> 

	<div class="row">
		<span class="label_col"><label for="costs" style="<?= strlen($response['msg'][PROFILE_FIELD_COMP_GENERAL_COSTS]) > 1 ? "color:red;" : ""; ?>">Costs / Pay</label></span>
		<span class="input_col"><textarea id="costs" name="costs" style="width: 360px; height: 90px;" /><?= stripslashes($_POST[PROFILE_FIELD_COMP_GENERAL_COSTS]); ?></textarea></span>
	</div> 


	<div class="row">
		&nbsp;
	</div>
	
	
