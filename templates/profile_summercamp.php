<?php

$response = $this->Get('VALIDATION_ERRORS');
$oProfile = $this->Get('COMPANY_PROFILE');

?>

<div class="row formgroup my-2">

<div class="row">
	<span class="label_col">
		<label for="camp_type" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_TYPE]) > 1 ? "color:red;" : ""; ?>">Camp Type <span class="red"> *</span></label>
	</span>
	<span class="input_col">
		<div class="row formgroup my-2">
		  <div class="py-2">
			  <ul class='form-check'>
					<?php
					foreach($this->Get('CAMP_TYPE_LIST') as $idx => $val) {
						print $val;
					}
					?>
			  </ul>
		  </div>
	  </div>

	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_DURATION_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_DURATION_LABEL]) > 1 ? "color:red;" : ""; ?>">Program Duration<span class="red"> *</span></label></span>
	<span class="input_col">
	<div class="row">
	<div class="col-4">from: <?= $this->Get('DURATION_FROM'); ?></div>
	<div class="col-4">to: <?= $this->Get('DURATION_TO'); ?></div>
	<br /><span class="p_small grey">Program durations eg. 1 week to 4 weeks </span>
	</div>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_PRICE_LABEL; ?>" style="<?= (strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_PRICE_LABEL]) > 1) ? "" : ""; ?>">Program Fees<span class="red"> *</span></label></span>
	<span class="input_col">
	<div class="row">
	<div class="col-4">from: <?= $this->Get('PRICE_FROM'); ?></div>
	<div class="col-4">to: <?= $this->Get('PRICE_TO'); ?></div>
	<div class="col-4">&nbsp;<?= $this->Get('CURRENCY'); ?></div>
	<br /><span class="p_small grey">Approx program tuition fees</span>
	</div>
	</span>
</div>



<div class="row my-2">
    <div class="col-6">
    	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMPER_AGE_LABEL]) > 1 ? "color:red;" : ""; ?>">Age Range</label></span>
    	<span class="input_col">
    	<div class="row">
    	<div class="col-4">from <?= $this->Get('CAMPER_AGE_FROM'); ?></div>
    	<div class="col-4">to <?= $this->Get('CAMPER_AGE_TO'); ?></div>
    	</div>
    	</span>
    </div>

    <div class="col-4">
    	<span class="label_col"><label for="state" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_GENDER]) > 1 ? "color:red;" : ""; ?>">Camp Gender</label></span>
    	<span class="input_col">
    	<div class="row">
    	<div class="col-4">&nbsp;<?= $this->Get("CAMP_GENDER_LIST"); ?></div>
    	</div>
    	</span>
    </div>    
</div>


<div class="row formgroup my-2">
        <span class="label_col"><label for="sc_camp_religion" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_RELIGION]) > 1 ? "color:red;" : ""; ?>">Religious Affiliation</label></span>
        <span class="input_col">
        <div class="col-4"><?= $this->Get("CAMP_RELIGION_LIST"); ?></div>
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




<div class="row formgroup my-2">
	<span class="label_col">
		<label style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_CAMP_ACTIVITY]) > 1 ? "color:red;" : ""; ?>">Activities <span class="red"> *</span></label>
	</span>
	<span class="input_col">
		<?
			$oColumnSort = new ColumnSort;
			$oColumnSort->SetElements($this->Get('CAMP_ACTIVITY_LIST'));
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

                <div class="row">
                        <div class="">
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


<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_SEASON_DATES; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_SEASON_DATES]) > 1 ? "color:red;" : ""; ?>">Season Dates</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SUMMERCAMP_SEASON_DATES; ?>" name="<?= PROFILE_FIELD_SUMMERCAMP_SEASON_DATES; ?>" class="form-select" cols="3" /><?= stripslashes($_POST[PROFILE_FIELD_SUMMERCAMP_SEASON_DATES]); ?></textarea>
	<br /><span class="p_small grey">When does program run?  When do you require staff?  When do you start recruiting?</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS]) > 1 ? "color:red;" : ""; ?>">Requirements</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS; ?>" name="<?= PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS; ?>" class="form-select" cols="3" /><?= stripslashes($_POST[PROFILE_FIELD_SUMMERCAMP_REQUIREMENTS]); ?></textarea>
	<br /><span class="p_small grey">Staff/Camp requirements age, qualifications, visas etc</span>
	</span>
</div>

 <div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY]) > 1 ? "color:red;" : ""; ?>">How to Apply</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY; ?>" name="<?= PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY; ?>" class="form-select" cols="3" /><?= stripslashes($_POST[PROFILE_FIELD_SUMMERCAMP_HOW_TO_APPLY]); ?></textarea></span>
</div>

</div>
