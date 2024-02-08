

/*
 * search_panel.js  
 * JS controller logic for search panel
 * 
 */


$(document).ready(function(){

	$("#search-panel-destination").autocomplete({
		source: "/search-dispatch",
		minLength: 1
	});

	var doSearchDispatch = function(action,changed,selected) {

        var url = '/search-dispatch';

        var keywords = $('#search-panel-keywords').val();
        var activity = $('#search-panel-activity').val();
        var destination = $('#search-panel-destination').val();
        
        var pars = 'k='+keywords+'&a='+action+'&act='+activity+'&d='+destination;
  
		$.getJSON(url,pars, function(json) {
			if (json.status == 1) {
				if (json.action == 'dispatch') {
					//console.log(json.url);
				}
			} else {
				processError();
			}

		});	
	};

	var processFacetData = function(json,changed,selected) {

		if (typeof(json.facet) == "undefined") {
			// @todo - handle error
			return false;
		}

		$.each(json.facet, function(name,facetData) {
			//facet.name, facet.data			
			
			var el = $("#search-panel-"+name);
			var curval = el.val();

			var output = [];
			output.push('<option value="NULL"></option>');
			
			$.each( facetData.data, function( id,facet )				
			{
				var selected_str = '';
				if (selected == facet.facet) {
					selected_str = 'selected';
				} else if ((curval == facet.facet) && (selected_str == '')) {
					selected_str = 'selected';
				}
				output.push('<option value="'+ facet.facet +'" '+selected_str+'>'+ facet.facet +' ('+facet.count+')</option>');
			});

			el.html(output.join(''));			
			//var list = $("#"+facetData.divName).append('<div class="facet-col-inner"><ul class="unstyled"></ul></div>').find('ul');
			
		}); // end each facet
			
	}

	var processError = function() {
		$('#search-panel-msg').html('An error occured and we could not process your search');
	}

	var validateSearch = function() {
	    if (($('#search-panel-activity').val() == 'NULL') &&
			($('#search-panel-destination').val() == '') &&
	        ($('#search-panel-keywords').val() == '')) {
	    		$('#search-panel-msg').addClass("alert-warning");
	        	$('#search-panel-msg').html('Please select one or more from Destination, Activity, or Keyword(s)');
	        	$('#search-panel-msg').show().delay(5000).fadeOut();
	        return false;
	    }
	    return true;
	};

	/*
	$('select[id^=search-panel-]').change(function(e) {
		e.preventDefault();
		var curval = $('#'+this.id).val();
		var changed = this.id;
		doSearchDispatch('update',changed,curval);		
	})
	*/

	$('#search-panel-btn').click(function(e) {
		if (validateSearch()) {
			//doSearchDispatch('dispatch',null,null);
			document.getElementById("searchForm").submit();
		}
		return false;
	});

	$('#search-panel-destination').click(function(e) {
	    if ($(this).val() == "Destination")
	    	$(this).val("");
	});

});
