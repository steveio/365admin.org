<style>

.ui-autocomplete {
    max-height: 100px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
    background: #FFFFFF;
    font-size: 1.4em;
    }
    * html .ui-autocomplete {
    height: 100px;
}
  
</style>

<div class="container">
<div class="align-items-center justify-content-center">


<div id="search-panel-msg" class="alert" role="alert" style="display: none;">
    <?= isset($aResponse['msg']) ? $aResponse['msg'] : "";  ?>
</div>

<form enctype="multipart/form-data" name="searchForm" id="searchForm" action="" method="POST">
<input type="hidden" name="search-process" value="true" />

<div class="row search-panel">
<div class="col-6">

  <h1>Search</h1>
 
  <div class="col-12 mb-3">
    <label for="search-panel-keywords" class="form-label">Keyword(s)</label>
    <input type="text" class="form-control" id="search-panel-keywords" name="search-panel-keywords" aria-describedby="search-panel-keywords-help" value="<?= $this->Get('SEARCH_KEYWORDS'); ?>">
    <div id="search-panel-keywords-help" class="form-text">Enter search keywords eg "Gap Year Australia" or "Volunteer with animals".</div>
  </div>

  <div class="col-12 mb-3">
    <label for="search-panel-destination" class="form-label">Destination</label>
    <input type="text" class="form-control" id="search-panel-destination" name="search-panel-destination" aria-describedby="search-panel-destination-help" />
    <div id="search-panel-destination-help" class="form-text">Or type the first few letters of a destination (Country or Continent).</div>
  </div>

  <div class="col-12 mb-3">
    <label for="search-panel-activity" class="form-label">Activity</label>    
    <select id="search-panel-activity" name="search-panel-activity" class="form-select">
    	<option value="NULL"></option>
        <?php
    	$strCurrentCategory = null; 
    	foreach ($this->Get('ACTIVITY_LIST') as $strCategory => $aActivity) {
    		if ($strCategory != $strCurrentCategory) { ?>
    			<option value="<?= $strCategory ?>"><?= $strCategory; ?></option><?
    			$strCategory = $strCategory;
    		} 
    		foreach($aActivity as $idx => $strActivity)
    		{?>
    			<option value="<?= $strActivity; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $strActivity; ?></option><?
    		}?>
    		<?
    	} ?>
    </select>
    <div id="searchActivityHelp" class="form-text">Or select an activity.</div>
  </div>

  <div class="col-2 mb-3">
  <input class="btn btn-primary rounded-pill px-3" type="submit" id="search-panel-btn" name="submit-btn" value="Submit" />
  </div>

</div>
</div>

</div>
</div>

</form>