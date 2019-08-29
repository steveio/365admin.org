<?php


$response = $this->Get('VALIDATION_ERRORS');

$oProfile = $this->Get('COMPANY_PROFILE');


?>

<script type="text/javascript">
$(document).ready(function(){


   $("#<?= PROFILE_FIELD_COMP_PROFILE_TYPE_ID ?>").change(function() 
    { 
        var idx;
		var id;
		var panel_prefix = 'profile_type_';
		var panels = new Array( 'profile_type_<?= PROFILE_COMPANY; ?>',
								'profile_type_<?= PROFILE_SUMMERCAMP; ?>',
								'profile_type_<?= PROFILE_SEASONALJOBS; ?>',
								'profile_type_<?= PROFILE_VOLUNTEER_PROJECT; ?>',
								'profile_type_<?= PROFILE_TEACHING; ?>'
							); 
		
        idx = $("#<?= PROFILE_FIELD_COMP_PROFILE_TYPE_ID; ?>").val();
		id = panel_prefix+idx;
		
		for(i=0;i<panels.length;i++) {
			(panels[i] == id) ?  $("#"+panels[i]).show() : $("#"+panels[i]).hide();
		}
		
	});

	$('#country_id').change(function()
	{
		// check the corresponding country checkbox, increment selected country count 
		var cid = 'cty_'+$(this).attr('value')
		if ($('input[name='+cid+']').attr('checked') != true) {
			$('input[name='+cid+']').attr('checked','checked');
			var c = parseInt($('#cty_selected').html());
			$('#cty_selected').html(String(++c));
		}
		
		
		// display state drop down
		if ($(this).attr('value') == '71') {
			$('#state_panel').show();
			$('#region_panel').hide();
		} else {
			$('#state_panel').hide();
			$('#region_panel').show();
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

	$('#expand_species_select').click(function() {
		$('#species_select').show();
	});

	$('#collapse_species_select').click(function() {
		$('#species_select').hide();
	});

	$('#expand_habitats_select').click(function() {
		$('#habitats_select').show();
	});

	$('#collapse_habitats_select').click(function() {
		$('#habitats_select').hide();
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

	$("input[name^='<?= REFDATA_SPECIES_PREFIX; ?>']").click(function() {
		var c = parseInt($('#species_selected').html());
		if ($(this).attr('checked') == true) {
			$('#species_selected').html(String(++c));
		} else {
			$('#species_selected').html(String(--c));
		}
	});

	$("input[name^='<?= REFDATA_HABITATS_PREFIX; ?>']").click(function() {
		var c = parseInt($('#habitats_selected').html());
		if ($(this).attr('checked') == true) {
			$('#habitats_selected').html(String(++c));
		} else {
			$('#habitats_selected').html(String(--c));
		}
	});

	// profile version editor
	$('#profile_version_target').change(function(e) {
		e.preventDefault();
		
		var tid = $('#profile_version_target').val();

		toggleProfileVersion(tid);
		
		//$('#PROFILE_VERSION_MASTER').hide();

		//
		
		
		return false;
		
	});	

	var toggleProfileVersion = function(tid) {
		$('#PROFILE_VERSION_MASTER').hide();
		$('#PROFILE_VERSION_0').hide();
		$('#PROFILE_VERSION_1').hide();
		$('#PROFILE_VERSION_2').hide();
		$('#PROFILE_VERSION_3').hide();
		$('#PROFILE_VERSION_4').hide();

		$('#PROFILE_VERSION_'+tid).show();
		
	};
	
});
</script>


<div class="col five clear">


<div class="row">
<h1><?= $this->Get('COMPANY_TITLE'); ?> Details</h1>

<img src="/images/icon_info.png" alt="" border="0" style="vertical-align: middle;" />
<span class="p_small grey">Enter details about your <?= strtolower($this->Get('COMPANY_TITLE')); ?>.  
Try to add original content, don't just duplicate your website.
</span>
</div>

<? if ($oAuth->oUser->isAdmin) { ?>
<div class="row"><span class="label_col">
	<label for="" class="">Profile Version</label></span> 
	<span class="input_col">
		<select id="profile_version_target">
			<option value="MASTER" selected>default version</option>
			<option value="0">oneworld365.org</option>
			<option value="1">gapyear365.com</option>
			<option value="2">seasonaljobs365.com</option>
			<option value="3">summercamp365.com</option>
			<option value="4">tefl365.com</option>
		</select>
		<!-- <input id="profile_version_edit" type="submit" name="edit_profile_version" value="edit" /> -->
		<br /><span class="p_small grey">Create a default profile version (all sites) and optionally additional versions for specific site(s).</span>
	</span>
</div>
<?php } // end is admin ?>

<div id="PROFILE_VERSION_MASTER">

	<? if ($oAuth->oUser->isAdmin) { ?>
	<div class="row">
	<h2>Profile Version : Default (all sites)</h2>
	</div>
	<?php } // end is admin ?>

	<div class="row"><span class="label_col"><label
		for="<?= PROFILE_FIELD_COMP_TITLE; ?>"
		class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_TITLE]) > 1 ? "red" : ""; ?>"><?= $this->Get('COMPANY_TITLE'); ?> Name<span
		class="red"> *</span></label></span> <span class="input_col"><input
		type="text" id="<?= PROFILE_FIELD_COMP_TITLE; ?>" maxlength="99"
		class="textinput_01" name="<?= PROFILE_FIELD_COMP_TITLE; ?>"
		value="<?= $_POST[PROFILE_FIELD_COMP_TITLE]; ?>" /></span></div>
	
	<div class="row"><span class="label_col"><label
		for="<?= PROFILE_FIELD_COMP_DESC_SHORT; ?>"
		class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_DESC_SHORT]) > 1 ? "red" : ""; ?>">Short
	Description<span class="red"> *</span><br />
	(300 chars or less)</label></span> <span class="input_col"><textarea
		id="<?= PROFILE_FIELD_COMP_DESC_SHORT; ?>"
		name="<?= PROFILE_FIELD_COMP_DESC_SHORT; ?>" class="textarea_01" /><?= stripslashes($_POST[PROFILE_FIELD_COMP_DESC_SHORT]); ?></textarea>
		<br /><span class="p_small grey"></span>
		</span>
		
	</div>
	
	<div class="row"><span class="label_col">
	<?php 
	$full_desc_label = 'Full Description';
	if (strlen($this->Get('FULL_DESC_LABEL')) > 1) $full_desc_label = $this->Get('FULL_DESC_LABEL'); 
	?>
	<label for="<?= PROFILE_FIELD_COMP_DESC_LONG; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_DESC_LONG]) > 1 ? "red" : ""; ?>"><?= $full_desc_label; ?><span class="red"> *</span></label></span> <span
		class="input_col"><textarea id="<?= PROFILE_FIELD_COMP_DESC_LONG; ?>"
		name="<?= PROFILE_FIELD_COMP_DESC_LONG; ?>" class="textarea_02" /><?= stripslashes($_POST[PROFILE_FIELD_COMP_DESC_LONG]); ?></textarea>
		<br />
		</span>
	</div>

</div>

<? if ($oAuth->oUser->isAdmin) { ?>
<?php 
foreach($_CONFIG['aProfileVersion'] as $tid => $sVersionTarget) {
?>
<div id="PROFILE_VERSION_<?= $tid; ?>" style="display: none;">
<?php $pv_prefix = "PV::".$tid."::"; ?>

	<div class="row">
	<h2>Profile Version: <?= $sVersionTarget; ?></h2>
	</div>

	<div class="row">
		<span class="label_col">
			<label for="<?= $pv_prefix.PROFILE_FIELD_COMP_TITLE; ?>" class="<?= strlen($response['msg'][$pv_prefix.PROFILE_FIELD_COMP_TITLE]) > 1 ? "red" : ""; ?>">Title </label>
		</span> 
		<span class="input_col">
			<input type="text" id="<?= $pv_prefix.PROFILE_FIELD_COMP_TITLE; ?>" maxlength="99" class="textinput_01" name="<?= $pv_prefix.PROFILE_FIELD_COMP_TITLE; ?>" value="<?= $_POST[$pv_prefix.PROFILE_FIELD_COMP_TITLE]; ?>" />
		</span>
	</div>
	
	<div class="row">
		<span class="label_col">
			<label for="<?= $pv_prefix.PROFILE_FIELD_COMP_DESC_SHORT; ?>" class="<?= strlen($response['msg'][$pv_prefix.PROFILE_FIELD_COMP_DESC_SHORT]) > 1 ? "red" : ""; ?>">Short Description<br />	(300 chars or less)</label>
		</span> 
		<span class="input_col">
		<textarea id="<?= $pv_prefix.PROFILE_FIELD_COMP_DESC_SHORT; ?>" name="<?= $pv_prefix.PROFILE_FIELD_COMP_DESC_SHORT; ?>" class="textarea_01" /><?= stripslashes($_POST[$pv_prefix.PROFILE_FIELD_COMP_DESC_SHORT]); ?></textarea>
		</span>
	</div>
	
	<div class="row">
		<span class="label_col">
			<label for="<?= $pv_prefix.PROFILE_FIELD_COMP_DESC_LONG; ?>" class="<?= strlen($response['msg'][$pv_prefix.PROFILE_FIELD_COMP_DESC_LONG]) > 1 ? "red" : ""; ?>">Full Description</label>
		</span> 
		<span class="input_col">
			<textarea id="<?= $pv_prefix.PROFILE_FIELD_COMP_DESC_LONG; ?>" name="<?= $pv_prefix.PROFILE_FIELD_COMP_DESC_LONG; ?>" class="textarea_02" /><?= stripslashes($_POST[$pv_prefix.PROFILE_FIELD_COMP_DESC_LONG]); ?></textarea>
		</span>
	</div>
</div>
<?php } // end profile versions?>
<?php } // end is admin ?>


<?php if ($this->Get('DISPLAY_CAT_ACT_CTY_OPTIONS')) {  ?>

<div class="left-align five clear pad4-b"><span class="label_col">
<h1 style="margin: 0; <?= strlen($response['msg']['category']) > 1 ? "color:red;" : ""; ?>">Categories<span class="red"> *</span></h1></span> 
<span class="input_col" style=""> 
<a id="expand_category_select">+ Expand</a> 
<a id="collapse_category_select">- Collapse</a> (<span id="cat_selected"><?= $this->Get('CATEGORY_LIST_SELECTED_COUNT'); ?></span> Selected)</a> </span> 
<span id="category_select" class="input_col" style="display: none;">
<div class="left-align four">
<ul class='select_list'>
<?= $this->Get('CATEGORY_LIST'); ?>
</ul>
</div>
</span></div>


<div class="left-align five clear pad4-b"><span class="label_col">
<h1 style="margin: 0; <?= strlen($response['msg']['activity']) > 1 ? "color:red;" : ""; ?>">Activities<span
	class="red"> *</span></h1>
</span> <span class="input_col" style=""> <a id="expand_activity_select">+
Expand</a> <a id="collapse_activity_select">- Collapse</a> (<span
	id="act_selected"><?= $this->Get('ACTIVITY_LIST_SELECTED_COUNT'); ?></span>
Selected) </span> <span id="activity_select" class="input_col"
	style="display: none;"> <?
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
</span></div>

<div class="left-align five clear pad4-b"><span class="label_col">
<h1 style="margin: 0; <?= strlen($response['msg']['country']) > 1 ? "color:red;" : ""; ?>">Countries<span
	class="red"> *</span></h1>
<span class="p_small grey">Where are your placements located?</span> </span>
<span class="input_col" style=""> <a id="expand_country_select">+ Expand</a>
<a id="collapse_country_select">- Collapse</a> (<span id="cty_selected"><?= $this->Get('COUNTRY_LIST_SELECTED_COUNT'); ?></span>
Selected) </span> <span id="country_select" class="input_col"
	style="display: none;"> <?
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

</span></div>

<?php } // end IF DISPLAY_CAT_ACT_CTY_OPTIONS ?>

<div class="row"><span class="label_col"><label
	for="<?= PROFILE_FIELD_COMP_URL; ?>"
	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_URL]) > 1 ? "red" : ""; ?>">Website
