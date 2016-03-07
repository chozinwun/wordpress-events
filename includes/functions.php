<?php

function brand_show_event_location( $post_id ) {

	$event_locations = wp_get_object_terms( $post_id, 'location' );

	if ( $event_locations ) {
		echo $event_locations[0]->name;
	}

}

function brand_show_event_day_of_week( $post_id = 0 ) {
	echo brand_get_event_day_of_week( $post_id );
}

function brand_show_event_day( $post_id = 0 ) {
	echo brand_get_event_day( $post_id );
}

function brand_show_event_month( $post_id = 0 ) {
	echo brand_get_event_month( $post_id );
}

function brand_show_event_date( $post_id = 0 ) {

	$event_start_date = brand_get_event_start_date( $post_id );
	$event_start_time = brand_get_event_start_time( $post_id );

	echo $event_start_date . ' ' . $event_start_time;

}

function brand_get_event_start_date( $post_id = 0 ) {

	$event_start_date = get_post_meta( $post_id, '_ambassador_event_start_date', true );
	$event_start_date_timestamp = strtotime( $event_start_date );

	return date( 'F jS, Y', $event_start_date_timestamp );

}

function brand_get_event_start_time( $post_id = 0 ) {

	$event_start_time = get_post_meta( $post_id, '_ambassador_event_start_time', true );
	$event_start_time_timestamp = strtotime( $event_start_time );

	return date( 'g:iA', $event_start_time_timestamp );

}

function brand_get_event_day_of_week( $post_id = 0 ) {

	$event_start_date = brand_get_event_start_date( $post_id );
	$event_start_date_timestamp = strtotime( $event_start_date );

	$day_of_week = date( 'l', $event_start_date_timestamp );

	return $day_of_week;

}

function brand_get_event_day( $post_id = 0 ) {

	$event_start_date = brand_get_event_start_date( $post_id );
	$event_start_date_timestamp = strtotime( $event_start_date );

	$day_of_week = date( 'd', $event_start_date_timestamp );

	return $day_of_week;

}

function brand_get_event_month( $post_id = 0 ) {

	$event_start_date = brand_get_event_start_date( $post_id );
	$event_start_date_timestamp = strtotime( $event_start_date );

	$day_of_week = date( 'M', $event_start_date_timestamp );

	return $day_of_week;

}

