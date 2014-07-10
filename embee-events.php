<?php
	/*
	Plugin Name: Embee Events
	Description: Simple Events for WordPress
	Version: 0.1
	License: GPL
	Author: Marcus Battle
	Author URI: http://marcusbattle.com
	*/
	
	include 'inc/shortcodes.php';


	function embee_events_admin_scripts() {

		wp_enqueue_script( 'jquery-ui-core', '//code.jquery.com/ui/1.10.4/jquery-ui.min.js' );
	}

	add_action( 'admin_enqueue_scripts', 'embee_events_admin_scripts' );

	function events_post_type() {

		global $wp_rewrite;

		date_default_timezone_set('America/New_York');

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
			'menu_position' => 31,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes'),
			'has_archive'   => true,
			'show_in_nav_menus' => true,
			'rewrite' 			=> array( 'slug' => 'events' ),
			'capability_type' => 'page',
			'hierarchical'	=> false,
			'publicly_queryable' => true,
			'query_var' => true,
			'can_export' => true
		);

		register_post_type('embee_event',$args);

		if ( isset($_REQUEST['rsvp_status']) ) {
			setcookie( 'event_rsvp_status', $_REQUEST['rsvp_status'], time()+3600*24*100, '/' );
		}


		$event_rewrite = '/events/%year%/%monthnum%/%embee_event%';

		add_rewrite_tag( '%embee_event%', '([^/]+)' );
		add_permastruct( 'embee_event', $event_rewrite, false );

	}

	// Adapted from get_permalink function in wp-includes/link-template.php
	function event_permalink( $permalink, $post ) {

	    if ( 'embee_event' == get_post_type( $post ) ) {
	    	
	    	$date_vars = array( '%year%', '%monthnum%' );
	    	
	    	$dates[] = date('Y', strtotime($post->post_date) );
	    	$dates[] = date('m', strtotime($post->post_date) );
	    	
	    	$permalink = str_replace( $date_vars, $dates, $permalink );
	        
	    }

	    return $permalink;

	}

	// Add filter to plugin init function
	add_filter( 'post_type_link', 'event_permalink', 10, 2 ); 


	function event_load_scripts() {
		wp_register_script('wp-events-js',plugins_url('/assets/js/embee.events.js',__FILE__),array('jquery'),false,true);
		wp_enqueue_script('wp-events-js');

		wp_localize_script(
			'wp-events-js',
			'wpevent', 
			array( 
		 		'ajaxurl' => admin_url('admin-ajax.php'),
		 		'user_logged_in' => is_user_logged_in()
		 	)
		);

		wp_register_style( 'wp-events-css', plugins_url('/assets/css/lemonbox-events.css', __FILE__) );
        wp_enqueue_style( 'wp-events-css' );
	}

	function register_event_submenu_page() {
		add_submenu_page( null, 'Volunteers', 'Volunteers', 'manage_options', 'volunteers', 'volunteer_page');

		function volunteer_page() {
			require_once ( plugin_dir_path(__FILE__) . 'templates/volunteers.php' );
		}
	}

	function event_details_box() {
		
		add_meta_box( 'event_information', 'Event Details', 'event_details_box_content', 'embee_event', 'normal', 'high' );
		add_meta_box( 'ticket_information', 'Ticket Information', 'event_meta_box_ticket_information', 'embee_event', 'normal', 'high' );
		add_meta_box( 'rsvp_information', 'RSVP', 'event_meta_box_rsvp', 'embee_event', 'normal', 'high' );
		
	}
	
	function event_meta_box_rsvp( $post ) {
		echo "RSVP";
	}

	function event_meta_box_ticket_information( $post ) {

		$args = array(
			'post_type' => 'lemonbox_product',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_category',
					'field' => 'slug',
					'terms' => array( 'tickets' )
				)
			)
		);

		$query = new WP_Query( $args );
		$tickets = $query->posts;

		echo '<p><strong>Ticket</strong></p>';
		echo '<select name="ticket_id" data-value="' . get_post_meta( $post->ID, 'ticket_id', true ) . '">';
		echo '<option value="">--</option>';
		foreach ($tickets as $ticket) {
			echo '<option value="' . $ticket->ID . '">' . $ticket->post_title . '</option>';
		}
		echo '</select>';

		echo "<script>jQuery('select[name=\"ticket_id\"]').val( jQuery('select[name=\"ticket_id\"]').data('value') );</script>";
	}

	function event_details_box_content($post) {

		$meta = get_post_meta($post->ID);

		$price = get_post_meta( $post->ID, 'event_price', true );
		$price_notes = get_post_meta( $post->ID, 'event_price_notes', true );

		$start_date = get_post_meta( $post->ID, 'event_start_date', true );
		$start_time = get_post_meta( $post->ID, 'event_start_time', true );
		$end_date = get_post_meta( $post->ID, 'event_end_date', true );
		$end_time = get_post_meta( $post->ID, 'event_end_time', true );

		$venue = get_post_meta( $post->ID, 'event_venue', true );
		$address = get_post_meta( $post->ID, 'event_address', true );
		$city = get_post_meta( $post->ID, 'event_city', true );
		$state = get_post_meta( $post->ID, 'event_state', true );
		$zip = get_post_meta( $post->ID, 'event_zip', true );

		echo '<label>Start Date</label><br /> <input type="date" name="event_start_date" value="' . $start_date . '" /><br />';
		echo '<label>Start Time</label><br /> <input name="event_start_time" value="' . $start_time . '" /><br />';
		echo '<label>End Date</label><br /> <input type="date" name="event_end_date" value="' . $end_date . '" /><br />';
		echo '<label>End Time</label><br /> <input name="event_end_time" value="' . $end_time . '" /><br />';
		echo '<hr />';

		echo '<p><strong>Price</strong></p>';
		echo "<label>Price</label><br /> <input name=\"event_price\" value=\"$price\" /><br />";
		echo "<label>Price Notes</label><br /> <textarea name=\"event_price_notes\">$price_notes</textarea><br />";
		echo '<p><strong>Location</strong></p>';
		echo '<input type="hidden" name="event_details_nonce" id="event_details_nonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
		echo '<label>Venue</label><br /> <input name="event_venue" value="' . $venue . '" /><br />';
		echo '<label>Address</label><br /> <input name="event_address" value="' . $address . '" /><br />';
		echo '<label>City</label><br /> <input name="event_city" value="' . $city . '" /><br />';
		echo '<label>State</label><br /> <input name="event_state" value="' . $state . '" /><br />';
		echo '<label>Zip Code</label><br /> <input name="event_zip" value="' . $zip . '" /><br />';

		if ( $meta['_event_allow_volunteers'][0] ) {
			echo '<label>Allow Volunteers?</label> <input name="_event_allow_volunteers" value="1" type="checkbox" checked="true" />';
		} else {
			echo '<label>Allow Volunteers?</label> <input name="_event_allow_volunteers" value="1" type="checkbox" />';
		}
	}
	
	function save_event_details($post_id) {
		
		if (isset($_REQUEST['event_start_date'])) {
			update_post_meta( $post_id, 'event_start_date', $_REQUEST['event_start_date']);
			update_post_meta( $post_id, 'event_start_time', $_REQUEST['event_start_time']);
			update_post_meta( $post_id, '_event_start_date_actual', strtotime( $_REQUEST['event_start_date'] . ' ' . $_REQUEST['event_start_time'] ) );
	    }

	    if (isset($_REQUEST['event_end_date'])) {
			update_post_meta($post_id, 'event_end_date', $_REQUEST['event_end_date']);
			update_post_meta($post_id, 'event_end_time', $_REQUEST['event_end_time']);
			update_post_meta( $post_id, '_event_end_date_actual', strtotime( $_REQUEST['event_end_date'] . ' ' . $_REQUEST['event_end_time'] ) );
	    }

		if (isset($_REQUEST['ticket_id'])) {
			update_post_meta( $post_id, 'ticket_id', $_REQUEST['ticket_id'] );
	    }
	    
		if (isset($_REQUEST['event_venue'])) {
			update_post_meta($post_id, 'event_venue', $_REQUEST['event_venue']);
	    }
	    
	    if (isset($_REQUEST['event_address'])) {
			update_post_meta($post_id, 'event_address', $_REQUEST['event_address']);
	    }
	    
	    if (isset($_REQUEST['event_city'])) {
			update_post_meta($post_id, 'event_city', $_REQUEST['event_city']);
	    }
	    
	    if (isset($_REQUEST['event_state'])) {
			update_post_meta($post_id, 'event_state', $_REQUEST['event_state']);
	    }

	    if (isset($_REQUEST['event_zip'])) {
			update_post_meta($post_id, 'event_zip', $_REQUEST['event_zip']);
	    }
	    
	    if (isset($_REQUEST['event_phone'])) {
			update_post_meta($post_id, 'event_phone', $_REQUEST['event_phone']);
	    }

	    if (isset($_REQUEST['event_price'])) {
			update_post_meta($post_id, 'event_price', $_REQUEST['event_price']);
	    }

	    if (isset($_REQUEST['event_price_notes'])) {
			update_post_meta($post_id, 'event_price_notes', $_REQUEST['event_price_notes']);
	    }

	    if (isset($_REQUEST['_event_allow_volunteers'])) {
			update_post_meta( $post_id, '_event_allow_volunteers', 1 );
	    } else {
	    	update_post_meta( $post_id, '_event_allow_volunteers', 0 );
	    }
	}
	
	function embee_event_add_event_columns($columns) {
		unset($columns['title']);
		unset($columns['date']);

		return array_merge($columns, 
			array(
				'title' => __('Event Name'),
				'start_date' => __('Start Date'),
				'end_date' => __('End Date'),
				'location' => __('Location'),
				'total_rsvps' =>__( 'RSVPs'),
	      		'total_volunteers' =>__( 'Volunteers')
	      	)
	    );
	}
	
	function embee_event_add_custom_column( $column, $post_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "events_volunteers";

		switch ( $column ) {
	      	case 'start_date':
	      		echo get_post_meta( $post_id , 'event_start_date' , true ) . ' ' . get_post_meta( $post_id, 'event_start_time', true );
	      		break;
	      	case 'end_date':
	      		echo get_post_meta( $post_id , 'event_end_date' , true ) . ' ' . get_post_meta( $post_id, 'event_end_time', true );
	      		break;
	      	case 'location':
	        	echo get_post_meta( $post_id , '_event_venue' , true ) . "<br />";
	        	echo get_post_meta( $post_id , '_event_city' , true ) . " " . get_post_meta( $post_id , '_event_state' , true );
	        	break;
	      	case 'total_volunteers':
	      		$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE event_id = $post_id" );
	      		echo "<strong><a href=\"?page=volunteers&event_id=$post_id\">$user_count</a></strong>";
	      		break;
	      	case 'total_rsvps':
	      		echo "<strong>0</strong>";
	      		break;
	    }
	}

	function embee_event_filter_date( $date ) {

		global $post; 

		if ( $post->post_type == 'embee_event' ) {

			$start_date = get_post_meta( $post->ID, 'event_start_date', true );
			$start_date = date( 'l F j, Y', strtotime( $start_date ) );

			$start_time = get_post_meta( $post->ID, 'event_start_time', true );
			// $start_time = date( 'F j, Y', strtotime( $start_time ) );

			return $start_date . ' at ' . $start_time;

		} 

		return $date;
		
	}

	function display_event_content($content) {
		
		global $post;
		
		if ( ($post->post_type == 'embee_event') && is_single() && is_main_query() ){
			
			require_once( plugin_dir_path(__FILE__) . 'templates/event-details.php' );	

		} 

		return $content;

	}

	function single_event_template($single_template) {
		global $post;
		
		if ($post->post_type == 'embee_event') {
			if (is_single()) {
				$single_template = plugin_dir_path( __FILE__ ) . 'templates/single-event.php';
	    		return $single_template;
	    	}
	    }
	}
	
	function embee_event_filter_events($query){
		
		// Show events on homepage
		if ( is_home() && $query->is_main_query() ) {

			// $query->set( 'post_type', array('post','embee_event') );

		}

		if ( is_post_type_archive('embee_event') ) {

			if( !is_admin() && $query->is_main_query() ) {

				$query->set( 'meta_key', '_event_start_date_actual' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'ASC' );

				$query->set( 'meta_query', 
					array(
						array(
							'key' => '_event_start_date_actual',
							'value' => strtotime(date('l F d, Y g:i A')),
							'compare' => '>='
						)
					)
				);

			} else if ( is_admin() && $query->is_main_query() ) {

				$query->set( 'meta_key', '_event_start_date_actual' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'DESC' );

			}

		} // endif

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

	function signup_volunteer() {
		global $wpdb;

		$user_id = username_exists( $user_name );
		$user_email = $_REQUEST['user_email'];

		if ( empty($_REQUEST['user_pass']) ) {
			echo json_encode( array( 'success' => false, 'msg' => 'Your password cannot be blank') );
			exit;
		} else if ($_REQUEST['user_pass'] != $_REQUEST['user_pass_retype']) {
			echo json_encode( array( 'success' => false, 'msg' => 'Your passwords don\'t match') );
			exit;
		}

		if ( !$user_id && email_exists($user_email) == false ) {
			$user_params = array(
				'user_pass' => $_REQUEST['user_pass'],
				'user_email' => $_REQUEST['user_email'],
				'user_login' => $_REQUEST['user_email'],
				'first_name' => $_REQUEST['first_name'],
				'last_name' => $_REQUEST['last_name'],
				'display_name' => $_REQUEST['first_name'] . ' ' . $_REQUEST['last_name'],
				'nickname' => $_REQUEST['first_name'] . $_REQUEST['last_name'],
				'user_nicename' => $_REQUEST['first_name'] . $_REQUEST['last_name']
			);

			$user_id = wp_insert_user( $user_params );

			update_user_meta( $user_id, 'user_mobile', $_REQUEST['user_mobile'] );

			return login_volunteer();
		} else {
			echo json_encode( array( 'success' => false, 'msg' => 'This email has already been used') );
			exit;
		}

	}

	function login_volunteer() {

		$creds = array();
		$creds['user_login'] = $_REQUEST['user_email'];
		$creds['user_password'] = $_REQUEST['user_pass'];
		$creds['remember'] = true;

		$user = wp_signon( $creds, false );

		if ( is_wp_error($user) ) {
			echo json_encode( array( 'success' => false, 'msg' => $user->get_error_message() ) );
			exit;
		} 

		return $user;
	}

	function add_event_volunteer() {
		global $wpdb, $current_user;
		
		$table_name = $wpdb->prefix . "events_volunteers";
		$event_id = $_REQUEST['event_id'];
		$user_id = $current_user->ID;

		// Login or Signup user
		if ( isset($_REQUEST['func']) && ($_REQUEST['func'] == 'login_user') ) {	
			$user = login_volunteer();
			$user_id = $user->ID;	

		} else if ( isset($_REQUEST['func']) && ($_REQUEST['func'] == 'register_user') ) {	
			$user = signup_volunteer();
			$user_id = $user->ID;
		}

		// if user logged in then add to volunteer database
		if ( is_user_logged_in() || isset($user) ) {

			$volunteer = $wpdb->get_row("SELECT * FROM $table_name WHERE user_id = $user_id AND event_id = $event_id");

			if (!$volunteer) {
				$entry = array(
					'event_id' => $event_id,
					'user_id' => $user_id,
					'created_at' => current_time('mysql')
				);

				$response = $wpdb->insert( $table_name, $entry );
				echo json_encode( array( 
					'success' => true, 
					'msg' => 'Thank You for volunteering. Be on the lookout for volunteer opportunities'
				) );
				
			} else {
				echo json_encode( array( 'success' => false, 'msg' => 'Thank You for your interest, but you\'re already registered as a volunteer') );	
			}
		} else {
			echo json_encode( array( 'success' => false, 'msg' => 'You must be registered to volunteer') );	 
		}

		exit;
	}

	function get_next_event() {

		$args = array(
			'post_type' => 'embee_event',
			'posts_per_page' => 1,
			'meta_key' => '_event_end_date_actual',
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => '_event_start_date_actual',
					'value' => strtotime(date('l F d, Y g:i A')),
					'compare' => '>='
				)
			) 
		);

		$query = new WP_Query( $args );

		if ( isset($query->posts[0]) ) {

			$event = $query->posts[0];

			$date = get_post_meta( $event->ID, 'event_start_date', true );
			$time = get_post_meta( $event->ID, 'event_start_time', true );

			$event->date = date( 'l F d, Y g:i A', strtotime( $date . ' ' . $time ) );
			$event->time = get_post_meta( $event->ID, 'event_start_time', true );
			$event->permalink = get_permalink( $event->ID );
			$event->ticket_id = get_post_meta( $event->ID, 'ticket_id', true );
			$event->summary = get_the_content( $event->ID );

			if ( $event->ticket_id ) {
				$event->ticket = get_post( $event->ticket_id );
				$event->ticket->meta = get_post_meta( $event->ticket_id );
			}

		} else {
			$event = null;
		}

		return $event;

	}

	add_action( 'init','events_post_type' );
	add_action( 'init','events_install' );

	add_action( 'wp_enqueue_scripts', 'event_load_scripts' );
	add_action( 'admin_menu', 'register_event_submenu_page' );

	add_action( 'add_meta_boxes','event_details_box' );
	add_action( 'save_post','save_event_details' );
	add_filter( 'manage_embee_event_posts_columns','embee_event_add_event_columns' );
	add_action( 'manage_embee_event_posts_custom_column', 'embee_event_add_custom_column', 10, 2 );
	
	// Visual modifications
	//add_filter('single_template','single_event_template');
	add_filter( 'the_content','display_event_content' );
	add_filter( 'get_the_date','embee_event_filter_date' );
	add_filter( 'the_date','embee_event_filter_date' );
	add_action( 'pre_get_posts','embee_event_filter_events' );

	// Ajax
	add_action( 'wp_ajax_add_event_volunteer', 'add_event_volunteer' );
	add_action( 'wp_ajax_nopriv_add_event_volunteer', 'add_event_volunteer' );

?>