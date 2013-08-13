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
			'show_in_nav_menus' => true,
			'rewrite' 			=> array( 'slug' => 'events' ),
			'capability_type' => 'page'
		);

		register_post_type('event',$args);
	}

	function event_load_scripts() {
		wp_register_script('wp-events-js',plugins_url('/assets/js/wp-events.js',__FILE__),array('jquery'),false);
		wp_enqueue_script('wp-events-js');

		wp_localize_script('wp-events-js','wpevent', array( 'ajaxurl' => admin_url('admin-ajax.php')));
	}

	function event_details_box() {
		add_meta_box('event_zip_box',__('Event Details'),'event_details_box_content','event','side','high');
	}
	
	function event_details_box_content($post) {
		$meta = get_post_meta($post->ID);
		
		echo '<p><strong>Details</strong></p>';
		echo '<label>Date</label><br /> <input name="_event_start_date" value="' . $meta['_event_start_date'][0] . '" /><br />';
		echo '<label>Time</label><br /> <input name="_event_start_time" value="' . $meta['_event_start_time'][0] . '" /><br />';
		echo '<p><strong>Location</strong></p>';
		echo '<input type="hidden" name="event_details_nonce" id="event_details_nonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
		echo '<label>Venue</label><br /> <input name="_event_venue" value="' . $meta['_event_venue'][0] . '" /><br />';
		echo '<label>Address</label><br /> <input name="_event_address" value="' . $meta['_event_address'][0] . '" /><br />';
		echo '<label>City</label><br /> <input name="_event_city" value="' . $meta['_event_city'][0] . '" /><br />';
		echo '<label>State</label><br /> <input name="_event_state" value="' . $meta['_event_state'][0] . '" /><br />';
		echo '<label>Zip Code</label><br /> <input name="_event_zip" value="' . $meta['_event_zip'][0] . '" /><br />';
		echo '<label>Phone</label><br /> <input name="_event_phone" value="' . $meta['_event_phone'][0] . '" /><br />';

		if ( $meta['_event_allow_volunteers'][0] ) {
			echo '<label>Allow Volunteers?</label> <input name="_event_allow_volunteers" value="1" type="checkbox" checked="true" />';
		} else {
			echo '<label>Allow Volunteers?</label> <input name="_event_allow_volunteers" value="1" type="checkbox" />';
		}
	}
	
	function save_event_details($post_id) {
		
		if (isset($_REQUEST['_event_start_date'])) {
			update_post_meta($post_id, '_event_start_date', $_REQUEST['_event_start_date']);
			update_post_meta($post_id, '_event_start_date_actual', strtotime($_REQUEST['_event_start_date']));
	    }
	    
	    if (isset($_REQUEST['_event_start_time'])) {
			update_post_meta($post_id, '_event_start_time', $_REQUEST['_event_start_time']);
	    }
	    
		if (isset($_REQUEST['_event_venue'])) {
			update_post_meta($post_id, '_event_venue', $_REQUEST['_event_venue']);
	    }
	    
	    if (isset($_REQUEST['_event_address'])) {
			update_post_meta($post_id, '_event_address', $_REQUEST['_event_address']);
	    }
	    
	    if (isset($_REQUEST['_event_city'])) {
			update_post_meta($post_id, '_event_city', $_REQUEST['_event_city']);
	    }
	    
	    if (isset($_REQUEST['_event_state'])) {
			update_post_meta($post_id, '_event_state', $_REQUEST['_event_state']);
	    }

	    if (isset($_REQUEST['_event_zip'])) {
			update_post_meta($post_id, '_event_zip', $_REQUEST['_event_zip']);
	    }
	    
	    if (isset($_REQUEST['_event_phone'])) {
			update_post_meta($post_id, '_event_phone', $_REQUEST['_event_phone']);
	    }

	    if (isset($_REQUEST['_event_allow_volunteers'])) {
			update_post_meta( $post_id, '_event_allow_volunteers', 1 );
	    } else {
	    	update_post_meta( $post_id, '_event_allow_volunteers', 0 );
	    }
	}
	
	function add_event_columns($columns) {
		unset($columns['title']);
		unset($columns['date']);
		
		return array_merge($columns, 
			array(
				'title' => __('Event Name'),
				'event_venue' => __('Venue'),
				'event_city' => __('City'),
				'event_state' => __('State'),
	      		'total_volunteers' =>__( 'Volunteers'),
	      		'total_rsvps' =>__( 'RSVPs')
	      	)
	    );
	}
	
	function custom_event_column($column,$post_id) {
		global $wpdb;
		$table_name = $wpdb->prefix . "events_volunteers";

		switch ( $column ) {
	      	case 'event_venue':
	        	echo get_post_meta( $post_id , '_event_venue' , true );
	        	break;
	      	case 'event_city':
	        	echo get_post_meta( $post_id , '_event_city' , true );
	        	break;
	        case 'event_state':
	        	echo get_post_meta( $post_id , '_event_state' , true );
	        	break;
	      	case 'total_volunteers':
	      		$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE event_id = $post_id" );
	      		echo "<strong>$user_count</strong>";
	      		break;
	      	case 'total_rsvps':
	      		echo "<strong>0</strong>";
	      		break;
	    }
	}
	
	function display_event_title($title) {
		global $post;

		if (($post->post_type == 'event') && in_the_loop()){
			$meta = get_post_meta(get_the_ID());
			$date = "";
			$time = "";
			
			if (isset($meta['_event_start_date'][0])) $date = $meta['_event_start_date'][0];
			if (isset($meta['_event_start_time'][0])) $time = $meta['_event_start_time'][0];
			
			$event_title = $title . "<br /><small style=\"font-weight: 200;\">$date $time</small>";
			return $event_title;	
		} else 
			return $title;
	}

	function display_event_content($content) {
		global $post;
		
		if (($post->post_type == 'event') && is_single()){
			include plugin_dir_path(__FILE__) . 'templates/event-content.php';	
		} elseif (is_archive()) {
			include plugin_dir_path(__FILE__) . 'templates/event-archive.php';
			return $post->post_excerpt;
		} else {
			return $content;
		}
	}

	function single_event_template($single_template) {
		global $post;
		
		if ($post->post_type == 'event') {
			if (is_single()) {
				$single_template = plugin_dir_path( __FILE__ ) . 'templates/single-event.php';
	    		return $single_template;
	    	}
	    }
	}
	
	function filter_events($query){
		
		if (is_home()) {
			//$query->set('post_type',array('post','event'));
		}

		if(is_post_type_archive('event')){
			if(!is_admin() && $query->is_main_query()) {

				query_posts(array(
					'post_type' => 'event',
					'orderby' => 'meta_value',
					'meta_key' => '_event_start_date_actual',
					'order' => 'ASC',
					'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1 )
				));
			}
		}
	}

	function events_install() {
		global $wpdb;

   		$table_name = $wpdb->prefix . "events_volunteers";

   		$sql = "CREATE TABLE $table_name (
			id mediumint(11) NOT NULL AUTO_INCREMENT,
			event_id mediumint(11) NOT NULL,
			user_id mediumint(11) NOT NULL,
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
		);";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
	}

	function add_event_volunteer() {
		global $wpdb, $current_user;
		
		$table_name = $wpdb->prefix . "events_volunteers";
		$event_id = $_REQUEST['event_id'];
		$user_id = $current_user->ID;

		if ( is_user_logged_in() ) {

			$volunteer = $wpdb->get_row("SELECT * FROM $table_name WHERE user_id = $user_id AND event_id = $event_id");

			if (!$volunteer) {
				$entry = array(
					'event_id' => $event_id,
					'user_id' => $current_user->ID,
					'created_at' => current_time('mysql')
				);

				$response = $wpdb->insert( $table_name, $entry );
				echo json_encode( array( 'success' => true, 'msg' => 'Thank You for volunteering. Be on the lookout for volunteer opportunities') );
			
			} else {
				echo json_encode( array( 'success' => false, 'msg' => 'Thank You for your interest, but you\'re already registered as a volunteer') );	
			}
			
		} else {

		}

		exit;
	}

	add_action('init','events_post_type');
	add_action('init','events_install');

	add_action('wp_enqueue_scripts', 'event_load_scripts');

	add_action('add_meta_boxes','event_details_box');
	add_action('save_post','save_event_details' );
	add_filter('manage_event_posts_columns','add_event_columns');
	add_action( 'manage_event_posts_custom_column', 'custom_event_column', 10, 2 );
	
	// Visual modifications
	//add_filter('single_template','single_event_template');
	add_filter('the_content','display_event_content');
	add_filter('the_title','display_event_title');
	add_action('pre_get_posts','filter_events');

	// Ajax
	add_action('wp_ajax_add_event_volunteer', 'add_event_volunteer');
	add_action('wp_ajax_nopriv_add_event_volunteer', 'add_event_volunteer');
?>