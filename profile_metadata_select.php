<div class="row my-3">

<div class="col-12">

  <div class="row formgroup my-2"><span class="label_col">
  <h1 style="margin: 0; <?= strlen($response['msg']['category']) > 1 ? "color:red;" : ""; ?>">Categories<span class="red"> *</span></h1></span>
  <span class="input_col" style="">
  <a id="expand_category_select">+ Expand</a>
  <a id="collapse_category_select">- Collapse</a> (<span id="cat_selected"><?= $this->Get('CATEGORY_LIST_SELECTED_COUNT'); ?></span> Selected)</a> </span>
  <span id="category_select" class="input_col" style="display: none;">
  <div class="row formgroup my-2">
  <div class="py-2">
  <ul class='form-check'>
  <?= $this->Get('CATEGORY_LIST'); ?>
  </ul>
  </div>
  </div>
  </span>
  </div>

</div>


<div class="col-12">
  <div class="row formgroup my-2"><span class="label_col">
  <h1 style="margin: 0; <?= strlen($response['msg']['activity']) > 1 ? "color:red;" : ""; ?>">Activities<span
  	class="red"> *</span></h1>
  </span> <span class="input_col" style=""> <a id="expand_activity_select">+
  Expand</a> <a id="collapse_activity_select">- Collapse</a> (<span
  	id="act_selected"><?= $this->Get('ACTIVITY_LIST_SELECTED_COUNT'); ?></span>
  Selected) </span> <span id="activity_select" class="input_col" style="display: none;"> <?
  	$oColumnSort = new ColumnSort;
  	$oColumnSort->SetElements($this->Get('ACTIVITY_LIST'));
  	$oColumnSort->SetCols(3);
  	$aElements = $oColumnSort->Sort();
  	?>

  <div class="row formgroup my-2">
  <div class="col-4 py-2">
  <ul class='form-check'>
  <?php
  foreach($aElements[1] as $idx => $val) {
  	print $val;
  }
  ?>
  </ul>
  </div>
  <div class="col-4 py-2">
  <ul class='form-check'>
  <?php
  foreach($aElements[2] as $idx => $val) {
  	print $val;
  }
  ?>
  </ul>
  </div>
  <div class="col-4 py-2">
  <ul class='form-check'>
  <?php
  foreach($aElements[3] as $idx => $val) {
  	print $val;
  }
  ?>
  </ul>
  </div>
  </div>

  </span></div>
</div>


<div class="col-12">


  <div class="row formgroup my-2"><span class="label_col">
  <h1 style="margin: 0; <?= strlen($response['msg']['country']) > 1 ? "color:red;" : ""; ?>">Countries<span
  	class="red"> *</span></h1></span>
  <span class="input_col" style=""> <a id="expand_country_select">+ Expand</a>
  <a id="collapse_country_select">- Collapse</a> (<span id="cty_selected"><?= $this->Get('COUNTRY_LIST_SELECTED_COUNT'); ?></span>
  Selected) </span> <span id="country_select" class="input_col"
  	style="display: none;"> <?
  	$oColumnSort = new ColumnSort;
  	$oColumnSort->SetElements($this->Get('COUNTRY_LIST'));
  	$oColumnSort->SetCols(3);
  	$aElements = $oColumnSort->Sort();
  	?>

  <div class="row formgroup my-2">
  <div class="col-4 py-2">
  <ul class='form-check'>
  <?php
  foreach($aElements[1] as $idx => $val) {
  	print $val;
  }?>
  </ul>
  </div>
  
  <div class="col-4 py-2">
  <ul class='form-check'>
  <?php 
  foreach($aElements[2] as $idx => $val) {
  	print $val;
  } ?>
  </ul>
  </div>
  
  <div class="col-4 py-2">
  <ul class='form-check'>
  <?php
  foreach($aElements[3] as $idx => $val) {
  	print $val;
  } ?>
  </ul>
  </div>
  
  </div>

  </span></div>

</div>
</div>