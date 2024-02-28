<!-- start: Search Results Panel-->
<div class="container row">
    <div class="col-12">
    <?php
    $strQuery = '';
    if (strlen($this->Get('ARTICLE_DISPLAY_OPT_SEARCH_KEYWORD')) >= 1)
    {
        $strQuery = $this->Get('ARTICLE_DISPLAY_OPT_SEARCH_KEYWORD');
    } else {
        $strQuery = $this->Get('URI');
    }
    ?>
	<input id="api-url" class="" type="hidden" value="<?= $this->Get('API_URL'); ?>" name="" />
	<input id="search-query" class="" type="hidden" value="<?= $strQuery; ?>" name="query" />
	<input id="query-origin" class="" type="hidden" value="0" name="query-origin" />
	<input id="currency" class="" type="hidden" value="GBP" name="currency" />
	<input id="search_projects" class="search_type" name="search_type" value="(1 OR 0)" type="hidden" />
	
	<div id="search-result-panel" class="row">
	
	<div id="spinner" class="my-3" style="display: none;">
		<img src="/images/loading_spinner.gif" alt="loading travel projects..." />
	</div>
	
	<?
	if (!$this->Get('HIDE_FILTERS')) { ?>
	<div id="refine-search-panel" class="row">
	
	
		<div class="row my-3">
			<div id="facet-continent" class="facet-col col-4"><?= $this->Get('FACET_CONTINENT'); ?></div>
			<div id="facet-country" class="facet-col col-4"><?= $this->Get('FACET_COUNTRY'); ?></div> 
			<div id="facet-activity" class="facet-col col-4"><?= $this->Get('FACET_ACTIVITY'); ?></div>
		</div>
		
		<div class="row my-3">
			<div id="facet-duration" class="facet-col col-3"></div>
			<div id="facet-price" class="facet-col col-3"></div>
			<div id="facet-species" class="facet-col col-3"></div>
			<div id="facet-habitats" class="facet-col col-3"></div>
		</div>

		<div class="row my-3">
			<div class="col-3">
			<input id="do-search" type="button" class="btn btn-primary rounded-pill px-3 btn-success btn-small" value="update" />
			<input id="clear-filters" type="button" class="btn btn-primary rounded-pill px-3 btn-success btn-small" value="clear filters" />
			</div>		
		</div>
		
		
	</div>
	<?php } ?>
	</div>
		
    <div id="profiles" class="col-12">
    	<div class="my-3">
    		<h2><?= $this->Get('ARTICLE_DISPLAY_OPT_PTITLE'); ?></h2>
    		<p class='lead'><?= $this->Get('ARTICLE_DISPLAY_OPT_PINTRO'); ?></p>
    	</div>
	    <div id="result-hdr"></div>
	    <div class="col-12">
			<div id="search-result" class="row">
		</div>
		</div>
		<div id="pager" class="col-12 pagination pagination-large pagination-centered page-links"></div>
	</div>



    </div>
</div>
<!-- end: Search Results Panel-->