Url <span class="red">*</span></label></span> <span class="input_col"><input
	type="text" id="<?= PROFILE_FIELD_COMP_URL; ?>"
	name="<?= PROFILE_FIELD_COMP_URL; ?>" class="textinput_01"
	maxlength="255"
	value="<?= (strlen($_POST[PROFILE_FIELD_COMP_URL]) > 1) ? $_POST[PROFILE_FIELD_COMP_URL] : "http://www."; ?>" /></span>
</div>

<div class="row"><span class="label_col"><label
	for="<?= PROFILE_FIELD_COMP_EMAIL; ?>"
	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_EMAIL]) > 1 ? "red" : ""; ?>">Enquiry
Email <span class="red">*</span></label></span> <span class="input_col"><input
	type="text" id="<?= PROFILE_FIELD_COMP_EMAIL; ?>" class="textinput_01"
	maxlength="60" name="<?= PROFILE_FIELD_COMP_EMAIL; ?>"
	value="<?= $_POST[PROFILE_FIELD_COMP_EMAIL]; ?>" /></span></div>

<div class="row"><span class="label_col"><label
	for="<?= PROFILE_FIELD_COMP_APPLY_URL; ?>"
	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_APPLY_URL]) > 1 ? "red" : ""; ?>">Apply
Url</label></span> <span class="input_col"><input type="text"
	id="<?= PROFILE_FIELD_COMP_APPLY_URL; ?>" class="textinput_01"
	maxlength="255" name="<?= PROFILE_FIELD_COMP_APPLY_URL; ?>"
	value="<?= $_POST[PROFILE_FIELD_COMP_APPLY_URL]; ?>" /> <br />
