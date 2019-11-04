<?php 
// type of profile being viewed
$sView = $this->Get('PROFILE_TYPE_ID');

$response = $this->Get('VALIDATION_ERRORS');

?>


<script type="text/javascript">
$(document).ready(function(){

	$("input[name='<?= PROFILE_FIELD_PLACEMENT_PROFILE_TYPE_ID ?>']").click(function() {

		var idx;
		var id;
		var panel_prefix = 'profile_type_';
		var panels = new Array( 'profile_type_<?= PROFILE_VOLUNTEER; ?>',
			'profile_type_<?= PROFILE_TOUR; ?>',
			'profile_type_<?= PROFILE_JOB; ?>'
		);

		idx = $(this).val();
		id = panel_prefix+idx;
		
		for(i=0;i<panels.length;i++) {
			(panels[i] == id) ?  $("#"+panels[i]).show() : $("#"+panels[i]).hide();
		}

	});
	
	
	$('#expand_category_select').click(function() {
		$('#category_select').show();
	});

	$('#collapse_category_select').click(function() {
		$('#category_select').hide();
	});	

	$('#expand_activity_select').click(function() {
		$('#activity_select').show();
	});

	$('#collapse_activity_select').click(function() {
		$('#activity_select').hide();
	});	

	$('#expand_country_select').click(function() {
		$('#country_select').show();
	});

	$('#collapse_country_select').click(function() {
		$('#country_select').hide();
	});		
	
	$("input[name^='cat_']").click(function() {
		var c = parseInt($('#cat_selected').html());
		if ($(this).attr('checked') == true) {
			$('#cat_selected').html(String(++c));
		} else {
			$('#cat_selected').html(String(--c));
		}
	});
	
	$("input[name^='act_']").click(function() {
		var c = parseInt($('#act_selected').html());
		if ($(this).attr('checked') == true) {
			$('#act_selected').html(String(++c));
		} else {
			$('#act_selected').html(String(--c));
		}
	});

	$("input[name^='cty_']").click(function() {
		var c = parseInt($('#cty_selected').html());
		if ($(this).attr('checked') == true) {
			$('#cty_selected').html(String(++c));
		} else {
			$('#cty_selected').html(String(--c));
		}
	});
	

});
</script>


<!-- BEGIN Page Content Container -->
<div class="page_content content-wrap clear">
<div class="row pad-tbl clear">

