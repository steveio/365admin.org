<?php

$response = $this->Get('VALIDATION_ERRORS');
$oProfile = $this->Get('COMPANY_PROFILE');

?>

<div class="row formgroup my-2">

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COURSES_DURATION_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_COURSES_DURATION_LABEL]) > 1 ? "color:red;" : ""; ?>">Program Duration<span class="red"> *</span></label></span>
	<span class="input_col">
	<div class="row">
	<div class="col-4">from: <?= $this->Get('DURATION_FROM'); ?></div>
	<div class="col-4">to: <?= $this->Get('DURATION_TO'); ?></div>
	<br /><span class="p_small grey">Program durations eg. from 1 week to 6 weeks </span>
	</div>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COURSES_PRICE_LABEL; ?>" style="<?= (strlen($response['msg'][PROFILE_FIELD_COURSES_PRICE_LABEL]) > 1) ? "color:red" : ""; ?>">Approx Program Fees<span class="red"> *</span></label></span>
	<span class="input_col">
	<div class="row">
	<div class="col-4">from: <?= $this->Get('PRICE_FROM'); ?></div>
	<div class="col-4">to: <?= $this->Get('PRICE_TO'); ?></div>
	<div class="col-4">&nbsp;<?= $this->Get('CURRENCY'); ?></div>
	<br /><span class="p_small grey">Approx program fees</span>
	</div>
	</span>
</div>


<div class="row formgroup my-2">
	<span class="label_col my-2">
		<label style="<?= strlen($response['msg'][PROFILE_FIELD_COURSES_LANGUAGES]) > 1 ? "color:red;" : ""; ?>">Languages</label>
	</span>
	<span class="input_col">
		<?
			$oColumnSort = new ColumnSort;
			$oColumnSort->SetElements($this->Get('LANGUAGES_LIST'));
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

<div class="row my-2">    
    
    <div class="col-3 formgroup my-2">
        <div class="label_col my-2">
                <label class="<?= strlen($response['msg'][PROFILE_FIELD_COURSES_COURSE_TYPE]) > 1 ? "color:red;" : ""; ?>">Course Type(s)</label>
        </div>
        <span class="input_col">
        <?
                $oColumnSort = new ColumnSort;
                $oColumnSort->SetElements($this->Get('COURSE_TYPE_LIST'));
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
    
    <div class="col-3 formgroup my-2">
        <div class="label_col my-2">
                <label class="<?= strlen($response['msg'][PROFILE_FIELD_COURSES_COURSES]) > 1 ? "color:red;" : ""; ?>">Other Study / Vocational Courses</label>
        </div>
        <span class="input_col">
        <?
                $oColumnSort = new ColumnSort;
                $oColumnSort->SetElements($this->Get('COURSES_LIST'));
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
    
    <div class="col-3 formgroup my-2">
        <div class="label_col my-2">
                <label style="margin: 0; <?= strlen($response['msg'][PROFILE_FIELD_COURSES_ACCOMODATION]) > 1 ? "color:red;" : ""; ?>">Accomodation Options</label>
        </div>
        <span class="input_col">
        <?
                $oColumnSort = new ColumnSort;
                $oColumnSort->SetElements($this->Get('ACCOMODATION_LIST'));
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

</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COURSES_START_DATES; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_COURSES_START_DATES]) > 1 ? "color:red;" : ""; ?>">Start Dates</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_COURSES_START_DATES; ?>" name="<?= PROFILE_FIELD_COURSES_START_DATES; ?>" class="form-select" cols="3" /><?= stripslashes($_POST[PROFILE_FIELD_COURSES_START_DATES]); ?></textarea>
	<br /><span class="p_small grey">Course start dates / term times</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COURSES_REQUIREMENTS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_COURSES_REQUIREMENTS]) > 1 ? "color:red;" : ""; ?>">Requirements</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_COURSES_REQUIREMENTS; ?>" name="<?= PROFILE_FIELD_COURSES_REQUIREMENTS; ?>" class="form-select" cols="3" /><?= stripslashes($_POST[PROFILE_FIELD_COURSES_REQUIREMENTS]); ?></textarea>
	<br /><span class="p_small grey">Age, Nationality, Education, Visa</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COURSES_QUALIFICATION; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_COURSES_QUALIFICATION]) > 1 ? "color:red;" : ""; ?>">Qualification / Certfications</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_COURSES_QUALIFICATION; ?>" name="<?= PROFILE_FIELD_COURSES_QUALIFICATION; ?>" class="form-select" cols="3" /><?= stripslashes($_POST[PROFILE_FIELD_COURSES_QUALIFICATION]); ?></textarea>
	<br /><span class="p_small grey">What qualifications are awarded on course completion?</span>
	</span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COURSES_PREPARATION; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_COURSES_PREPARATION]) > 1 ? "color:red;" : ""; ?>">Preparation / Guidelines</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_COURSES_PREPARATION; ?>" name="<?= PROFILE_FIELD_COURSES_PREPARATION; ?>" class="form-select" cols="3" /><?= stripslashes($_POST[PROFILE_FIELD_COURSES_PREPARATION]); ?></textarea>
	<br /><span class="p_small grey">Detail any guidance on suggested preparation or course guidelines</span>
	</span>
</div>

 <div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COURSES_HOW_TO_APPLY; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_COURSES_HOW_TO_APPLY]) > 1 ? "color:red;" : ""; ?>">How to Apply</label></span>
	<span class="input_col"><textarea id="<?= PROFILE_FIELD_COURSES_HOW_TO_APPLY; ?>" name="<?= PROFILE_FIELD_COURSES_HOW_TO_APPLY; ?>" class="form-select" cols="3" /><?= stripslashes($_POST[PROFILE_FIELD_COURSES_HOW_TO_APPLY]); ?></textarea></span>
</div>

</div>