<span class="p_small grey">(optional) supply a url if you want applicants sent directly to your booking/recruitment website</span> </span></div>

<div class="row"><span class="label_col"><label
	for="<?= PROFILE_FIELD_COMP_ADDRESS; ?>"
	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_ADDRESS]) > 1 ? "red" : ""; ?>">Address</label></span>
<span class="input_col"><input type="text"
	id="<?= PROFILE_FIELD_COMP_ADDRESS; ?>" class="textinput_01"
	maxlength="999" name="<?= PROFILE_FIELD_COMP_ADDRESS; ?>"
	value="<?= $_POST[PROFILE_FIELD_COMP_ADDRESS]; ?>" /></span></div>

<div class="row"><span class="label_col"><label
	for="<?= PROFILE_FIELD_COMP_COUNTRY_ID; ?>"
	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_COUNTRY_ID]) > 1 ? "red" : ""; ?>">Country</label></span>
<span class="input_col"> <?= $this->Get("COUNTRY_ID_LIST"); ?> </span></div>

<? $css = ($this->Get('COUNTRY_ID_SELECTED') != 71) ? "display: none;" : ""; ?>
<div id="state_panel" class="row" style="<?= $css; ?>"><span
	class="label_col"><label for="<?= PROFILE_FIELD_COMP_STATE_ID; ?>"
	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_STATE_ID]) > 1 ? "red" : ""; ?>">State</label></span>
