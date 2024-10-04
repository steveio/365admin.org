
/*
 * generic.js
 * generic javascript funcs
 *
 */




function go(url) {
	var w = window.open(url,'','');
}

function goExternal(url) {
    var load = window.open(url,'','');
}



function ArticleMapOptions(mid) {
	
	var prefix = "opt_"+mid;
    var w = [];
    
    $('input[name^="'+prefix+'"]').each(function() {
    	//alert($(this).attr('name'));
    	if ($(this).attr('name') != prefix) {
    		//w[$(this).attr('name')] = $(this).attr('checked');
    		var v = ($(this).prop('checked')) ? "T" : "F";
    		var v2 = $(this).attr('name') +'_'+ v;
    		w.push(v2);
    	}
    });

    // get search phrase 
    var q = $("#sphrase_"+mid).val();
    
    // get titles if specified
    var pt = $("#ptitle_"+mid).val();
    var pi = $("#pintro_"+mid).val();

    var tid = $("#opt_"+mid+"_20").val(); // template_id
    var scid = $("#opt_"+mid+"_25").val(); // search panel cfg

    var opts = w.join('::');
	var url = "/webservices/article_opt_ajax.php";
    var pars = '&mid='+mid+'&opts='+opts+'&q='+q+'&pt='+pt+'&pi='+pi+'&tid='+tid;


	$.post(url, { mid: mid, opts: opts, q: q, pt: pt, pi: pi, tid: tid, scid: scid }, 
		function(data){
	
			$('#alert-msg').removeClass('alert-success');
			$('#alert-msg').removeClass('alert-warning');
			$('#alert-msg').addClass('alert-'+data.status);
			$('#alert-msg').show();
			$('#alert-msg').html(data.msg);

			setTimeout(function () {
                $('#alert-msg').fadeOut('fast');
            }, 5000);

			return false;
		
	}, "json");
	
		
	return false;
}



function ArticleDeattach(pid,aid) {

	var url = "/webservices/article_deattach_ajax.php";
    var pars = '&pid='+pid+'&aid='+aid;
    
	$.getJSON(url, pars, function(data){
		
        if (data.retVal == 1) {
        	$('#deattach_row' + pid).remove();
        }
        
        $('#article_deattach_msg').show();
        $('#article_deattach_msg').html(data.msg);
        
        return false;
		
	});
		
	
}

/*
 * AJAX search API
 * 
 * @param string path to PHP file in /templates 
 */
function SearchAPI() {

	$('#recent_activity').hide();
	$('#alert-msg').hide();
	
    var exp = escapePercent(document.getElementById('search_phrase').value);

    if (exp == "") {
        alert('Please enter valid keyword(s)');
        return false;
    }

	var exact = 0; /* default = fuzzy "like" matching */
	if ($('#search_exact').is(':checked')) {
		exact = 1; /* exact "=" equal matching */
	}

	var ctype = 000;

	if ($('#search_company').is(':checked')) {
		ctype = ctype + 1;
	}
	if ($('#search_placement').is(':checked')) {
		ctype = ctype + 10;
	}
	if ($('#search_article').is(':checked')) {
		ctype = ctype + 100;
	}
	
	ctype= padToThree(ctype);

		
	var filterDate = 0;
	var fromDate;
	var toDate;

	if ($('#filter_date').is(':checked')) {
		filterDate = 1;
		var daterangestr = $('#daterange').val();
		const myDateArray = daterangestr.split(" - ");
		fromDate = myDateArray[0];
		toDate = myDateArray[1];
	}

	$('#search_msg').html('');
	$('#search_result').html('');

	$('#spinner').show();


    var url = "/webservices/searchAPI_ajax.php";
    var pars = '&exp='+exp+'&ctype='+ctype+'&exact='+exact+'&filterDate='+filterDate+'&fromDate='+fromDate+'&toDate='+toDate;
    
	$.getJSON(url, pars, function(data){

        $('#search_msg').html('<div class="alert alert-'+data.status+'" role="alert">'+data.msg+'</div>');

		$('#spinner').hide();
		
	    if (data.retVal == 1) {
	        $('#search_result').html(data.html);
	    } else {
	        $('#search_result').html('');
	    }
	    
	    return false;
	});

    
    return false;
}


