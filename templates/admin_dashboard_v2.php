



<!-- BEGIN Page Content Container -->
<div class="container">
<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">


<div id="" class="">

	<h1>Admin Dashboard</h1>


	<div class="row formgroup my-2">
	<ul class="">
		<li><a class="more-link" href="/<?= ROUTE_COMPANY ?>/add" title="Add a new Company Profile">Add a new Company Profile</a></li>
		<li><a class="more-link" href="/<?= ROUTE_PLACEMENT ?>/add" title="Add a new Placement Profile">Add a new Placement</a></li>
		<!-- <li><a class="more-link" href="/<?= ROUTE_MAILSHOT ?>" title="Send an email to a list of companies">Mailshot</a></li> -->
	</ul>
	</div>



<div class="row formgroup my-2">
  <div class="row my-2">
    <div class="col-2 form-check form-check-inline">
  		<label class="" for="projects">Projects</label>
  		<input id="search_projects" class="form-check" name="search_type" value="1" type="radio" checked />
  	</div>
  	<div class="col-2 form-check form-check-inline">
  		<label class="" for="organisations">Organisations</label>
  		<input id="search_orgs" class="form-check" name="search_type" value="0" type="radio" />
  	</div>
  	<div class="col-2 form-check form-check-inline">
  		<label class="" for="articles">Articles</label>
  		<input id="search_articles" class="form-check" name="search_type" value="2" type="radio" />
  	</div>
  </div>

  <div class="row">
  	<div class="col-9">
        <div class="form-group mx-sm-3 mb-2">
    			<input id="search-query" class="form-control" type="text" value="" autocapitalize="off" autocorrect="off" autofocus="autofocus" tabindex="1" name="query">
          <input id="query-origin" class="" type="hidden" value="1" name="query-origin">
        </div>
    </div>
  	<div class="col-2">    
        <button id="search-btn" class="btn btn-primary rounded-pill mb-2" type="submit" name="search" value="search">Search</button>
    </div>
    <div class="col-2">
    
    </div>
  </div>
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