<span class="input_col"> <?= $this->Get("US_STATE_LIST"); ?> </span></div>

<? $css = ($this->Get('COUNTRY_ID_SELECTED') == 71) ? "display: none;" : ""; ?>
<div id="region_panel" class="row"  style="<?= $css; ?>"><span
	class="label_col"><label for="<?= PROFILE_FIELD_COMP_LOCATION; ?>"
	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_LOCATION]) > 1 ? "red" : ""; ?>">Region</label></span>
<span class="input_col"><input type="text"
	id="<?= PROFILE_FIELD_COMP_LOCATION; ?>" class="textinput_01"
	maxlength="99" name="<?= PROFILE_FIELD_COMP_LOCATION; ?>"
	value="<?= $_POST[PROFILE_FIELD_COMP_LOCATION]; ?>" /></span></div>

<div class="row pad3-b"><span class="label_col"><label
	for="<?= PROFILE_FIELD_COMP_TELEPHONE; ?>"
	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_TELEPHONE]) > 1 ? "red" : ""; ?>">Telephone</label></span>
<span class="input_col"><input type="text"
	id="<?= PROFILE_FIELD_COMP_TELEPHONE; ?>" class="textinput_01"
	maxlength="39" name="<?= PROFILE_FIELD_COMP_TELEPHONE; ?>"
	value="<?= $_POST[PROFILE_FIELD_COMP_TELEPHONE]; ?>" /> <br />
