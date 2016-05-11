<?php
/*
Plugin Name: Embee Events
Description: Simple Events for WordPress
Version: 0.1
License: GPL
Author: Marcus Battle
Author URI: http://marcusbattle.com
*/

class Ambassador_Events {

	protected static $single_instance = null;

	static function init() {

		if ( self::$single_instance === null ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;

	}

	public function __construct() {

		if ( ! class_exists('CMB2_Bootstrap_220') ) {
			include_once 'includes/third-party/CMB2/init.php';
		}

		include_once 'includes/functions.php';
		include_once 'includes/shortcodes.php';

		$this->shortcode = new Ambassador_Events_Shortcode();

	}

	public function hooks() {

		add_action( 'init', array( $this, 'events_post_type' ) );
		add_action( 'init', array( $this, 'register_location_taxonomy' ) );
		add_action( 'admin_menu', array( $this, 'hide_location_taxonomy_metabox' ) );

		add_action( 'parse_request', array( $this, 'modify_event_query' ) );
		add_filter( 'post_type_link', array( $this, 'event_permalink' ), 10, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'save_post', array( $this, 'save_event_details' ), 20, 2 );

		add_action( 'cmb2_admin_init', array( $this, 'init_event_details_metabox' ) );

		add_filter( 'manage_embee_event_posts_columns', array( $this, 'add_event_columns' ) );
		add_action( 'manage_embee_event_posts_custom_column', array( $this, 'add_custom_column' ), 10, 2 );

		// // Visual modifications
		// //add_filter('single_template','single_event_template');
		// add_filter( 'the_content','display_event_content' );
		add_filter( 'get_the_date', array( $this, 'embee_event_filter_date' ) );
		add_filter( 'the_date', array( $this, 'embee_event_filter_date' ) );
		add_action( 'pre_get_posts', array( $this, 'filter_event_query' ) );

		add_filter( 'ambassador_section_types', array( $this, 'add_upcoming_event_section_type' ) );
		add_action( 'show_upcoming_event_section', array( $this, 'show_upcoming_event_section' ) );

		$this->shortcode->hooks();

	}

	public function events_post_type() {

		global $wp_rewrite;

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
			'supports'      => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'   => true,
			'show_in_nav_menus' => true,
			'rewrite' 			=> array( 'slug' => 'events' ),
			'capability_type' => 'page',
			'hierarchical'	=> false,
			'publicly_queryable' => true,
			'query_var' => true,
			'can_export' => true
		);

		register_post_type( 'ambassador_event', $args );

		$event_rewrite = '/events/%year%/%monthnum%/%ambassador_event%';

		add_rewrite_tag( '%ambassador_event%', '([^/]+)' );
		add_permastruct( 'ambassador_event', $event_rewrite, false );

		// $this->install_pages();

	}

	public function register_location_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Locations', 'taxonomy general name' ),
			'singular_name'              => _x( 'Location', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Locations' ),
			'popular_items'              => __( 'Popular Locations' ),
			'all_items'                  => __( 'All Locations' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Location' ),
			'update_item'                => __( 'Update Location' ),
			'add_new_item'               => __( 'Add New Location' ),
			'new_item_name'              => __( 'New Location Name' ),
			'separate_items_with_commas' => __( 'Separate locations with commas' ),
			'add_or_remove_items'        => __( 'Add or remove locations' ),
			'choose_from_most_used'      => __( 'Choose from the most used locations' ),
			'not_found'                  => __( 'No locations found.' ),
			'menu_name'                  => __( 'Locations' ),
		);

		$args = array(
			'hierarchical'          => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'location' ),
		);

