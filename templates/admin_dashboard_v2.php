



<!-- BEGIN Page Content Container -->
<div class="container">
<div class="align-items-center justify-content-center">


<h1>Dashboard</h1>


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
			<li>Patterns: <span class="p_small">"%" = fuzzy eg /blog/thailand%  OR /company/camp%  OR "%africa"</i></span></li>
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


<div class="container">
<div class="">

	<div id="result-hdr"></div>

	<div id="profiles"></div>

	<div id="pager" class="page-links" style="float: left; width: 700px; margin: 20px 0px 20px 0px;"></div>

</div>
</div>
</div>


	<script id="pTWide" type="text/template">
        <div class="featured-project-1col">
   		<div class="featured-project-item-1col">
    		<div class="featured-project-img-1col">
			<% if (image_url_medium.length > 1) { %>
      			<a title="<%= title %>" target="_new" href="http://www.oneworld365.org<%= profile_url %>">
         		<img src="<%= image_url_medium %>" alt="<%= title %>" />
      			</a>
			<% } %>
    		</div>

			<div class="featured-project-details-1col">
				<h2><a href="http://www.oneworld365.org<%= profile_url %>" title="<%= title %>" target="_new"><%= title %></a></h2>
				<h3><a href="<%= company_profile_url %>" title="<%= company_name %>" target="_new"><%= company_name %></a></h3>
				<p><%= desc_short %></p>
				<div class="buttons">
				<a title="<%= title %>" target="_new" href="<%= profile_url %>">
					<button id="" class="" tabindex="3" value="view" type="submit">view</button>
				</a>
				<a title="<%= title %>" target="_new" href="<%= profile_uri %>/edit">
					<button id="" class="" tabindex="3" value="edit" type="submit">edit <%= profile_type_label %></button>
				</a>
				<a title="<%= title %>" target="_new" href="<%= profile_uri %>/delete" onclick="javascript: return confirm('This will delete (archive) this placement - you can restore it later if necessary.  Are you sure?')">
					<button id="" class="" tabindex="3" value="delete" type="submit">delete</button>
				</a>
				</div>
			</div>
   		</div>
		</div>
		</script>

	<script id="oTWide" type="text/template">
        <div class="featured-project-org">
   		<div class="featured-project-item-org">
    		<div class="featured-project-logo-org">
				<% if (logo_url.length > 1) { %>
      				<a title="<%= title %>" href="<%= profile_url %>">
         			<img src="<%= logo_url %>" alt="<%= title %>" />
      				</a>
				<% } %>
    		</div>

			<div class="featured-project-details-org my-3">
			<% if (image_url_medium.length > 1) { %>
				<div style="float: right; padding: 0px 0px 6px 6px;">
				<img src="<%= image_url_medium %>" alt="<%= title %>" />
				</div>
			<% } %>

 				<h2><a href="<%= profile_url %>" target="_new" title="<%= title %>"><%= title %></a></h2>
				<p><%= desc_short %></p>
				<div class="buttons">
				<a title="<%= title %>" target="_new" href="<%= profile_url %>">
					<button id="" class="btn btn-primary rounded-pill px-3" tabindex="3" value="view" type="submit">view</button>
				</a>
				<a title="<%= title %>" target="_new" href="<%= profile_uri %>/edit">
					<button id="" class="btn btn-primary rounded-pill px-3" tabindex="3" value="edit" type="submit">edit company</button>
				</a>
				<a title="<%= title %>" target="_new" href="<%= profile_uri %>/delete" onclick="javascript: return confirm('This will delete (archive) company and all placements - you can restore it later if necessary.  Are you sure?')">
					<button id="" class="btn btn-primary rounded-pill px-3" tabindex="3" value="edit" type="submit">delete company</button>
				</a>

				</div>
			</div>

   		</div>
		</div>
	</script>

	<script id="aTWide" type="text/template">
        <div class="featured-project-org my-3">
   		<div class="featured-project-item-org">
 				<h2><a href="<%= view_url %>" target="_new" title="<%= title %>"><%= title %></a></h2>
				<p><%= desc_short %></p>
				<div class="buttons">
				<a title="<%= title %>" target="_new" href="<%= view_url %>">
					<button id="" class="btn btn-primary rounded-pill px-3" tabindex="3" value="view" type="submit">view</button>
				</a>
				<a title="<%= title %>" target="_new" href="<%= edit_url %>">
					<button id="" class="btn btn-primary rounded-pill px-3" tabindex="3" value="view" type="submit">edit</button>
				</a>
				<a title="<%= title %>" target="_new" href="<%= publish_url %>">
					<button id="" class="btn btn-primary rounded-pill px-3" tabindex="3" value="view" type="submit">publish</button>
				</a>
				</div>
			</div>

   		</div>
		</div>
	</script>


</div>
</div>
