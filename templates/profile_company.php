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
		var panels = new Array(
                'profile_type_<?= PROFILE_COMPANY; ?>',
								'profile_type_<?= PROFILE_SUMMERCAMP; ?>',
								'profile_type_<?= PROFILE_SEASONALJOBS; ?>',
								'profile_type_<?= PROFILE_VOLUNTEER_PROJECT; ?>',
								'profile_type_<?= PROFILE_TEACHING; ?>'
							);

    idx = $("#<?= PROFILE_FIELD_COMP_PROFILE_TYPE_ID; ?>").val();
    id = panel_prefix+idx;

		for(i=0;i<panels.length;i++)
    {
      (panels[i] == id) ? $("#"+panels[i]).show() : $("#"+panels[i]).hide();
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



<div class="container">
<div class="align-items-center justify-content-center">
<div class="row">

<h1><?= $this->Get('COMPANY_TITLE'); ?></h1>


<div class="row my-2">
  <div class="col">
  <span><img src="/images/icon_info.png" alt="" border="0" style="vertical-align: middle;" /></span>
  <span class="p_small grey">Enter details about your <?= strtolower($this->Get('COMPANY_TITLE')); ?>.
  Add original content, don't duplicate your website.
  </span>
  </div>
</div>

<? if ($oAuth->oUser->isAdmin) { ?>
<div class="row">
	<input type="hidden" id="profile_version_target" name="profile_version_target" value="MASTER" / />
</div>
<?php } // end is admin ?>

<div id="PROFILE_VERSION_MASTER">

    <div class="row formgroup my-2">
        <span class="label_col">
          <label for="<?= PROFILE_FIELD_COMP_TITLE; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_TITLE]) > 1 ? "red" : ""; ?>"><?= $this->Get('COMPANY_TITLE'); ?> Name
        <span class="red"> *</span></label></span>
        <span class="input_col">
          <input class="form-control" type="text" id="<?= PROFILE_FIELD_COMP_TITLE; ?>" maxlength="99" class="textinput_01" name="<?= PROFILE_FIELD_COMP_TITLE; ?>" value="<?= $_POST[PROFILE_FIELD_COMP_TITLE]; ?>" />
        </span>
    </div>

	<div class="row formgroup my-2">
		<span class="label_col"><label for="<?= PROFILE_FIELD_COMP_DESC_SHORT; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_DESC_SHORT]) > 1 ? "red" : ""; ?>">Short Description<span class="red"> *</span><br />(30 chars or less)</label></span> 
		<span class="input_col"><textarea id="<?= PROFILE_FIELD_COMP_DESC_SHORT; ?>" name="<?= PROFILE_FIELD_COMP_DESC_SHORT; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_COMP_DESC_SHORT]); ?></textarea><br /><span class="p_small grey"></span>
		</span>
	</div>

	<div class="row formgroup my-2"><span class="label_col">
	<?php
	$full_desc_label = 'Full Description';
	if (strlen($this->Get('FULL_DESC_LABEL')) > 1) $full_desc_label = $this->Get('FULL_DESC_LABEL');
	?>
	<label for="<?= PROFILE_FIELD_COMP_DESC_LONG; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_DESC_LONG]) > 1 ? "red" : ""; ?>"><?= $full_desc_label; ?><span class="red"> *</span></label></span> <span
		class="input_col"><textarea id="<?= PROFILE_FIELD_COMP_DESC_LONG; ?>"
		name="<?= PROFILE_FIELD_COMP_DESC_LONG; ?>" class="form-control" /><?= stripslashes($_POST[PROFILE_FIELD_COMP_DESC_LONG]); ?></textarea>
	</span>
	</div>

</div>


<?php if ($this->Get('DISPLAY_CAT_ACT_CTY_OPTIONS')) {  
	
    // Category, Activity, Country metadata common to all profile types
    require_once("profile_metadata_select.php");


} // end IF DISPLAY_CAT_ACT_CTY_OPTIONS ?>


<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COMP_URL; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_URL]) > 1 ? "red" : ""; ?>">Website Url <span class="red">*</span></label></span> 
	<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_COMP_URL; ?>" name="<?= PROFILE_FIELD_COMP_URL; ?>" class="form-control" maxlength="255" value="<?= (strlen($_POST[PROFILE_FIELD_COMP_URL]) > 1) ? $_POST[PROFILE_FIELD_COMP_URL] : "http://www."; ?>" /></span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COMP_EMAIL; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_EMAIL]) > 1 ? "red" : ""; ?>">Enquiry Email <span class="red">*</span></label></span> 
	<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_COMP_EMAIL; ?>" class="form-control" maxlength="60" name="<?= PROFILE_FIELD_COMP_EMAIL; ?>" value="<?= $_POST[PROFILE_FIELD_COMP_EMAIL]; ?>" /></span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COMP_APPLY_URL; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_APPLY_URL]) > 1 ? "red" : ""; ?>">Apply Url</label></span> 
	<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_COMP_APPLY_URL; ?>" class="form-control" maxlength="255" name="<?= PROFILE_FIELD_COMP_APPLY_URL; ?>" value="<?= $_POST[PROFILE_FIELD_COMP_APPLY_URL]; ?>" /> <br />
	<span class="p_small grey">(optional) supply a url if you want applicants sent directly to your booking/recruitment website</span> </span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COMP_ADDRESS; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_ADDRESS]) > 1 ? "red" : ""; ?>">Address</label></span>
	<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_COMP_ADDRESS; ?>" class="form-control" maxlength="999" name="<?= PROFILE_FIELD_COMP_ADDRESS; ?>" value="<?= $_POST[PROFILE_FIELD_COMP_ADDRESS]; ?>" /></span>