<span class="p_small grey">Include international / regional dialing
code(s)</span> </span></div>

<?php if ($this->Get("PROFILE_TYPE_COUNT") >  1) { ?>
<div class="row pad4-t pad3-b">
<h2>Profile Type</h2>
</div>

<div class="row"><span class="label_col"><label for="<?= PROFILE_FIELD_COMP_PROFILE_TYPE_ID; ?>" style="<?= strlen($response['msg'][PROFILE_FIELD_COMP_PROFILE_TYPE_ID]) > 1 ? "color:red;" : ""; ?>">Profile
Type<span class="red"> *</span></label></span> <span class="input_col">
<?= $this->Get("PROFILE_TYPE_LIST"); ?> <br />
<span class="p_small grey"> Choose profile type that best matches your
organisation's activities. <br />
If your activities span multiple profile types choose 'Organisation
Profile - General'. </span> </span></div>
<?php } else { ?> <input type="hidden"
	name="<?= PROFILE_FIELD_COMP_PROFILE_TYPE_ID; ?>"
	value="<?= $this->Get("PROFILE_TYPE_SELECTED_ID"); ?>" /> <?php } ?> <?php 
	$panel_key = 'profile_type_'.PROFILE_COMPANY;
	$visibility = ($this->Get('PROFILE_ACTIVE_PANEL') == $panel_key) ? "" : "display: none;";
	?>
<div id="<?= $panel_key; ?>" class="row pad3-t" style="<?= $visibility; ?>">
	<?= $this->Get('EXTENDED_FIELDSET_GENERAL_PROFILE'); ?></div>

	<?php
	$panel_key = 'profile_type_'.PROFILE_SUMMERCAMP;
	$visibility = ($this->Get('PROFILE_ACTIVE_PANEL') == $panel_key) ? "" : "display: none;";
	?>
<div id="profile_type_<?= PROFILE_SUMMERCAMP; ?>" class="row pad3-t" style="<?= $visibility; ?>">
	<?= $this->Get('EXTENDED_FIELDSET_SUMMERCAMP'); ?></div>

	<?php
	$panel_key = 'profile_type_'.PROFILE_SEASONALJOBS;
	$visibility = ($this->Get('PROFILE_ACTIVE_PANEL') == $panel_key) ? "" : "display: none;";
	?>
<div id="profile_type_<?= PROFILE_SEASONALJOBS; ?>" class="row pad3-t" style="<?= $visibility; ?>">
	<?= $this->Get('EXTENDED_FIELDSET_SEASONALJOBS'); ?></div>

	<?php
	$panel_key = 'profile_type_'.PROFILE_VOLUNTEER_PROJECT;
	$visibility = ($this->Get('PROFILE_ACTIVE_PANEL') == $panel_key) ? "" : "display: none;";
	?>
<div id="profile_type_<?= PROFILE_VOLUNTEER_PROJECT; ?>" class="row pad3-t" style="<?= $visibility; ?>">
	<?= $this->Get('EXTENDED_FIELDSET_VOLUNTEER_PROJECT'); ?></div>

	<?php
	$panel_key = 'profile_type_'.PROFILE_TEACHING;
	$visibility = ($this->Get('PROFILE_ACTIVE_PANEL') == $panel_key) ? "" : "display: none;";
	?>
<div id="profile_type_<?= PROFILE_TEACHING; ?>" class="row pad3-t" style="<?= $visibility; ?>">
	<?= $this->Get('EXTENDED_FIELDSET_TEACHING_PROJECT'); ?></div>






