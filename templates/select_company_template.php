<h1>New / Existing Listing? <?= $this->Get('STEP_NO'); ?></h1>

<p><img src="/images/chevron-grey.gif" alt="" border="0" /> To setup a new listing <a href="/<?= ROUTE_COMPANY ?>" title="Create a new listing">click here.</a></p>

<p><img src="/images/chevron-grey.gif" alt="" border="0" /> To search for an existing listing to update <a id="" href="" title="">click here</a>.</p>

<hr />



<div class="col four-sm pad hide">

	<form name="CompanyListingSearchForm" action="/<?= ROUTE_SELECT_COMPANY; ?>" method="post">

	<h2>Company Listing Search</h2>

	<div class="row four">
		<span class="label_col"><label for="comp_name" style="<?= isset($aError['COMP_NAME']) ? "color:red;" : ""; ?>">Company / Organization Name: </label></span>
		<span class="input_col">
			<input title="comp_name" type="text" class="textbox250" name="comp_name" id="comp_name" maxlength="30" value="<?= isset($_POST['comp_name']) ? $_POST['comp_name'] : ""; ?>" />
			<?php if (isset($aError['COMP_NAME'])) { ?>
				<br /><span class="error red"><?= $aError['COMP_NAME']; ?></span>			
			<?php } ?>
			<input type="submit" title="company search button" name="comp_search" value="search" />		
		</span>
	</div>
	
	<div class="row four">
		<span  class="label_col">&nbsp;</span>
		<span class="input_col">
			
		</span>
	</div>
	
	</form>	

</div>