		register_taxonomy( 'location', 'ambassador_event', $args );

	}

	public function hide_location_taxonomy_metabox() {
		remove_meta_box( 'locationdiv', 'ambassador_event', 'side' );
	}

	// Adapted from get_permalink function in wp-includes/link-template.php
	public function event_permalink( $permalink, $post ) {

	    if ( 'ambassador_event' == get_post_type( $post ) ) {

	    	$date_vars = array( '%year%', '%monthnum%' );

	    	$start_date = get_post_meta( $post->ID, '_ambassador_event_start_date', true );

	    	$dates[] = date('Y', strtotime( $start_date ) );
	    	$dates[] = date('m', strtotime( $start_date ) );

	    	$permalink = str_replace( $date_vars, $dates, $permalink );

	    }

	    return $permalink;

	}

	public function load_scripts() {

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

	public function init_event_details_metabox() {

		$prefix = '_ambassador_event_';

		$event_details_box = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => __( 'Event Details', 'ambassador' ),
			'object_types'  => array( 'ambassador_event', ),
			'context'    => 'normal',
			'show_names' => true,
		) );

		$event_details_box->add_field( array(
			'name' => __( 'Event Start Date', 'ambassador' ),
			'id'   => $prefix . 'start_date',
			'type' => 'text_date',
		) );

		$event_details_box->add_field( array(
			'name' => __( 'Event Start Time', 'ambassador' ),
			'id'   => $prefix . 'start_time',
			'type' => 'text_time',
		) );

		$event_details_box->add_field( array(
			'name' => __( 'Event End Date', 'ambassador' ),
			'id'   => $prefix . 'end_date',
			'type' => 'text_date',
		) );

		$event_details_box->add_field( array(
			'name' => __( 'Event End Time', 'ambassador' ),
			'id'   => $prefix . 'end_time',
			'type' => 'text_time',
		) );

		$event_details_box->add_field( array(
			'name'     => __( 'Event Location', 'ambassador' ),
			'desc'     => __( 'Select an event location. If none, present, create one in the menu', 'ambassador' ),
			'id'       => $prefix . 'location',
			'type'     => 'taxonomy_select',
			'taxonomy' => 'location', // Taxonomy Slug
		) );

	}

	public function add_event_columns( $columns = array() ) {

		unset( $columns['title'] );
		unset( $columns['date'] );

		return array_merge( $columns,
			array(
				'title' => __('Event Name'),
				'start_date' => __('Start Date'),
				'end_date' => __('End Date'),
				'location' => __('Location'),
	      	)
	    );

	}

	public function add_custom_column( $column, $post_id ) {

		global $wpdb;

		switch ( $column ) {
	      	case 'start_date':
	      		echo get_post_meta( $post_id , '_ambassador_event_start_date' , true ) . ' ' . get_post_meta( $post_id, '_ambassador_event_start_time', true );
	      		break;
	      	case 'end_date':
	      		echo get_post_meta( $post_id , '_ambassador_event_end_date' , true ) . ' ' . get_post_meta( $post_id, '_ambassador_event_end_time', true );
	      		break;
	      	case 'location':
	        	break;
	    }

	}

	function save_event_details( $post_id ) {

		if ( isset( $_POST['_ambassador_event_start_date'] ) ){

			$start_date = strtotime( $_POST['_ambassador_event_start_date'] );

			update_post_meta( $post_id, '_ambassador_event_start_date', date( 'Y-m-d', $start_date ) );
			update_post_meta( $post_id, '_ambassador_event_start_date_actual', strtotime( $_REQUEST['_ambassador_event_start_date'] . ' ' . $_REQUEST['_ambassador_event_start_time'] ) );

	    }

	    if ( isset( $_POST['_ambassador_event_end_date'] ) ){

	    	$end_date = strtotime( $_POST['_ambassador_event_end_date'] );

			update_post_meta( $post_id, '_ambassador_event_end_date', date( 'Y-m-d', $end_date ) );
			update_post_meta( $post_id, '_ambassador_event_end_date_actual', strtotime( $_REQUEST['_ambassador_event_end_date'] . ' ' . $_REQUEST['_ambassador_event_end_time'] ) );
	    }

	}

	public function embee_event_filter_date( $date ) {

		global $post;

		if ( $post->post_type == 'ambassador_event' ) {

			$start_date = get_post_meta( $post->ID, '_ambassador_event_start_date', true );
			$start_date = date( 'l F j, Y', strtotime( $start_date ) );

			$start_time = get_post_meta( $post->ID, '_ambassador_event_start_time', true );
			// $start_time = date( 'F j, Y', strtotime( $start_time ) );

			return $start_date . ' at ' . $start_time;

		}

		return $date;

	}

	function display_event_content($content) {

		global $post;

		if ( ($post->post_type == 'embee_event') && is_single() && is_main_query() ){

			ob_start();  // start buffer

		   	include( plugin_dir_path(__FILE__) . 'templates/event-details.php' );  // read in buffer

		   	$content .= ob_get_contents();  // get buffer content
		   	ob_end_clean();  // delete buffer content

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

	public function filter_event_query( $query ) {

		if ( is_post_type_archive('ambassador_event') ) {

			if ( ! is_admin() && $query->is_main_query() ) {

				$query->set( 'meta_key', '_ambassador_event_start_date_actual' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'ASC' );

				$query->set( 'meta_query',
					array(
						array(
							'key' => '_ambassador_event_start_date_actual',
							'value' => strtotime( date('l F d, Y g:i A') ),
							'compare' => '>='
						)
					)
				);

			} else if ( is_admin() && $query->is_main_query() ) {

				$query->set( 'meta_key', '_ambassador_event_start_date_actual' );
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'order', 'DESC' );

			}

		} // endif

	}

	static public function get_next_event() {

		$args = array(
			'post_type' => 'ambassador_event',
			'posts_per_page' => 1,
			'order'	=> 'ASC',
			'orderby' => 'meta_value',
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => '_ambassador_event_end_date',
					'compare' => '>=',
					'value' => current_time( 'Y-m-d' ),
					// 'type'	=> 'DATE'
				),
			)
		);

		$query = new WP_Query( $args );

		$upcoming_event = null;

		if ( isset($query->posts[0]) ) {

			foreach ( $query->posts as $index => $event ) {

				$upcoming_event = $event;

				$start_date = get_post_meta( $event->ID, '_ambassador_event_start_date', true );
				$start_time = get_post_meta( $event->ID, '_ambassador_event_start_time', true );

				$end_date = get_post_meta( $event->ID, '_ambassador_event_end_date', true );
				$end_time = get_post_meta( $event->ID, '_ambassador_event_end_time', true );

				$upcoming_event->timestamp = strtotime( $start_date . ' ' . $start_time );
				$upcoming_event->mysql = date( 'Y/m/d H:i:s', $upcoming_event->timestamp );

				$upcoming_event->date_formatted = date( 'l F jS, Y g:iA', strtotime( $start_date . ' ' . $start_time ) );
				$upcoming_event->end_date_formatted = date( 'l F jS', strtotime( $end_date ) );

				$upcoming_event->time = $start_time;
				$upcoming_event->permalink = get_permalink( $event->ID );
				$upcoming_event->summary = get_the_content( $event->ID );

			}

		}

		return $upcoming_event;

	}

	public function install_pages() {

		$pages = array(
			'events' => __( 'Events', 'ambassador-events' ),
		);

		foreach ( $pages as $page => $page_title ) {

			$page_exists = get_page_by_title( $page_title, OBJECT, 'page' );

			if ( $page_exists ) {
				continue;
			}

			$page_args = array(
				'post_type'		=> 'page',
				'post_title'	=> $page_title,
				'post_name'		=> $page,
				'post_status'	=> 'publish'
			);

			$page_id = wp_insert_post( $page_args );

		}

	}

	public function modify_event_query( $query ) {

		if ( isset( $query->query_vars['post_type'] ) ) {

			if ( 'ambassador_event' == $query->query_vars['post_type'] ) {

				unset( $query->query_vars['year'] );
				unset( $query->query_vars['monthnum'] );

			}

		}

		return $query;

	}

	public function add_upcoming_event_section_type( $section_types = array() ) {

		$section_types['upcoming_event'] = 'Upcoming Event';

		return $section_types;

	}

	public function show_upcoming_event_section( $section_id ) {

		$height = get_post_meta( $section_id, '_ambassador_section_height', true );
		$background_url = get_post_meta( $section_id, '_ambassador_section_background', true );

		$html = '<section class="upcoming-event" style="min-height: ' . $height . 'px;">';
		$html .= '<div class="content align-text-center">' . do_shortcode('[upcoming_event]') . '</div>';
		// $html .= '<div class="background" style="background-image: url(' . $background_url . ');"></div>';
		$html .= '</section>';

		echo $html;

	}

}

add_action( 'plugins_loaded', array( Ambassador_Events::init(), 'hooks' ) );
