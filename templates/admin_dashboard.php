<script type="text/javascript">
$(document).ready(function(){

	$("#search_btn").click(function() { 

		$("#loading").hide();
		$("#error_msg").html('');
		$('#result_tbl').html('');
		$("#result_count").html('');
		
		var search_str = $("#search_str").val();	

		if (search_str == '') {
			$("#error_msg").html('Enter a company name or url eg "bunac" or "/company/bunac"');
			return false;
		}

		$("#loading").show();

		var url = '<?= BASE_URL; ?>/webservices/comp_search.php';
		var pars = '&s='+search_str;

		$.getJSON(url, pars, function(data){

			$("#loading").hide();
			$("#result_count").html('Found '+data.count+' results');
			
			var $table = $('<table>').appendTo($('#result_tbl'));
			$.each(data.data, function(index, row) {
				$('<tr>').appendTo($table)
					.append($('<td width="240px;">').text(row.title))
					.append($('<td>').html('<a class="more-link" href="<?= $this->Get('WEBSITE_URL'); ?>/<?= ROUTE_COMPANY; ?>/'+row.url_name+'" title="View Company Profile" target="_new">view</a>'))
					.append($('<td>').html('<a class="more-link" href="/<?= ROUTE_DASHBOARD; ?>/<?= ROUTE_COMPANY; ?>/'+row.url_name+'" title="Edit Company Profile">edit</a>'));
	        });

			
		});

		return false;
								
	}); 
	
});
</script>




<div class="col four-sm pad">

	<h1>Admin Dashboard</h1>

	<form name="CompanyListingSearchForm" action="/<?= ROUTE_DASHBOARD; ?>" method="post">

	
	<div class="row four">
	<ul class="std">
		<li><a class="more-link" href="/<?= ROUTE_COMPANY ?>/add" title="Add a new Company Profile">Add a new Company Profile</a></li>
		<li><a class="more-link" href="/<?= ROUTE_PLACEMENT ?>/add" title="Add a new Placement Profile">Add a new Placement</a></li>
		<li><a class="more-link" href="/<?= ROUTE_MAILSHOT ?>" title="Send an email to a list of companies">Mailshot</a></li>
	</ul>
	</div>
	

	<h2>Search</h2>

	<div class="row four">
		<span class="label_col"><label for="comp_name" style="<?= isset($aError['COMP_NAME']) ? "color:red;" : ""; ?>">Organisation Name: </label></span>
		<span class="input_col">
			<input id="search_str" title="comp_name" type="text" class="textbox250" name="comp_name" maxlength="120" value="<?= isset($_POST['comp_name']) ? $_POST['comp_name'] : ""; ?>" />
			<input id="search_btn" type="submit" title="company search button" name="comp_search" value="search" />
			<br /><span id="error_msg" class="error red"></span>
					
		</span>
	</div>
	
	<div id="loading" class="row four" style="display: none;">
	<img src="/images/spinner.gif" alt="loading" border="0" style="vertical-align: middle;" /> Searching...
	</div>
	
	<div id="result_count" class="row four"></div>
	<div id="result_tbl" class="row four"></div>
	
	</form>	

</div>

