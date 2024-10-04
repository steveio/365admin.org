
<div class="container">
<div class="align-items-center justify-content-center">


<div id="search-panel-msg" class="alert" role="alert" style="display: none;">
    <?= isset($aResponse['msg']) ? $aResponse['msg'] : "";  ?>
</div>

<form enctype="multipart/form-data" name="searchForm" id="searchForm" action="" method="POST">
<input type="hidden" name="search-process" value="true" />

<div class="row search-panel">
<div class="col-sm-12 col-md-6 col-lg-6">

<h1>Search
<div class="row my-3">
<div class="col-2 icon_lg"><img src="/images/globe-americas.svg" width="32px" height="32px"></div>
<div class="col-2 icon_lg"><img src="/images/airplane.svg" width="32px" height="32px"></div>
<div class="col-2 icon_lg"><img src="/images/backpack4.svg" width="32px" height="32px"></div>
<div class="col-2 icon_lg"><img src="/images/brightness-high.svg" width="32px" height="32px"></div>
<div class="col-2 icon_lg"><img src="/images/building-check.svg" width="32px" height="32px"></div>
</div>
</h1>
 
<div class='row my-3'>

	<div class="row my-3">
		Keyword Search:
		<input type="text" id="search_phrase" name="search_phrase" class="form-control" value="<?= $_REQUEST['search_phrase'] ?>" />
	</row>
	<div class="row">
		<div class="col-12">		
    		Company <input type="checkbox" id="search_company" name="search_company" checked />
    		Project <input type="checkbox" id="search_placement" name="search_placement" checked />
    		Article <input type="checkbox" id="search_article" name="search_article" checked />
		</div>
        <div class="row my-3">
            <div class="col-3">
                    <button class="btn btn-primary rounded-pill px-3" type="button" onclick="javascript: SearchAPI(); return false;" name="article_search">Search</button>
            </div>
		</div>
	</div>
</div>


  <div class="col-6 mb-3">
    <label for="search-panel-destination" class="form-label">Destination</label>

    <select id="search-panel-destination" name="search-panel-destination" class="form-select">
	<option value="NULL"></option>
    <?php 
    
    $oCountry = new Country();
    $aDestination = $oCountry->GetByContinent();

    $cn_id = NULL;

    foreach($aDestination as $key => $val)
    {
        foreach($val as $aCountry)
        {
            if ($cn_id != $aCountry['continent_id'])
            {
            ?>
            <option value="cn_<?= $aCountry['continent_id'] ?>"><?= $aCountry['continent_name']?></option>
            <?  
            }
            $cn_id = $aCountry['continent_id'];
            ?>
            <option value="cty_<?= $aCountry['country_id'] ?>"><?= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$aCountry['country_name']?></option>
            <?
        }
    }
    ?>
    </select>
    <div id="search-panel-destination-help" class="form-text">Select a destination (Country or Continent).</div>
  </div>

  <div class="col-6 mb-3">
    <label for="search-panel-activity" class="form-label">Activity</label>
    <?php 
    
    $aActivity = Activity::getActivityByCategoryList();

    ?>
    <select id="search-panel-activity" name="search-panel-activity" class="form-select">
    	<option value="NULL"></option>
        <?php
    	$strCurrentCat = null; 
    	foreach ($aActivity as $strCategory => $aActivity) {
    	    if ($aActivity['cid'] != $strCurrentCat) { ?>
    			<option value="cat_<?= $aActivity['cid'] ?>"><?= $aActivity['cname']; ?></option><?
    			$strCurrentCat = $aActivity['cid'];
    		} 
    		?>
    			<option value="act_<?= $aActivity['aid']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $aActivity['aname']; ?></option><?
    		?>
    		<?
    	} ?>
    </select>
    <div id="searchActivityHelp" class="form-text">Or select an activity.</div>
  </div>

  <div class="col-2 mb-3">
  <input class="btn btn-primary rounded-pill px-3" type="submit" id="search-route-btn" onclick="javascript: SearchDispatch(); return false;" name="" value="Submit" />
  </div>

    <div class="row">
    <div class="col-12 mb-3">
    	<div id="search_result"></div>
   	</div>
    </div>


</div>
</div>

</div>
</div>

</form>
