
/*
 * generic.js
 * generic javascript funcs
 *
 */




function go(url) {
        //window.location = url;
	var w = window.open(url,'','');
}
    
function hitandgo(url,gid) {
        pageTracker._trackPageview(gid);
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
    
    var radio_prefix_txtalign = "opt_rad_"+mid+"_txtalign";
    
    var v = $('input[name=' + radio_prefix_txtalign + ']:radio:checked').val()+'_T';
	w.push(v);

    
    // get search phrase 
    var q = $("#sphrase_"+mid).val();
    
    // get titles if specified
    var pt = $("#ptitle_"+mid).val();
    var ot = $("#otitle_"+mid).val();
    var nt = $("#ntitle_"+mid).val();
    var pi = $("#pintro_"+mid).val();
    var oi = $("#ointro_"+mid).val();
    
    
    var opts = w.join('::');
	var url = "/article_opt_ajax.php";
    var pars = '&mid='+mid+'&opts='+opts+'&q='+q+'&pt='+pt+'&ot='+ot+'&nt='+nt;
    
	$.post(url, { mid: mid, opts: opts, q: q, pt: pt, ot: ot, nt: nt, pi: pi, oi: oi }, 
		function(data){
	
			$('#msgtext').html(data.msg);
			return false;
		
	}, "json");
	

	//$.getJSON(url, pars, function(data){
	//	$('#msgtext').html(data.msg);
	//	return false;	
	//});
		
	return false;
}



function ArticleDeattach(url,pid,aid) {

	var url = url +"/article_deattach_ajax.php";
    var pars = '&pid='+pid+'&aid='+aid;

	$.getJSON(url, pars, function(data){
		
        if (data.retVal == 1) {
        	$('#deattach_row' + pid).remove();
        }
        $('#article_deattach_msg').html(data.msg);
        
        return false;
		
	});
		
	
}


function ArticleSearch(url,mode,aid,template) {

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

        var search_recursive = null;
        if (mode == 'map') {
                search_recursive = document.getElementById('search_recursive').value;
        }


	var match = 0; /* default = fuzzy "like" matching */
	if ($('#search_exact').is(':checked')) {
		match = 1; /* exact "=" equal matching */
	}
        
        var url = url +"/article_search_ajax.php";
        var pars = '&m='+mode+'&uri='+uri+'&aid='+aid+'&r='+search_recursive+'&t='+template+'&match='+match+'&wid='+wid;

		$.getJSON(url, pars, function(data){
                $('#article_search_msg').html('<span class="red">'+data.msg+'</span>');

                if (data.retVal == 1) {
                        $('#article_search_result').html(data.html);
                } else {
                        $('#article_search_result').html('');

                }
                return false;
    	});
                        
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

function doPlacementListRequest(url,form_id,comp_id) {

        var comp_id = document.getElementById('company_id').value;

        if (comp_id == 'NULL') {
                alert('Please select a profile to attach');
                return false;
        }

        var pars = '&cid=' + comp_id;
        var target = 'placement_list';
        $.getJSON(url, pars, function(data){
                $('#placement_list').html(data.msg);
    	});        
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

/* @depreciated - use jquery cookie instead */
function createCookie(name,value,days) {
        if (days) {
                var date = new Date();
                date.setTime(date.getTime()+(days*24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
} 
/* @depreciated - use jquery cookie instead */
function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
}
/* @depreciated - use jquery cookie instea */
function eraseCookie(name) {
        createCookie(name,"",-1);
} 

/* @depreciated - use jquery .show() .hide() instead */
function setLightSwitch(e,state) {

        if (document.getElementById) {
                // this is the way the standards work
                if(document.getElementById(e) != null) {
                        if(state == 0) {
                                document.getElementById(e).style.visibility = "hidden";
                                document.getElementById(e).style.display = "none";
                        } else {
                                document.getElementById(e).style.visibility = "visible";
                                document.getElementById(e).style.display = "";
                        }
                }
        } else if (document.all) {
                // this is the way old msie versions work
                if(document.all[e] != null ) {
                        if(state == 0) {
                                document.all[e].style.visibility = "hidden";
                                document.all[e].style.display = "none";
                        } else {
                                document.all[e].style.visibility = "visible";
                                document.all[e].style.display = "";
                        }
                }
        } else if (document.layers) {
                // this is the way nn4 works
                if(state == 0) {
                        if(document.layers[e].visibility != "hidden") {
                                document.layers[e].visibility = "hidden";
                        } else {
                                document.layers[e].visibility = "visible";
                        }
                }
        }
}


        function AttachLink(url,link_to,link_id) {

                var link_title = document.getElementById('link_title').value;
                var link_url = document.getElementById('link_url').value;

                var url = url +"/attach_link_ajax.php";
                var pars = '&m=ADD&link_to='+link_to+'&link_to_id='+link_id+'&link_title='+link_title+'&link_url='+link_url;

		$.getJSON(url, pars, function(data){
			if (data.retVal == 1) {
				$('#link_msg').html('<span class="red">'+data.msg+'</span>');
				$('#link_result').html(data.html);
			}
			return false;
		});
        }

        function RemoveLink(url,link_id,link_to_id) {

                var url = url +"/attach_link_ajax.php";
                var pars = '&m=DEL&link_id='+link_id+'&link_to_id='+link_to_id+'&link_to=ARTICLE';

		$.getJSON(url, pars, function(data){
			if (data.retVal == 1) {
				$('#link_msg').html('<span class="red">'+data.msg+'</span>');
				$('#link_result').html(data.html);
			}
			return false;
		});
        }


function RemoveImage(url,link_type,link_id,image_id) {

        var url = url +"/image_detach_ajax.php";
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

function doProjectSearchRequest(host) {
	var url = host + '/project_search_ajax.php';
	var aid = document.project_search.s_activity_id.value.toUpperCase();
	var ctn = document.project_search.s_continent_id.value.toUpperCase();
	var cty = document.project_search.s_country_id.value.toUpperCase();
	
	if ((aid == "NULL") && (ctn == "NULL") && (cty == "NULL")) return false;
	
	var pars = '&aid='+aid+'&ctn='+ctn+'&cty='+cty;

	$.getJSON(url, pars, function(data){

                if (data.retVal == 1) {
			//alert(data.act);
			//alert(data.ctn);
			//alert(data.cty);
        		$('#act_ddlist').html(data.act);
		        $('#ctn_ddlist').html(data.ctn);
		        $('#cty_ddlist').html(data.cty);
		}
	});
}