function SearchDispatch() {

	var key;
	var activity = $('#search-panel-activity').find(":selected").val();
	var destination = $('#search-panel-destination').find(":selected").val();
	
	if (activity != 'NULL') {
		key = activity;
	} else if (destination != 'NULL') {
		key = destination;
	} else {
		alert('Please specify either activity or destination');
	}
	
    var url = "/search-router";
    var pars = '&key='+key;
    
	$.getJSON(url, pars, function(data){

	    if (data.retVal == 1) {
	    	var url = data.html;
			window.location = url;
	    }

	    return false;
	});

    return false;
}


function padToThree(number) {
	  if (number<=999) { number = ("000"+number).slice(-3); }
	  return number;
}

function ArticleSearch(mode,aid,template) {
	
	$('#recent_activity').hide();
	$('#alert-msg').hide();

    var uri = escapePercent(document.getElementById('search_phrase').value);

    var w = [];
    
    $('input[name^="web_"]').each(function() {
    	if ($(this).attr('checked')) {
    		var wid = $(this).attr('name').split('_')[1];
    		w.push(wid);
    	}
    });
    
    var wid = w.join('::');
    
    if (uri == "") {
            alert('Please enter a valid search url or url pattern');
            return false;
    }

	$('#spinner').show();

    var search_recursive = null;
    if (mode == 'map') {
            search_recursive = document.getElementById('search_recursive').value;
    }


	var match = 0; /* default = fuzzy "like" matching */
	if ($('#search_exact').is(':checked')) {
		match = 1; /* exact "=" equal matching */
	}
	
    var url = "/webservices/article_search_ajax.php";
    var pars = '&m='+mode+'&uri='+uri+'&aid='+aid+'&r='+search_recursive+'&t='+template+'&match='+match+'&wid='+wid;

	$.getJSON(url, pars, function(data){
            $('#article_search_msg').html('<span class="red">'+data.msg+'</span>');

        	$('#spinner').hide();

            if (data.retVal == 1) {
                    $('#article_search_result').html(data.html);
            } else {
                    $('#article_search_result').html('');
            }
            return false;
	});
                        
}

function deleteProfile(url)
{
	var text = 'This will delete (archive) company and all placements - you can restore it later if necessary.  Are you sure?';

	if (confirm(text) == true) {
		window.location = url;
	}

	return false;
}

function escapePercent(str){
        return str.replace(/%/g, '%25');
}


function validateAttachProfile() { 
  
        var comp_id = document.getElementById('company_id').value;
        var placement_id = document.getElementById('placement_id').value;

        if (comp_id == 'NULL') {
                alert('Please select a profile to attach');
                return false;
        }
        
        return true;
} 


function setProfilePanelState(panel_id) {

	var panels = [4,3,2];

	for(var i = 0; i < panels.length; i++) {

		if (panels[i] == panel_id) {
			setLightSwitch(panels[i],1);
		} else {
			setLightSwitch(panels[i],0);
		}
	}
	
	var cookie_name = 'oneworld365_profile';
	var value = panel_id;
	var days;
	
	createCookie(cookie_name,value,days)
				
}


function RemoveImage(link_type,link_id,image_id) {

        var url = "/webservices/image_detach_ajax.php";
        var target = '';
        var pars = '&link_to='+link_type+'&link_id='+link_id+'&image_id='+image_id;

        $.getJSON(url, pars, function(data){

                if (data.retVal == 1) {
					/* @todo - remove requested image */
					$('#msgtext').html("Removed Image OK");
					$('#img_'+image_id).hide(); /* remove the image */
                } else {
					/* @todo - display error */
					$('#msgtext').html('An error occured and it was not possible to remove image.');
                }
                return false;
        });
}