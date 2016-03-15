window.TimeSlotPicker = ( function( window, document, $ ) {

	var app = {};

	app.cache = function() {
		app.$body = $( document.body );
	};

	app.init = function() {

		app.cache();

		app.startCountdown();

	};

	app.startCountdown = function() {

		// Countdown timer for events
		var target_date = new Date( $('.event-countdown').data('date') ).getTime();

		var days, hours, minutes, seconds;

		if ( target_date ) {

			setInterval(function () {

				var days_label = $('.event-countdown #days');
			    var hours_label = $('.event-countdown #hours');
			    var minutes_label = $('.event-countdown #minutes');
			    var seconds_label = $('.event-countdown #seconds');

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
			    $(days_label).text(days);
			    $(hours_label).text(hours);
			    $(minutes_label).text(minutes);
			    $(seconds_label).text(seconds);

			    if ( $(days_label).text() == 0 ) {

			    	$(days_label).removeClass('label-primary').addClass('label-default');
			    	$(hours_label).addClass('label-primary');

			    } else if ( $(hours_label).text() == 0 ) {

			    	$(hours_label).removeClass('label-primary').addClass('label-default');
			    	$(minutes_label).addClass('label-primary');

			    }

			}, 1000);

			$('.event-countdown').fadeIn(300);

		}

	}

	$( document ).ready( app.init );

	return app;

} )( window, document, jQuery );