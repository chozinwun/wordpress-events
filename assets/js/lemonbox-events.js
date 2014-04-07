(function($){

	// Countdown timer for events
	var target_date = new Date( $('.event-countdown').data('date') ).getTime();
	var days, hours, minutes, seconds;

	if ( target_date ) {

		setInterval(function () {
		    
		    // find the amount of "seconds" between now and target
		    var current_date = new Date().getTime();
		    var seconds_left = (target_date - current_date) / 1000;
		 
		    // do some time calculations
		    days = parseInt(seconds_left / 86400);
		    seconds_left = seconds_left % 86400;
		     
		    hours = parseInt(seconds_left / 3600);
		    seconds_left = seconds_left % 3600;
		     
		    minutes = parseInt(seconds_left / 60);
		    seconds = parseInt(seconds_left % 60);
		     
		    // format countdown string + set tag value
		    $('.event-countdown #days').text(days);
		    $('.event-countdown #hours').text(hours);
		    $('.event-countdown #minutes').text(minutes);
		    $('.event-countdown #seconds').text(seconds);

		}, 1000);

		$('.event-countdown').fadeIn(300);

	}

	$('.volunteer-button').live('click',function(e){
		e.preventDefault(); 

		$.ajax({
			type: 'POST',
			url: wpevent.ajaxurl,
			data: { 
				action: 'add_event_volunteer',
				event_id: $(this).data('event-id')
			},
			dataType: 'json',
			success: function(response){
				if (response) {
					alert(response.msg);

					if (response.success) {
						window.location = window.location.pathname;
					}
				}
			}
		});
	});

	$('.volunteer-form').live('submit',function(e){
		e.preventDefault(); 

		var form = $(this);
		var submitButton = $(this).find('input[type="submit"]');

		submitButton.attr('disabled',true);

		$.ajax({
			type: 'POST',
			url: wpevent.ajaxurl,
			data: $(this).serialize(),
			dataType: 'json',
			success: function(response){
				if (response) {
					submitButton.attr('disabled',false);

					alert(response.msg);
					
					if ( response.success ) {
						if ( (form.find('input[name="func"]').val() == 'login_user') || (form.find('input[name="func"]').val() == 'register_user') )
							window.location = window.location.pathname;
						else
							location.reload();
					} 

				}
			}
		});
		
	});

})(jQuery);