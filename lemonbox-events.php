<?php
	/*
	Plugin Name: Lemonbox Events
	Description: Events Custom Post Type
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
			'menu_position' => 31,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes'),
			'has_archive'   => true,
			'show_in_nav_menus' => true,
			'rewrite' 			=> array( 'slug' => 'events' ),
			'capability_type' => 'page',
			'hierarchical'	=> true,
			'publicly_queryable' => true,
			'query_var' => true,
			'can_export' => true
		);

		register_post_type('lemonbox_event',$args);
	}

	function event_load_scripts() {
		wp_register_script('wp-events-js',plugins_url('/assets/js/lemonbox-events.js',__FILE__),array('jquery'),false,true);
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
		add_meta_box( 'event_zip_box', __('Event Details'), 'event_details_box_content', 'lemonbox_event', 'normal', 'high' );
	}
	
	function event_details_box_content($post) {
		$meta = get_post_meta($post->ID);

		$price = isset($meta['_event_price']) ? $meta['_event_price'][0] : '';
		$price_notes = isset($meta['_event_price_notes']) ? $meta['_event_price_notes'][0] : '';

		echo '<p><strong>Details</strong></p>';
		echo '<label>Date</label><br /> <input name="_event_start_date" value="' . $meta['_event_start_date'][0] . '" /><br />';
		echo '<label>Time</label><br /> <input name="_event_start_time" value="' . $meta['_event_start_time'][0] . '" /><br />';
		echo '<p><strong>Price</strong></p>';
		echo "<label>Price</label><br /> <input name=\"_event_price\" value=\"$price\" /><br />";
		echo "<label>Price Notes</label><br /> <textarea name=\"_event_price_notes\">$price_notes</textarea><br />";
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

	    if (isset($_REQUEST['_event_price'])) {
			update_post_meta($post_id, '_event_price', $_REQUEST['_event_price']);
	    }

	    if (isset($_REQUEST['_event_price_notes'])) {
			update_post_meta($post_id, '_event_price_notes', $_REQUEST['_event_price_notes']);
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
				'start_date' => __('Date'),
				'location' => __('Location'),
				'total_rsvps' =>__( 'RSVPs'),
	      		'total_volunteers' =>__( 'Volunteers')
	      	)
	    );
	}
	
	function custom_event_column($column,$post_id) {
		global $wpdb;
		$table_name = $wpdb->prefix . "events_volunteers";

		switch ( $column ) {
	      	case 'start_date':
	      		echo get_post_meta( $post_id , '_event_start_date' , true );
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
	
	function display_event_title($title) {
		
		/* global $post;

		if (($post->post_type == 'lemonbox_event') && in_the_loop()){
			$meta = get_post_meta(get_the_ID());
			$date = "";
			$time = "";
			
			if (isset($meta['_event_start_date'][0])) $date = $meta['_event_start_date'][0];
			if (isset($meta['_event_start_time'][0])) $time = $meta['_event_start_time'][0];
			
			$event_title = $title . "<br /><small style=\"font-weight: 200;\">$date $time</small>";
			return $event_title;	
		} else {
			return $title;
		} */

		return $title;

	}

	function display_event_content($content) {
		global $post;
		
		if ( ($post->post_type == 'lemonbox_event') && is_single() && is_main_query() ){
			
			if ( isset($_REQUEST['show']) &&  ($_REQUEST['show'] == 'signup') ) {
				require_once ( plugin_dir_path(__FILE__) . 'templates/event-signup.php' );	

			} else if ( isset($_REQUEST['show']) &&  ($_REQUEST['show'] == 'login') ) {
				require_once ( plugin_dir_path(__FILE__) . 'templates/event-login.php' );

			} else {
				require_once( plugin_dir_path(__FILE__) . 'templates/event-content.php' );	
			}

		} elseif (is_archive()) {
			include plugin_dir_path(__FILE__) . 'templates/event-archive.php';
			return $post->post_excerpt;

		} else {
			return $content;
		}

	}

	function single_event_template($single_template) {
		global $post;
		
		if ($post->post_type == 'lemonbox_event') {
			if (is_single()) {
				$single_template = plugin_dir_path( __FILE__ ) . 'templates/single-event.php';
	    		return $single_template;
	    	}
	    }
	}
	
	function filter_events($query){
		
		// Show events on homepage
		if ( is_home() && $query->is_main_query() ) {

			$query->set( 'post_type', array('post','lemonbox_event') );

		}

		if(is_post_type_archive('lemonbox_event')){
			if(!is_admin() && $query->is_main_query()) {

				query_posts(array(
					'post_type' => 'lemonbox_event',
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
			'post_type' => 'lemonbox_event',
			'posts_per_page' => 1,
			'orderby' => 'meta_value',
			'meta_key' => '_event_start_date_actual',
			'order' => 'ASC'
		);

		$query = new WP_Query( $args );

		if ( isset($query->posts) ) {
			
			$event = $query->posts[0];
			$event->date = date( 'F d, Y', strtotime(get_post_meta( $event->ID, '_event_start_date', true )) );
			$event->time = get_post_meta( $event->ID, '_event_start_time', true );
			$event->permalink = get_permalink( $event->ID );

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
	add_filter( 'manage_event_posts_columns','add_event_columns' );
	add_action( 'manage_event_posts_custom_column', 'custom_event_column', 10, 2 );
	
	// Visual modifications
	//add_filter('single_template','single_event_template');
	add_filter( 'the_content','display_event_content' );
	add_filter( 'the_title','display_event_title' );
	add_action( 'pre_get_posts','filter_events' );

	// Ajax
	add_action( 'wp_ajax_add_event_volunteer', 'add_event_volunteer' );
	add_action( 'wp_ajax_nopriv_add_event_volunteer', 'add_event_volunteer' );
?>