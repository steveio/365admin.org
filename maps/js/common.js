
/*
 * generic.js
 * generic javascript funcs
 *
 */



function SaveCoords(pid) {

		var lat = $('#lat').val();
		var long = $('#long').val();
		
		if ((lat == '') || (long == '') || (pid == '')) return false;
		
        var url = "/webservices/save_map_coords_ajax.php";
        var target = '';
        var pars = '&pid='+pid+'&lat='+lat+'&long='+long;

        $.getJSON(url, pars, function(data){

            if (data.retVal == 1) {
                $('#user_msg').html("<img src='/images/icon_green_tick.png' alt='' title='' style='vertical-align:middle;' /> Saved Map Location OK");
                $('#img_'+image_id).hide(); /* remove the image */
            } else {
                $('#user_msg').html('Sorry, an error occured and it was not possible to save map location.');
            }
            return false;
            
        });
}
