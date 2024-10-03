<div class="container">
<div class="align-items-center justify-content-center">

<h1>Admin Dashboard</h1>

<div class="row my-3	">
<div class="col-12">
	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = './article-editor'; return false;">New Article</button>
	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/company/add'; return false;">New Company</button>
	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/placement/add'; return false;">New Placement</button>
	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/enquiry-report'; return false;">Enquiries</button>
	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/review-report'; return false;">Comments / Reviews</button>
	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/user'; return false;">Users</button>
	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/activity-admin'; return false;">Activity</button>
	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/refdata-admin'; return false;">Refdata</button>
	<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/link-report'; return false;">Links</button>
    <button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/image-browser'; return false;">Image Browser</button>
</div>
</div>


<div class='row my-3'>

	<div class="row my-3">
		Search:
		<input type="text" id="search_phrase" name="search_phrase" class="form-control" value="<?= $_REQUEST['search_phrase'] ?>" />
	</row>
	<div class="row">
		<div class="col-12">
			URL: <input type="checkbox" id="search_url" name="search_url" checked />
			Name: <input type="checkbox" id="search_name" name="search_name" />
    	</div>
		<div class="col-12">		
    		Company <input type="checkbox" id="search_company" name="search_company" checked />
    		Placement <input type="checkbox" id="search_placement" name="search_placement" checked />
    		Article <input type="checkbox" id="search_article" name="search_article" checked />
		</div>
		<div class="col-12">
    		Exact? <input type="checkbox" id="search_exact" name="search_exact" />
		
    		<?php 
    		$strDateRange = isset($_REQUEST['daterange']) ? $_REQUEST['daterange'] : date("d-m-Y",strtotime("-1 month"))." - ".date("d-m-Y");
    		?>
        	<label for="daterange">Date range:</label>
        	<input type="checkbox" id="filter_date" name="filter_date" />
        	<input type="text" id="daterange" name="daterange" value="<?= $strDateRange; ?>" />

		</div>
        <div class="row my-3">
                <div class="col-3">
                        <button class="btn btn-primary rounded-pill px-3" type="button" onclick="javascript: SearchAPI(); return false;" name="article_search">Search</button>
                </div>
		</div>
	</div>
</div>


<div class="row">
	<div id="search_msg"></div>
	<div id="search_result"></div>
</div>


<div id="spinner" style="display: none;">
	<img src="/images/loading_triangles.gif" alt="loading..." />
</div>


<div class="row">
	<div id="recent_activity">

<?= $this->Get('RECENT_ACTIVITY'); ?>	
	</div>
</div>


<script>

$(document).ready(function() {

	$('input[name="search_name"]').click(function() {
		$('input[name="search_url"]').prop('checked', false);
	});

	$('input[name="search_url"]').click(function() {
		$('input[name="search_name"]').prop('checked', false);
	});

	
    $(function() {
      $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        locale: {
            format: 'DD-MM-YYYY'
        }
      }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      });
    });

});

</script>


</div>
</div>
