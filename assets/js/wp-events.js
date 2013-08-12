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
				}
			}
		});
	});

})(jQuery);