<div class="container">
<div class="align-items-center justify-content-center">

<h1>Admin Dashboard</h1>

<div class="row my-3	">
<div class="col-12">
	<div class="col float-right">
			<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = './article-editor'; return false;">New Article</button>
			<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/company/add'; return false;">New Company</button>
			<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/placement/add'; return false;">New Placement</button>
			<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/enquiry-report'; return false;">Enquiries</button>
			<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/review-report'; return false;">Comments / Reviews</button>
			<button class="btn btn-outline-primary rounded-pill px-3" type="button" onclick="javascript: window.location = '/user'; return false;">Users</button>

	</div>
</div>
</div>


<div class='row my-3'>

	<h1>Search</h1>

	<div class="col">

		Url or Keywords:
		<input type="text" id="search_phrase" class="form-control" value="<?= $_REQUEST['filter_uri'] ?>" />
		<button class="btn btn-primary rounded-pill px-3" type="button" onclick="javascript: SearchAPI('<?= $_CONFIG['url'] ?>','article_search_result_list_03.php'); return false;" name="article_search">Search</button>
		Projects <input type="checkbox" id="search_exact" name="filter_project" />
		Orgs <input type="checkbox" id="search_exact" name="filter_org" />		
		<ul>
			<li>Patterns: <span class="p_small">"%" = fuzzy eg /blog/%hong-kong%  OR /company/camp%  OR /company/placement/%ski%"</i></span></li>
		</ul>

	</div>
</div>

<div class="row">
	<div id="search_msg"></div>
	<div id="search_result"></div>
</div>


<div id="spinner" style="display: none;">
	<img src="/images/loading_triangles.gif" alt="loading..." />
</div>


</div>
</div>