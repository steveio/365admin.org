<?php 

$response = $this->Get('VALIDATION_ERRORS');
$oProfile = $this->Get('COMPANY_PROFILE');

?>

<div class="row">
<h2>Camp Info</h2>
</div>

<div class="left-align five clear pad4-b">
	<span class="label_col">
		<label for="camp_type" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_TYPE]) > 1 ? "color:red;" : ""; ?>">Camp Type <span class="red"> *</span></label>
	</span>
	<span class="input_col">
		<?
			$oColumnSort = new ColumnSort;
			$oColumnSort->SetElements($this->Get('CAMP_TYPE_LIST'));
			$oColumnSort->SetCols(3);
			$aElements = $oColumnSort->Sort();
		?>
	
		<div class="left-align four">
			<div class="one-half left-align">
				<ul class='select_list'>
				<?php 
				foreach($aElements[1] as $idx => $val) {
					print $val;
				}
				?>
				</ul>
			</div>
			<div class="one-half left-align">
				<ul class='select_list'>
				<?php 
				foreach($aElements[2] as $idx => $val) {
					print $val;
				}
				?>
				</ul>				
			</div>
			<div class="one-half left-align">
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

<div class="row">
	<span class="label_col"><label for="state" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER]) > 1 ? "color:red;" : ""; ?>">Camp Gender <span class="red"> *</span></label></span>
	<span class="input_col">
	<?= $this->Get("CAMP_GENDER_LIST"); ?>
	</span>
</div>

<div class="row">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_LABEL]) > 1 ? "color:red;" : ""; ?>">Camper Age<span class="red"> *</span></label></span>
	<span class="input_col">
	from <?= $this->Get('CAMPER_AGE_FROM'); ?>
	to <?= $this->Get('CAMPER_AGE_TO'); ?>
	<br />
	</span>
</div>


<div class="row">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_DURATION_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_DURATION_LABEL]) > 1 ? "color:red;" : ""; ?>">Program Duration<span class="red"> *</span></label></span>
	<span class="input_col">
	from: <?= $this->Get('DURATION_FROM'); ?>
	to: <?= $this->Get('DURATION_TO'); ?>
	<br /><span class="p_small grey">Program durations eg. 1 week to 4 weeks </span>
	</span>
</div>

<div class="row">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_PRICE_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_PRICE_LABEL]) > 1 ? "color:red;" : ""; ?>">Program Fees<span class="red"> *</span></label></span>
	<span class="input_col">
	from: <?= $this->Get('PRICE_FROM'); ?>
	to: <?= $this->Get('PRICE_TO'); ?>
	<?= $this->Get('CURRENCY'); ?>
	<br /><span class="p_small grey">Approx program tuition fees</span>
	</span>
</div>


<div class="row">
        <span class="label_col"><label for="sc_camp_religion" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION]) > 1 ? "color:red;" : ""; ?>">Religious Affiliation</label></span>
        <span class="input_col">
        <?= $this->Get("CAMP_RELIGION_LIST"); ?>
        </span>
</div>


<!-- 
<div class="row">
	<span class="label_col"><label for="state" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_NO_STAFF]) > 1 ? "color:red;" : ""; ?>">Number of Staff / Instructors</label></span>
	<span class="input_col">
	<?= $this->Get("NUMBER_OF_STAFF_LIST"); ?>
	</span>
</div>
<div class="row">
	<span class="label_col"><label for="state" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_STAFF_GENDER]) > 1 ? "color:red;" : ""; ?>">Staff Gender</label></span>
	<span class="input_col">
	<?= $this->Get("STAFF_GENDER_LIST"); ?>
	</span>
</div>
<div class="row">
	<span class="label_col"><label for="state" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_STAFF_ORIGIN]) > 1 ? "color:red;" : ""; ?>">Staff Origin</label></span>
	<span class="input_col">
	<?= $this->Get("STAFF_ORIGIN_LIST"); ?>
	</span>
</div>
 -->

<div class="row">
	&nbsp;
</div>




<div class="left-align five clear pad4-b">
	<span class="label_col">
		<label style="margin: 0; <?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_ACTIVITY]) > 1 ? "color:red;" : ""; ?>">Activities <span class="red"> *</span></label>
	</span>
	<span class="input_col">
		<?
			$oColumnSort = new ColumnSort;
			$oColumnSort->SetElements($this->Get('CAMP_ACTIVITY_LIST'));
			$oColumnSort->SetCols(3);
			$aElements = $oColumnSort->Sort();
		?>
	
		<div class="left-align four">
			<div class="one-half left-align">
				<ul class='select_list'>
				<?php 
				foreach($aElements[1] as $idx => $val) {
					print $val;
				}
				?>
				</ul>
			</div>
			<div class="one-half left-align">
				<ul class='select_list'>
				<?php 
				foreach($aElements[2] as $idx => $val) {
					print $val;
				}
				?>
				</ul>				
			</div>
			<div class="one-half left-align">
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

<div class="left-align five clear pad4-b">
        <span class="label_col">
                <label style="margin: 0; <?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_JOB_TYPE]) > 1 ? "color:red;" : ""; ?>">Job Types</label>
        </span>
        <span class="input_col">
                <?
                        $oColumnSort = new ColumnSort;
                        $oColumnSort->SetElements($this->Get('CAMP_JOB_TYPE_LIST'));
                        $oColumnSort->SetCols(1);
                        $aElements = $oColumnSort->Sort();
                ?>

                <div class="left-align four">
                        <div class="one-half left-align">
                                <ul class='select_list'>
                                <?php
                                foreach($aElements[1] as $idx => $val) {
                                        print $val;
                                }
                                ?>
                                </ul>
                        </div>
                </div>
        </span>
</div>


<!-- 
<div class="row">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_SEASON_DATES; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_SEASON_DATES]) > 1 ? "color:red;" : ""; ?>">Season Dates</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SUMMERCAMP_SEASON_DATES; ?>" name="<?= PROFILE_FIELD_SUMMERCAMP_SEASON_DATES; ?>" style="width: 360px; height: 90px;" /><?= stripslashes($_POST[PROFILE_FIELD_SUMMERCAMP_SEASON_DATES]); ?></textarea>
	<br /><span class="p_small grey">How long is your season?  When do you require staff?  When do you start recruiting?</span>
	</span>
</div> 

<div class="row">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS]) > 1 ? "color:red;" : ""; ?>">Requirements</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS; ?>" name="<?= PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS; ?>" style="width: 360px; height: 90px;" /><?= stripslashes($_POST[PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS]); ?></textarea>
	<br /><span class="p_small grey">Staff requirements age, qualifications, visas etc</span>
	</span>
</div>
 --> 

<div class="row">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY]) > 1 ? "color:red;" : ""; ?>">How to Apply</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY; ?>" name="<?= PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY; ?>" style="width: 360px; height: 90px;" /><?= stripslashes($_POST[PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY]); ?></textarea></span>
</div>
