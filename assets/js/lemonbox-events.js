(function($){

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