<? if ($oAuth->oUser->isAdmin) { ?>

<div class="row">
<h2>Admin Options</h2>
</div>


<div class="row"><span class="label_col"><label
	for="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>">Listing Level</label></span>
<span class="input_col"> <?php
$checked = (($oProfile->GetListingType() == FREE_LISTING) || ($_POST[PROFILE_FIELD_COMP_PROD_TYPE] == FREE_LISTING)) ? "checked" : "";
?> Free<input type="radio"
	id="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>_<?= FREE_LISTING ?>"
	name="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>" value="<?= FREE_LISTING ?>"
	<?= $checked; ?>> <?php
	$checked = (($oProfile->GetListingType() == BASIC_LISTING) || ($_POST[PROFILE_FIELD_COMP_PROD_TYPE] == BASIC_LISTING)) ? "checked" : "";
	?> Basic<input type="radio"
	id="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>_<?= BASIC_LISTING ?>"
	name="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>"
	value="<?= BASIC_LISTING ?>" <?= $checked; ?>> <?php
	$checked = (($oProfile->GetListingType() == ENHANCED_LISTING) || ($_POST[PROFILE_FIELD_COMP_PROD_TYPE] == ENHANCED_LISTING)) ? "checked" : "";
	?> Enhanced<input type="radio"
	id="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>_<?= ENHANCED_LISTING ?>"
	name="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>"
	value="<?= ENHANCED_LISTING ?>" <?= $checked; ?>> <?php
	$checked = (($oProfile->GetListingType() == SPONSORED_LISTING) || ($_POST[PROFILE_FIELD_COMP_PROD_TYPE] == SPONSORED_LISTING)) ? "checked" : "";
	?> Sponsored<input type="radio"
	id="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>_<?= SPONSORED_LISTING ?>"
	name="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>"
	value="<?= SPONSORED_LISTING ?>" <?= $checked; ?>> </span></div>


<div class="row"><span class="label_col"> <label
	for="<?= PROFILE_FIELD_COMP_LISTING_TYPE; ?>"
	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_LISTING_TYPE]) > 1 ? "red" : ""; ?>">Listing
Type :</label><span class="red"> *</span> </span> <span
	class="input_col"> <?

	$aListingOption = $this->Get('ADMIN_LISTING_OPTIONS');
	$oListing = $this->Get('ADMIN_CURRENT_LISTING_OBJECT'); // null if no existing listing record exists

	/* default listings start date */
	if (is_object($oListing)) {
		$aDate = explode("-",$oListing->GetStartDate());
		$_REQUEST['ListingMonth'] = $aDate[1];
		$_REQUEST['ListingYear'] = $aDate[2];
	}

	?> <select id="<?= PROFILE_FIELD_COMP_LISTING_TYPE; ?>"
	name="<?= PROFILE_FIELD_COMP_LISTING_TYPE; ?>">
	<option value="null"></option>
	<?
	foreach($aListingOption as $key => $value) {
		if (isset($_POST[PROFILE_FIELD_COMP_LISTING_TYPE])) {
			$selected = ($_POST[PROFILE_FIELD_COMP_LISTING_TYPE] == $key) ? "selected" : "";
		}  elseif (is_object($oListing)) {
			$selected = ($oListing->GetCode() == $key) ? "selected" : "";
		} else { // default to FREE listing, start date now
			$selected = ($key == "FREE") ? "selected" : "";
			$_REQUEST['ListingMonth'] = date("m");
			$_REQUEST['ListingYear'] = date("Y");
		}
		//$label = "&pound;".$value['price'];
		if ($key == "FREE") $label = "";
		?>
	<option value="<?= $key ?>" <?= $selected ?>><?= $value['label'] ?> <?= $label ?></option>
	<? } ?>
</select> </span></div>

<div class="row"><span class="label_col"><label
	for="<?= PROFILE_FIELD_COMP_LISTING_START_DATE ?>"
	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_LISTING_START_DATE]) > 1 ? "red" : ""; ?>">Listing
Start :</label><span class="red"> *</span></span> <span
	class="input_col"><? print Date::GetDateInput('Listing',false,true,true,$iYFrom = 1, $iTo = 5); ?></span>