</div>

<div class="row formgroup my-2">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COMP_COUNTRY_ID; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_COUNTRY_ID]) > 1 ? "red" : ""; ?>">Country</label></span>
	<span class="input_col"> <?= $this->Get("COUNTRY_ID_LIST"); ?> </span>
</div>



<? 
$css_mandatory = "";
if ($oProfile->GetProfileType() == PROFILE_SUMMERCAMP)
{
    $css_mandatory = "<span class=\"red\">*</span>";
}
$css = ($this->Get('COUNTRY_ID_SELECTED') != 71) ? "display: none;" : ""; 
?>
<div id="state_panel" class="row formgroup my-2" style="<?= $css; ?>">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COMP_STATE_ID; ?>" class="<?= (strlen($response['msg'][PROFILE_FIELD_COMP_STATE_ID]) > 1) ? "red" : ""; ?>">State <?= $css_mandatory; ?></label></span>
	<span class="input_col"> <?= $this->Get("US_STATE_LIST"); ?> </span>
</div>

<? $css = ($this->Get('COUNTRY_ID_SELECTED') == 71) ? "display: none;" : ""; ?>
<div id="region_panel" class="row formgroup my-2"  style="<?= $css; ?>">
	<span class="label_col"><label for="<?= PROFILE_FIELD_COMP_LOCATION; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_LOCATION]) > 1 ? "red" : ""; ?>">Region</label></span>
	<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_COMP_LOCATION; ?>" class="form-control" maxlength="99" name="<?= PROFILE_FIELD_COMP_LOCATION; ?>" value="<?= $_POST[PROFILE_FIELD_COMP_LOCATION]; ?>" /></span>
</div>

<div class="row formgroup my-2"><span class="label_col">
	<label for="<?= PROFILE_FIELD_COMP_TELEPHONE; ?>" class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_TELEPHONE]) > 1 ? "red" : ""; ?>">Telephone</label></span>
	<span class="input_col"><input type="text" id="<?= PROFILE_FIELD_COMP_TELEPHONE; ?>" class="form-control" maxlength="39" name="<?= PROFILE_FIELD_COMP_TELEPHONE; ?>" value="<?= $_POST[PROFILE_FIELD_COMP_TELEPHONE]; ?>" /> <br />
	<span class="p_small grey">Include international / regional dialing code(s)</span> </span>
</div>

<?php if ($this->Get("PROFILE_TYPE_COUNT") >  1) { ?>
<div class="row formgroup my-2">
  <h2>Profile Type <span class="red"> *</span></h2>
</div>

<div class="row my-2">
  <div class="col">
  <span><img src="/images/icon_info.png" alt="" border="0" style="vertical-align: middle;" /></span>
  <span class="p_small grey"> Choose profile type that best matches your organisation's activities.</span>
  </div>
</div>
<div class="row my-2">
<span class="input_col">
  <?= $this->Get("PROFILE_TYPE_LIST"); ?> <br />
</span>
</div>

<?php } else { ?>
  <input type="hidden" name="<?= PROFILE_FIELD_COMP_PROFILE_TYPE_ID; ?>" value="<?= $this->Get("PROFILE_TYPE_SELECTED_ID"); ?>" />
<?php } ?>

<?php
$panel_key = 'profile_type_'.PROFILE_COMPANY;
$visibility = ($this->Get('PROFILE_ACTIVE_PANEL') == $panel_key) ? "" : "display: none;";
?>
<div id="<?= $panel_key; ?>" class="row  formgroup my-2" style="<?= $visibility; ?>">
  <?= $this->Get('EXTENDED_FIELDSET_GENERAL_PROFILE'); ?>
</div>

