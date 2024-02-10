

/*
 * review.js 
 */


$(document).ready(function(){

	$("#rateYo").rateYo({
		 rating: 2,
		 fullStar: true
	});

	$('#review-viewall').click(function(e) {
        e.preventDefault();
        $('#review-add').hide();
        $('#review-display').show();
        $('#review-more').show();
        return false;
	}); 
	$('#review-add-lnk').click(function(e) {
		$('#review-display').hide();
		$('#review-add').show();
		$('#review-display').addClass('in');
		$('#review-display').removeClass('fade');
	});
	$('#review-display-lnk').click(function(e) {
		$('#review-add').hide();
		$('#review-add').removeClass('fade');
		$('#review-display').show();
	});

	$('#review-btn').click(function(e) {
		e.preventDefault();

		$('#review-msg').html('');
		$('#review-msg').hide();
		$('#review-error').html('');
		$('#review-error').hide();

		if (validateReview()) {
			var rating = $("#rateYo").rateYo("rating");
			var form = $('#review-form');

		    $.ajax( {
		      type: "POST",
		      url: '/review',
		      data: form.serialize()+'&review-rating='+rating+'&review-submitted=true',
		      success: function( response ) {
		    	if (response.status == 0)
		    	{
		    		$('#review-msg').html(response.msg);
		    		$('#review-msg').show();
		    		$('#review-add-form').hide();
		    	} else {
		    		$('#review-error').html(response.error);
		    		$('#review-error').show();
		    	}
		      }
		    });
 
		}
	});

});

function validateReview()
{
	var arrError = [];

	if ($('#review-name').val() == '')
		arrError.push('Please enter your name');

	if ($('#review-email').val() == '')
		arrError.push('Please enter your email');

	if ($('#review-age').val() == 'NULL')
		arrError.push('Please enter your age');

	if ($('#review-gender').val() == 'NULL')
		arrError.push('Please enter your gender');

	if ($('#review-nationality').val() == '')
		arrError.push('Please enter your nationality');

	if ($('#review-title').val() == '')
		arrError.push('Please enter a review title');

	if ($('#review-review').val() == '')
		arrError.push('Please enter your review');

	if ($('#review-rating').val() == 'NULL')
		arrError.push('Please enter your age');

	var errorMsg = '';
	if (arrError.length >= 1)
	{
		errorMsg += "<p>"+arrError.length+" errors occured: </p>";
		errorMsg += "<ul>";
		for(var i =0; i< arrError.length; i++)
		{
			errorMsg += "<li>"+arrError[i]+"</li>";
		}
		errorMsg += "</ul>";

		$('#review-error').html(errorMsg);
		$('#review-error').show();
		
		return false;
	}
	
	return true;
}