</div>

	<?
	if (!is_numeric($oProfile->GetProfileQuota())) {
		switch($oProfile->GetProfileQuota()) {
			case FREE_LISTING :
				$oProfile->SetProfileQuota(FREE_PQUOTA);
				break;
			case BASIC_LISTING :
				$oProfile->SetProfileQuota(BASIC_PQUOTA);
				break;
			case ENHANCED_LISTING :
				$oProfile->SetProfileQuota(ENHANCED_PQUOTA);
				break;
			case SPONSORED_LISTING :
				$oProfile->SetProfileQuota(SPONSORED_PQUOTA);
				break;
			default :
				$oProfile->SetProfileQuota(FREE_PQUOTA);
		}
	}
	?>
<div class="row"><span class="label_col"><label>Placement Quota:</label></span>
	<?php
	if (isset($_POST['submit'])) {
		$profile_quota = $_POST[PROFILE_FIELD_COMP_PROFILE_QUOTA];
	} else {
		$profile_quota = $oProfile->GetProfileQuota();
	}
	?> <span class="input_col"><input type="text"
	id="<?= PROFILE_FIELD_COMP_PROFILE_QUOTA; ?>"
	name="<?= PROFILE_FIELD_COMP_PROFILE_QUOTA; ?>" class="text_input"
	style="width: 30px;" maxlength="3" value="<?= $profile_quota; ?>" /></span>
</div>

<div class="row"><span class="label_col"><label style="<?= strlen($response['msg']['prof_opt_1']) > 1 ? "color:red;" : ""; ?>">Profile
Options :</label></span> <span class="input_col"> General<input
	type="checkbox" name="prof_opt_1"
	<? if (($oProfile->HasProfileOption(PROFILE_VOLUNTEER)) || isset($_POST['prof_opt_1'])) print "checked"; ?>>
Tour<input type="checkbox" name="prof_opt_2"
<? if (($oProfile->HasProfileOption(PROFILE_TOUR)) || isset($_POST['prof_opt_2'])) print "checked"; ?>>
Job<input type="checkbox" name="prof_opt_3"
<? if (($oProfile->HasProfileOption(PROFILE_JOB)) || isset($_POST['prof_opt_3'])) print "checked"; ?>>
</span></div>

<div class="row"><span class="label_col"><label style="<?= strlen($response['msg']['profile_filter_from_search']) > 1 ? "color:red;" : ""; ?>">Profile Filter from Search :</label></span> <span class="input_col">
	<?php
	if (isset($_POST['submit'])) {
		$profile_filter = $_POST['profile_filter_from_search'];
	} else {
		$profile_filter = $oProfile->GetProfileFilterFromSearch();
	}
	?>
<input type="checkbox" name="profile_filter_from_search"
<? if ($profile_filter == 't') print "checked"; ?>>
</span></div>

<div class="row"><span class="label_col"><label>Enquiry Options :</label></span>
<span class="input_col"> General<input type="checkbox" name="enq_opt_1"
<? if (($oProfile->HasEnquiryOption(ENQUIRY_GENERAL)) || isset($_POST['enq_opt_1'])) print "checked"; ?>>
Booking<input type="checkbox" name="enq_opt_2"
<? if (($oProfile->HasEnquiryOption(ENQUIRY_BOOKING)) || isset($_POST['enq_opt_2'])) print "checked"; ?>>
Job App<input type="checkbox" name="enq_opt_3"
<? if (($oProfile->HasEnquiryOption(ENQUIRY_JOB_APP)) || isset($_POST['enq_opt_3'])) print "checked"; ?>>
</span></div>

<div class="row"><span class="label_col"><label>Homepage on:</label></span>
<span class="input_col"> <?= $this->Get('ADMIN_WEBSITE_HOMEPAGE_OPTIONS'); ?>
</span></div>


<div class="row"><span class="label_col"><label><b>Approved?</b> :<span
	class="red"> *</span></label></span> <span class="input_col"> <? 
	if (isset($_POST['submit'])) {
		$checked = ($_POST['status'] == "true") ? "checked" : "";
	} else {
		$checked = ($oProfile->GetStatus() == 1) ? "checked" : "";
	}
	?> <input id="status" type="checkbox" name="status" value="true"
	<?= $checked; ?> /> </span></div>

<? } // end admin options ?>
<div class="row"><span class="label_col">&nbsp;</span> <span
	class="input_col"><input type="submit" name="submit" id="submit"
	value="Submit" /> </span></div>


</div>