<?php
$panel_key = 'profile_type_'.PROFILE_SUMMERCAMP;
$visibility = ($this->Get('PROFILE_ACTIVE_PANEL') == $panel_key) ? "" : "display: none;";
?>
<div id="profile_type_<?= PROFILE_SUMMERCAMP; ?>" class="row formgroup my-2" style="<?= $visibility; ?>">
  <?= $this->Get('EXTENDED_FIELDSET_SUMMERCAMP'); ?>
</div>

<?php
$panel_key = 'profile_type_'.PROFILE_SEASONALJOBS;
$visibility = ($this->Get('PROFILE_ACTIVE_PANEL') == $panel_key) ? "" : "display: none;";
?>
<div id="profile_type_<?= PROFILE_SEASONALJOBS; ?>" class="row formgroup my-2" style="<?= $visibility; ?>">
  <?=  $this->Get('EXTENDED_FIELDSET_SEASONALJOBS'); ?>
</div>

<?php
$panel_key = 'profile_type_'.PROFILE_VOLUNTEER_PROJECT;
$visibility = ($this->Get('PROFILE_ACTIVE_PANEL') == $panel_key) ? "" : "display: none;";
?>
<div id="profile_type_<?= PROFILE_VOLUNTEER_PROJECT; ?>" class="row formgroup my-2" style="<?= $visibility; ?>">
  <?= $this->Get('EXTENDED_FIELDSET_VOLUNTEER_PROJECT'); ?>
</div>

<?php
$panel_key = 'profile_type_'.PROFILE_TEACHING;
$visibility = ($this->Get('PROFILE_ACTIVE_PANEL') == $panel_key) ? "" : "display: none;";
?>
<div id="profile_type_<?= PROFILE_TEACHING; ?>" class="row formgroup my-2" style="<?= $visibility; ?>">
  <?= $this->Get('EXTENDED_FIELDSET_TEACHING_PROJECT'); ?>
</div>


<? if ($oAuth->oUser->isAdmin) { ?>

<div class="row formgroup my-2">
  <h2>Admin Options</h2>
</div>


<div class="row formgroup my-2">
  <div class="col-3">
    <span class="label_col"><label
    	for="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>">Listing Level</label></span>
  </div>
  <div class="col-9 form-group">
    <span class=""> <?php
    $checked = (($oProfile->GetListingType() == FREE_LISTING) || ($_POST[PROFILE_FIELD_COMP_PROD_TYPE] == FREE_LISTING)) ? "checked" : "";
  ?><input type="radio"
  	id="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>_<?= FREE_LISTING ?>"
  	name="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>" value="<?= FREE_LISTING ?>" class="form-check-input"
  	<?= $checked; ?>> Free <?php
  	$checked = (($oProfile->GetListingType() == BASIC_LISTING) || ($_POST[PROFILE_FIELD_COMP_PROD_TYPE] == BASIC_LISTING)) ? "checked" : "";
  	?>
    <input type="radio"
  	id="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>_<?= BASIC_LISTING ?>"
  	name="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>"  class="form-check-input"
    value="<?= BASIC_LISTING ?>" <?= $checked; ?>> Basic  <?php
  	$checked = (($oProfile->GetListingType() == ENHANCED_LISTING) || ($_POST[PROFILE_FIELD_COMP_PROD_TYPE] == ENHANCED_LISTING)) ? "checked" : "";
  	?>
    <input type="radio"
  	id="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>_<?= ENHANCED_LISTING ?>"
  	name="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>"  class="form-check-input"
  	value="<?= ENHANCED_LISTING ?>" <?= $checked; ?>> Enhanced  <?php
  	$checked = (($oProfile->GetListingType() == SPONSORED_LISTING) || ($_POST[PROFILE_FIELD_COMP_PROD_TYPE] == SPONSORED_LISTING)) ? "checked" : "";
  	?>
    <input type="radio"
  	id="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>_<?= SPONSORED_LISTING ?>"
    class="form-check-input"
  	name="<?= PROFILE_FIELD_COMP_PROD_TYPE; ?>"
  	value="<?= SPONSORED_LISTING ?>" <?= $checked; ?>> Sponsored </span>
  </div>
</div>

<div class="row formgroup my-2">
  <div class="col-3">
    <span class="label_col"> <label
    	for="<?= PROFILE_FIELD_COMP_LISTING_TYPE; ?>"
    	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_LISTING_TYPE]) > 1 ? "red" : ""; ?>">Listing Type :</label><span class="red"> *</span> </span>
  </div>
  <div class="col-9 form-group">
    <span class="input_col"> <?

    	$aListingOption = $this->Get('ADMIN_LISTING_OPTIONS');
    	$oListing = $this->Get('ADMIN_CURRENT_LISTING_OBJECT'); // null if no existing listing record exists

    	/* default listings start date */
    	if (is_object($oListing)) {
    		$aDate = explode("-",$oListing->GetStartDate());
    		$_REQUEST['ListingMonth'] = $aDate[1];
    		$_REQUEST['ListingYear'] = $aDate[2];
    	}

    	?> <select id="<?= PROFILE_FIELD_COMP_LISTING_TYPE; ?>"
    	name="<?= PROFILE_FIELD_COMP_LISTING_TYPE; ?>" class="form-select">
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
    </select> </span>
  </div>