<h1><?= $this->Get('PLACEMENT_TITLE'); ?> Details</h1>


	<div class="col four clear">	
	<div>
	
	<form enctype="multipart/form-data" name="edit_placement" id="edit_placement" action="#" method="POST">


	<div class="row">
		<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_TITLE; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_TITLE]) > 1 ? "red" : ""; ?>">Title<span class="red"> *</span></label></span>
		<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_PLACEMENT_TITLE; ?>" maxlength="128" class="textinput_01"  name="<?= PROFILE_FIELD_PLACEMENT_TITLE; ?>" value="<?= $_POST[PROFILE_FIELD_PLACEMENT_TITLE]; ?>" /></span>
	</div>	

	<div class="row">
		<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_COMP_ID; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_COMP_ID]) > 1 ? "red" : ""; ?>">Company</label></span>
		<span class="input_col">
		<?= $this->Get("COMPANY_NAME_LIST"); ?>
		</span>
	</div>	

	<div class="row">
		<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_DESC_SHORT; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_DESC_SHORT]) > 1 ? "red" : ""; ?>">Short Description<span class="red"> *</span><br/>(1500 chars or less)</label></span>
		<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_DESC_SHORT; ?>" name="<?= PROFILE_FIELD_PLACEMENT_DESC_SHORT; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_DESC_SHORT]); ?></textarea></span>
	</div> 

	<div class="row">
		<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_DESC_LONG; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_DESC_LONG]) > 1 ? "red" : ""; ?>">Full Description<span class="red"> *</span></label></span>
		<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_DESC_LONG; ?>" name="<?= PROFILE_FIELD_PLACEMENT_DESC_LONG; ?>" class="textarea_02" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_DESC_LONG]); ?></textarea></span>
	</div> 

	<div class="left-align five clear pad4-b">
		<span class="label_col"><h1 style="margin: 0; <?= strlen($response['msg']['category']) > 1 ? "color:red;" : ""; ?>">Categories<span class="red"> *</span></h1></span>
		<span class="input_col" style="">
			<a id="expand_category_select">+ Expand</a> <a id="collapse_category_select">- Collapse</a>  (<span id="cat_selected"><?= $this->Get('CATEGORY_LIST_SELECTED_COUNT'); ?></span> Selected)</a>
		</span>
		<span id="category_select" class="input_col" style="display: none;">
			<div class="left-align four">
				<ul class='select_list'>
					<?= $this->Get('CATEGORY_LIST'); ?>
				</ul>
			</div>
		</span>
	</div>

	<div class="left-align five clear pad4-b">
		<span class="label_col">
			<h1 style="margin: 0; <?= strlen($response['msg']['activity']) > 1 ? "color:red;" : ""; ?>">Activities<span class="red"> *</span></h1>
		</span>
		<span class="input_col" style="">
			<a id="expand_activity_select">+ Expand</a> <a id="collapse_activity_select">- Collapse</a>  (<span id="act_selected"><?= $this->Get('ACTIVITY_LIST_SELECTED_COUNT'); ?></span> Selected)
		</span>
		<span id="activity_select" class="input_col" style="display: none;">
			<?
				$oColumnSort = new ColumnSort;
				$oColumnSort->SetElements($this->Get('ACTIVITY_LIST'));
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
		<span class="label_col"><h1 style="margin: 0; <?= strlen($response['msg']['country']) > 1 ? "color:red;" : ""; ?>">Country(s)<span class="red"> *</span></h1>
		</span>
		<span class="input_col" style="">
			<a id="expand_country_select">+ Expand</a> <a id="collapse_country_select">- Collapse</a>  (<span id="cty_selected"><?= $this->Get('COUNTRY_LIST_SELECTED_COUNT'); ?></span> Selected)
		</span>
		
		<span id="country_select" class="input_col" style="display: none;">
			<?
				$oColumnSort = new ColumnSort;
				$oColumnSort->SetElements($this->Get('COUNTRY_LIST'));
				$oColumnSort->SetCols(4);
				$aElements = $oColumnSort->Sort();
			?>
		
			<div class="left-align four">
				<div class="one left-align">
					<ul class='select_list'>
					<?php 
					foreach($aElements[1] as $idx => $val) {
						print $val;
					}
					?>
					</ul>
				</div>
				<div class="one left-align">
					<ul class='select_list'>
					<?php 
					foreach($aElements[2] as $idx => $val) {
						print $val;
					}
					?>
					</ul>				
				</div>
				<div class="one left-align">
					<ul class='select_list'>
					<?php 
					foreach($aElements[3] as $idx => $val) {
						print $val;
					}
					?>
					</ul>				
				</div>
				<div class="one left-align">
					<ul class='select_list'>
					<?php 
					foreach($aElements[4] as $idx => $val) {
						print $val;
					}
					?>
					</ul>				
				</div>

			</div>

		</span>
	</div> 

	<div class="row">
		<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_LOCATION; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_LOCATION]) > 1 ? "red" : ""; ?>">Location / Region</label></span>
		<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_PLACEMENT_LOCATION; ?>" maxlength="99" class="textinput_01"  name="<?= PROFILE_FIELD_PLACEMENT_LOCATION; ?>" value="<?= $_POST[PROFILE_FIELD_PLACEMENT_LOCATION]; ?>" /></span>
	</div>	

	<div class="row">
		<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_URL; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_URL]) > 1 ? "red" : ""; ?>">More Info Url <span class="red"> *</span></label></span>
		<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_PLACEMENT_URL; ?>" maxlength="255" class="textinput_01"  name="<?= PROFILE_FIELD_PLACEMENT_URL; ?>" value="<?= $_POST[PROFILE_FIELD_PLACEMENT_URL]; ?>" /></span>
	</div>	

	<div class="row">
		<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_EMAIL; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_EMAIL]) > 1 ? "red" : ""; ?>">Enquiries / Apply Email <span class="red"> *</span></label></span>
		<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_PLACEMENT_EMAIL; ?>" maxlength="120" class="textinput_01"  name="<?= PROFILE_FIELD_PLACEMENT_EMAIL; ?>" value="<?= $_POST[PROFILE_FIELD_PLACEMENT_EMAIL]; ?>" />
		<br /><span class="p_small grey">
		Enter a contact email address to receieve enquiry/booking leads directly to your email inbox.
		</span>
	</div>	

	<div class="row">
		<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_APPLY_URL; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_APPLY_URL]) > 1 ? "red" : ""; ?>">Apply / Booking Url </label></span>
		<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_PLACEMENT_APPLY_URL; ?>" maxlength="120" class="textinput_01"  name="<?= PROFILE_FIELD_PLACEMENT_APPLY_URL; ?>" value="<?= $_POST[PROFILE_FIELD_PLACEMENT_APPLY_URL]; ?>" />
		<br /><span class="p_small grey">
		Enter a url to direct enquiries/leads directly to your application/booking page (leave blank to receieve leads by email).
		</span>
	</div>	
	
	<? if ($oAuth->oUser->isAdmin) { ?>
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_KEYWORD_EXCLUDE; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_KEYWORD_EXCLUDE]) > 1 ? "red" : ""; ?>">Keyword Exclude</label></span>
			<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_PLACEMENT_KEYWORD_EXCLUDE; ?>" maxlength="256" class="textinput_01"  name="<?= PROFILE_FIELD_PLACEMENT_KEYWORD_EXCLUDE; ?>" value="<?= $_POST[PROFILE_FIELD_PLACEMENT_KEYWORD_EXCLUDE]; ?>" /></span>
		</div>		
	<? } ?>	
	
	
	<div class="row">
			<input type="hidden" name="<?= PROFILE_FIELD_PLACEMENT_ACTIVE; ?>" value="true" />
	</div> 	

	<div class="row">
	<h2>Additional Information</h2>
	</div>
	
	
	<div class="row pad3-b">
		<span class="label_col"><label for="requirements" class="f_label">Profile Type :</label><span class="red"> *</span></span>
		<span class="input_col">
		
		<? if ($this->Get('oCProfile')->HasProfileOption(PROFILE_VOLUNTEER) || $oAuth->oUser->isAdmin) { ?>
			General Profile <input type="radio" name="<?= PROFILE_FIELD_PLACEMENT_PROFILE_TYPE_ID; ?>" value="<?= PROFILE_VOLUNTEER  ?>" <?= ($sView == PROFILE_VOLUNTEER) ? "checked" : ""; ?>/>
		<? } ?>
		<? if ($this->Get('oCProfile')->HasProfileOption(PROFILE_TOUR) || $oAuth->oUser->isAdmin) { ?>
			Tour Profile <input type="radio" name="<?= PROFILE_FIELD_PLACEMENT_PROFILE_TYPE_ID; ?>" value="<?= PROFILE_TOUR  ?>" <?= ($sView == PROFILE_TOUR) ? "checked" : ""; ?> />
		<? } ?>	
		<? if ($this->Get('oCProfile')->HasProfileOption(PROFILE_JOB) || $oAuth->oUser->isAdmin) { ?>
			Job Profile<input type="radio" name="<?= PROFILE_FIELD_PLACEMENT_PROFILE_TYPE_ID; ?>" value="<?= PROFILE_JOB ?>" <?= ($sView == PROFILE_JOB) ? "checked" : ""; ?> />
		<? } ?>				
		</span>
	</div> 


	<div id="profile_type_<?= PROFILE_VOLUNTEER ?>" style="<?= ($sView == PROFILE_VOLUNTEER) ? "display: show;" : "display: none;"; ?>" class="row">

		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_DURATION_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_DURATION_LABEL]) > 1 ? "color:red;" : ""; ?>">Approx Duration <span class="red"> *</span></label></span>
			<span class="input_col">
				from: <?= $this->Get('DURATION_FROM'); ?>
				to: <?= $this->Get('DURATION_TO'); ?>
			</span>	
		</div>

		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_START_DATES_TXT; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_START_DATES_TXT]) > 1 ? "color:red;" : ""; ?>">Start Dates</label></span>
			<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_START_DATES_TXT; ?>" name="<?= PROFILE_FIELD_PLACEMENT_START_DATES_TXT; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_START_DATES_TXT]); ?></textarea>
			</span>	
		</div>

		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_PRICE_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_PRICE_LABEL]) > 1 ? "color:red;" : ""; ?>">Approx Costs <span class="red"> *</span></label></span>
			<span class="input_col">
				from: <?= $this->Get('PRICE_FROM'); ?>
				to: <?= $this->Get('PRICE_TO'); ?>
				<?= $this->Get('CURRENCY'); ?>
			</span>
		</div>

		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_BENEFITS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_BENEFITS]) > 1 ? "color:red;" : ""; ?>">Costs / Salary / Benefits</label></span>
			<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_BENEFITS; ?>" name="<?= PROFILE_FIELD_PLACEMENT_BENEFITS; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_BENEFITS]); ?></textarea>
			</span>	
		</div>

		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_REQUIREMENTS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_REQUIREMENTS]) > 1 ? "color:red;" : ""; ?>">Requirements</label></span>
			<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_REQUIREMENTS; ?>" name="<?= PROFILE_FIELD_PLACEMENT_REQUIREMENTS; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_REQUIREMENTS]); ?></textarea>
			</span>	
		</div>
	
	</div><!--  end profile general -->


	
	<div id="profile_type_<?= PROFILE_TOUR ?>" style="<?= ($sView == PROFILE_TOUR) ? "display: show;" : "display: none;"; ?>" class="row">
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_TOUR_DURATION_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_TOUR_DURATION_LABEL]) > 1 ? "color:red;" : ""; ?>">Approx Duration <span class="red"> *</span></label></span>
			<span class="input_col">
				from: <?= $this->Get('TOUR_DURATION_FROM'); ?>
				to: <?= $this->Get('TOUR_DURATION_TO'); ?>
			</span>	
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_START_DATES; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_START_DATES]) > 1 ? "color:red;" : ""; ?>">Start Dates</label></span>
			<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_START_DATES; ?>" name="<?= PROFILE_FIELD_PLACEMENT_START_DATES; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_START_DATES]); ?></textarea>
			</span>	
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_ITINERY; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_ITINERY]) > 1 ? "color:red;" : ""; ?>">Itinerary</label></span>
			<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_ITINERY; ?>" name="<?= PROFILE_FIELD_PLACEMENT_ITINERY; ?>" class="textarea_02" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_ITINERY]); ?></textarea>
			</span>	
		</div>

		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_TOUR_PRICE_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_TOUR_PRICE_LABEL]) > 1 ? "color:red;" : ""; ?>">Approx Costs <span class="red"> *</span></label></span>
			<span class="input_col">
				from: <?= $this->Get('TOUR_PRICE_FROM'); ?>
				to: <?= $this->Get('TOUR_PRICE_TO'); ?>
				<?= $this->Get('TOUR_CURRENCY'); ?>
			</span>
		</div>
			
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_TOUR_PRICE; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_TOUR_PRICE]) > 1 ? "red" : ""; ?>">Price / Costs - what's included / excluded?</label></span>
			<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_TOUR_PRICE; ?>" name="<?= PROFILE_FIELD_PLACEMENT_TOUR_PRICE; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_TOUR_PRICE]); ?></textarea>
			<br /><span class="p_small grey">Explain costs/pricing and whats included / excluded from the price (for example any local payments)
			</span>
		</div>	
		
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_GROUP_SIZE; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_GROUP_SIZE]) > 1 ? "red" : ""; ?>">Group Size</label></span>
			<span class="input_col">
			<?= $this->Get('GROUP_SIZE'); ?>
			</span>
		</div>
		
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_TOUR_TRAVEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_TOUR_TRAVEL]) > 1 ? "color:red;" : ""; ?>">Travel / Transport</label></span>
			<span class="input_col">
				<ul class='select_list'>
			<?
				foreach($this->Get('TRAVEL_TOUR_LIST') as $option) {
					print $option;
				} 
			?>	
				</ul>	
			</span>
		</div>
		
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_TOUR_ACCOM; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_TOUR_ACCOM]) > 1 ? "color:red;" : ""; ?>">Accomodation</label></span>
			<span class="input_col">
				<ul class='select_list'>
			<?
				foreach($this->Get('ACCOMODATION_LIST') as $option) {
					print $option;
				} 
			?>	
				</ul>	
			</span>
		</div>		
		
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_TOUR_MEALS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_TOUR_MEALS]) > 1 ? "color:red;" : ""; ?>">Meals</label></span>
			<span class="input_col">
				<ul class='select_list'>
			<?
				foreach($this->Get('MEALS_LIST') as $option) {
					print $option;
				} 
			?>	
				</ul>	
			</span>
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS]) > 1 ? "color:red;" : ""; ?>">Requirements</label></span>
			<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS; ?>" name="<?= PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_TOUR_REQUIREMENTS]); ?></textarea>
			</span>	
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_TOUR_CODE; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_TOUR_CODE]) > 1 ? "red" : ""; ?>">Tour Code</label></span>
			<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_PLACEMENT_TOUR_CODE; ?>" maxlength="30" class="textinput_01"  name="<?= PROFILE_FIELD_PLACEMENT_TOUR_CODE; ?>" value="<?= $_POST[PROFILE_FIELD_PLACEMENT_TOUR_CODE]; ?>" style="width: 130px;" /></span>
		</div>	

	</div> <!--  end profile tour -->


	<div id="profile_type_<?= PROFILE_JOB ?>" style="<?= ($sView == PROFILE_JOB) ? "display: show;" : "display: none;"; ?>" class="row">

		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_JOB_REFERENCE; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_JOB_REFERENCE]) > 1 ? "red" : ""; ?>">Reference</label></span>
			<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_PLACEMENT_JOB_REFERENCE; ?>" maxlength="30" class="textinput_01"  name="<?= PROFILE_FIELD_PLACEMENT_JOB_REFERENCE; ?>" value="<?= $_POST[PROFILE_FIELD_PLACEMENT_JOB_REFERENCE]; ?>"  style="width: 130px;" /></span>
		</div>	
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_JOB_START_DT; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_JOB_START_DT]) > 1 ? "color:red;" : ""; ?>">Job Start Date</label><span class="red"> *</span></span>
			<span class="input_col">
			<?= $this->Get('JOB_START_DATE'); ?>
			</span>
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE]) > 1 ? "color:red;" : ""; ?>">Or start dates (if multiple)</label></span>
			<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE; ?>" name="<?= PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_JOB_START_DT_MULTIPLE]); ?></textarea>
			</span>	
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_JOB_DURATION_LABEL; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_JOB_DURATION_LABEL]) > 1 ? "color:red;" : ""; ?>">Approx Duration / Length</label><span class="red"> *</span></span>
			<span class="input_col">
			from: <?= $this->Get('JOB_DURATION_FROM'); ?>
			to: <?= $this->Get('JOB_DURATION_TO'); ?>
			</span>
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_JOB_CONTRACT_TYPE; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_JOB_CONTRACT_TYPE]) > 1 ? "red" : ""; ?>">Contract Type</label></span>
			<span class="input_col">
				<?= $this->Get('JOB_CONTRACT_TYPE'); ?>
			</span>
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_JOB_OPTIONS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_JOB_OPTIONS]) > 1 ? "color:red;" : ""; ?>">Job Details</label></span>
			<span class="input_col">
				<ul class='select_list'>
			<?
				foreach($this->Get('JOB_OPTIONS') as $option) {
					print $option;
				} 
			?>	
				</ul>	
			</span>
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_JOB_SALARY; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_JOB_SALARY]) > 1 ? "red" : ""; ?>">Salary / Pay</label></span>
			<span class="input_col">
			<textarea id="<?= PROFILE_FIELD_PLACEMENT_JOB_SALARY; ?>" name="<?= PROFILE_FIELD_PLACEMENT_JOB_SALARY; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_JOB_SALARY]); ?></textarea>
			</span>
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_JOB_BENEFITS; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_JOB_BENEFITS]) > 1 ? "color:red;" : ""; ?>">Benefits</label></span>
			<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_JOB_BENEFITS; ?>" name="<?= PROFILE_FIELD_PLACEMENT_JOB_BENEFITS; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_JOB_BENEFITS]); ?></textarea></span>	
		</div>
	
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE]) > 1 ? "color:red;" : ""; ?>">Requirements / Experience</label></span>
			<span class="input_col"><textarea id="<?= PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE; ?>" name="<?= PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_PLACEMENT_JOB_EXPERIENCE]); ?></textarea></span>	
		</div>
		
		<div class="row">
			<span class="label_col"><label for="<?= PROFILE_FIELD_PLACEMENT_JOB_CLOSING_DATE; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_PLACEMENT_JOB_CLOSING_DATE]) > 1 ? "color:red;" : ""; ?>">Application Closing Date</label></span>
			<span class="input_col">
			<?= $this->Get('JOB_CLOSING_DATE'); ?>
			</span>
		</div>


	</div> <!--  end job profile -->

	<div class="row">
		<span class="label_col">&nbsp;</span>
		<span class="input_col"><input type="submit" name="submit" id="submit" value="Submit" />
		</span>
	</div>
	
	
	</div><!--  end profile inner -->
	</div><!--  end profile -->

</div>
</div>
<!-- END Page Content Container -->
