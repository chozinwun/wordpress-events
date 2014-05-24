<?php
	$start_date = get_post_meta( $post->ID, 'event_start_date', true );
	$start_date = date( 'l F j, Y', strtotime( $start_date ) );

	$start_time = get_post_meta( $post->ID, 'event_start_time', true );
	$start_time = date( 'g:i A', strtotime( $start_time ) );

	$price = get_post_meta( $post->ID, 'event_price', true );;
	$price_notes = get_post_meta( $post->ID, 'event_price_notes', true );;

	$venue = get_post_meta( $post->ID, 'event_venue', true );
	$address = get_post_meta( $post->ID, 'event_address', true );
	$city = get_post_meta( $post->ID, 'event_city', true );
	$state = get_post_meta( $post->ID, 'event_state', true );
	$zip = get_post_meta( $post->ID, 'event_zip', true );

	if ( $city && $state )
		$location = $city . ', ' . $state;
	else
		$location = '';

	$venue = $venue . ' ' . $address . ' ' . $location . ' ' . $zip;
?>
<div class="panel panel-default">

  <div class="panel-heading">Event Details</div>
  <?php if( $price ): ?>
  	<div class="panel-body">
  		<p>This event is ticketed. Ticket price is <?php echo $price; ?></p>
  		<p><?php echo $price_notes; ?></p>
  	</div>
  <?php endif; ?>

  <!-- List group -->
  <ul class="list-group">
    <li class="list-group-item"><span class="glyphicon glyphicon-calendar"></span> <?php echo $start_date; ?></li>
    <li class="list-group-item"><span class="glyphicon glyphicon-time"></span> <?php echo $start_time; ?></li>
    <li class="list-group-item"><span class="glyphicon glyphicon-map-marker"></span> <?php echo $venue; ?></li>
  </ul>
</div>