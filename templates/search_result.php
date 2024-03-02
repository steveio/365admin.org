<!-- start: Search Results Panel-->
<div class="container row">
    <div class="col-12">
	<input id="api-url" class="" type="hidden" value="<?= $this->Get('API_URL'); ?>" name="" />
	<input id="search-rows" class="" type="hidden" value="<?= $this->Get('SEARCH_ROWS'); ?>" name="" />
	<input id="search-query" class="" type="hidden" value="<?= $this->Get('SEARCH_QUERY'); ?>" name="squery" />
	<input id="search-type" class="" type="hidden" value="<?= $this->Get('SEARCH_TYPE'); ?>" name="stype" />
	<input id="query-origin" class="" type="hidden" value="0" name="query-origin" />
	<input id="search_profile" class="" name="profile_type" value="<?= $this->Get('SEARCH_PROFILE_TYPE'); ?>" type="hidden" />
	
	<div id="search-result-panel" class="row">
	
	<div id="spinner" class="my-3" style="display: none;">
		<img src="/images/loading_spinner.gif" alt="loading travel projects..." />
	</div>
	
	<?
	if (!$this->Get('HIDE_FILTERS')) { ?>
	<div id="refine-search-panel" class="row my-2">
		<div class="row my-2">
			<!--  <div id="facet-continent" class="facet-col col-4"><?= $this->Get('FACET_CONTINENT'); ?></div>-->
			<div id="facet-country" class="facet-col col-lg-4 col-md-6 col-sm-12"><?= $this->Get('FACET_COUNTRY'); ?></div> 
			<div id="facet-activity" class="facet-col col-lg-4 col-md-6 col-sm-12"><?= $this->Get('FACET_ACTIVITY'); ?></div>
		</div>
		
		<div class="row my-2">
			<div id="facet-duration" class="facet-col col-lg-4 col-md-6 col-sm-12"></div>
			<div id="facet-price" class="facet-col col-lg-4 col-md-6 col-sm-12"></div>
			<div id="facet-species" class="facet-col col-lg-4 col-md-6 col-sm-12"></div>
			<div id="facet-habitats" class="facet-col col-lg-4 col-md-6 col-sm-12"></div>
		</div>

		<div class="row my-3">
			<div class="col-lg-3 col-md-4 col-sm-12">
			<!--<input id="do-search" type="button" class="btn btn-primary rounded-pill px-3 btn-success btn-small" value="update" />-->
			<input id="clear-filters" type="button" class="btn btn-primary rounded-pill px-3 btn-success btn-small" value="clear filters" />
			</div>		
		</div>
	</div>
	<?php } ?>
	</div>
		
    <div id="profiles" class="col-12 my-3">
    	<div class="my-3">
    		<h2><?= $this->Get('ARTICLE_DISPLAY_OPT_PTITLE'); ?></h2>
    		<p class='lead'><?= $this->Get('ARTICLE_DISPLAY_OPT_PINTRO'); ?></p>
    	</div>
	    <div id="result-hdr"></div>
	    <div class="col-12">
			<div id="search-result-b1" class="row"></div>
			<div id="search-viewall-lnk" class="row d-sm-none d-md-none">
				<a href="#" id="search-viewall" class="btn btn-primary rounded-pill px-3">View All Results</a>
			</div>
			<div id="search-result-b2" class="row"></div>
		</div>
		<div id="pager" class="col-12 pagination pagination-large pagination-centered page-links"></div>
	</div>

    <script>
	$(document).ready(function(){ 
		$('#search-viewall').click(function(e) {
            e.preventDefault();

            $('#search-result-b2').removeClass("d-sm-none");
            $('#pager').removeClass("d-sm-none");
            $('#search-viewall-lnk').addClass("d-sm-none");

            return false;
		});
	}); 
    </script>


    </div>
</div>
<!-- end: Search Results Panel-->
