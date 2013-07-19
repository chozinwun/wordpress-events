<?php
	/*
	Plugin Name: Events Post Type
	Description: Simple events custom post type
	Version: 0.1
	License: GPL
	Author: Marcus Battle
	Author URI: http://marcusbattle.com
	*/
	
	function events_post_type() {
		
		$labels = array(
			'name'               => _x( 'Events', 'post type general name' ),
			'singular_name'      => _x( 'Event', 'post type singular name' ),
			'add_new'            => _x( 'Add New', 'event' ),
			'add_new_item'       => __( 'Add New Event' ),
			'edit_item'          => __( 'Edit Event' ),
			'new_item'           => __( 'New Event' ),
			'all_items'          => __( 'All Events' ),
			'view_item'          => __( 'View Event' ),
			'search_items'       => __( 'Search Events' ),
			'not_found'          => __( 'No events found' ),
			'not_found_in_trash' => __( 'No events found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'Events',
			'can_export'			=> true
		);
		
		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds our events and event specific data',
			'public'        => true,
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt'),
			'has_archive'   => true,
			'show_in_nav_menus' => true
		);
		register_post_type('event',$args);
	}

	function event_details_box() {
		add_meta_box('event_zip_box',__('Event Location'),'event_details_box_content','event','side','high');
	}
	
	function event_details_box_content($post) {
		$meta = get_post_meta($post->ID);

		echo '<input type="hidden" name="event_details_nonce" id="event_details_nonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
		echo '<label>Venue</label><br /> <input name="event_venue" value="' . $meta['event_venue'][0] . '" />';
		echo '<label>Zip Code</label><br /> <input name="event_zip" placeholder="Ex. 27405" value="' . $meta['event_zip'][0] . '" />';
	}
	
	function save_event_details($post_id) {
		print_r($post_id);
		
		if (isset($_REQUEST['event_zip'])) {
			update_post_meta($post_id, 'event_zip', $_REQUEST['event_zip']);
    }
	}
	
	add_action('init','events_post_type');
	
	add_action('add_meta_boxes','event_details_box');
	add_action('save_post','save_event_details' );
?>