</div>

<div class="row formgroup my-2">
  <div class="col-3">
    <span class="label_col"><label
    	for="<?= PROFILE_FIELD_COMP_LISTING_START_DATE ?>"
    	class="<?= strlen($response['msg'][PROFILE_FIELD_COMP_LISTING_START_DATE]) > 1 ? "red" : ""; ?>">Listing
    Start :</label><span class="red"> *</span></span>
  </div>
  <div class="col-9 form-group">
    <span class="input_col"><? print Date::GetDateInput('Listing',false,true,true,$iYFrom = 1, $iTo = 5); ?></span>
  </div>
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
<div class="row formgroup my-2">
  <div class="col-3">
    <span class="label_col"><label>Placement Quota:</label></span>
  </div>
  <div class="col-9 form-group">
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
</div>



<div class="row formgroup my-2">
  <div class="col-3">
    <span class="label_col"><label style="<?= strlen($response['msg']['prof_opt_1']) > 1 ? "color:red;" : ""; ?>">Profile Options :</label></span>
  </div>
  <div class="col-9">
    <div class="form-check form-check-inline">
    General<input
    	type="checkbox" name="prof_opt_1" class="form-check-input"
    	<? if (($oProfile->HasProfileOption(PROFILE_VOLUNTEER)) || isset($_POST['prof_opt_1'])) print "checked"; ?>>
    </div>
    <div class="form-check form-check-inline">
    Tour<input type="checkbox" name="prof_opt_2" class="form-check-input"
    <? if (($oProfile->HasProfileOption(PROFILE_TOUR)) || isset($_POST['prof_opt_2'])) print "checked"; ?>>
    </div>
    <div class="form-check form-check-inline">
    Job<input type="checkbox" name="prof_opt_3" class="form-check-input"
    <? if (($oProfile->HasProfileOption(PROFILE_JOB)) || isset($_POST['prof_opt_3'])) print "checked"; ?>>
	  </div>
  </div>
</div>


<div class="row formgroup my-2">
  <div class="col-3">
    <span class="label_col"><label style="<?= strlen($response['msg']['profile_filter_from_search']) > 1 ? "color:red;" : ""; ?>">Profile Filter from Search :</label></span>
  </div>
  <div class="col-9">
    <span class="input_col">
      <input type="checkbox" class="form-check-input" name="profile_filter_from_search" <? if ($oProfile->GetProfileFilterFromSearch() == 't') print "checked"; ?>>
    </span>
  </div>
</div>



<div class="row formgroup my-2">
  <div class="col-3">
    <span class="label_col"><label>Enquiry Options :</label></span>
  </div>
  <div class="col-9">
    <div class="form-check form-check-inline">
      General<input type="checkbox" name="enq_opt_1"  class="form-check-input" <? if (($oProfile->HasEnquiryOption(ENQUIRY_GENERAL)) || isset($_POST['enq_opt_1'])) print "checked"; ?>>
    </div>
    <div class="form-check form-check-inline">
      Booking<input type="checkbox" name="enq_opt_2" class="form-check-input" <? if (($oProfile->HasEnquiryOption(ENQUIRY_BOOKING)) || isset($_POST['enq_opt_2'])) print "checked"; ?>>
    </div>
    <div class="form-check form-check-inline">
      Job App<input type="checkbox" name="enq_opt_3" class="form-check-input"  <? if (($oProfile->HasEnquiryOption(ENQUIRY_JOB_APP)) || isset($_POST['enq_opt_3'])) print "checked"; ?>>
    </div>
  </div>
</div>


<div class="row formgroup my-2">
  <div class="col-3">
    <span class="label_col"><label><b>Approved?</b> :<span class="red"> *</span></label></span>
  </div>
  <div class="col-9">
    <span class="input_col"> <?
  	if (isset($_POST['submit'])) {
  		$checked = ($_POST['status'] == "true") ? "checked" : "";
  	} else {
  		$checked = ($oProfile->GetStatus() == 1) ? "checked" : "";
  	}
  	?> <input id="status" type="checkbox" name="status" value="true" <?= $checked; ?> />
    </span>
  </div>
</div>


<? } // end admin options ?>


<div class="row my-3">
  <span  class="label_col">&nbsp;</span>
  <span class="input_col">
    <button class="btn btn-primary rounded-pill px-3" type="submit" name="submit">Submit</button>
  </span>
</div>


</div>
</div>
</div>