<?php 
	
	class Volunteer_Table extends WP_List_Table {
		
		function __construct() {
			parent::__construct( array(
				'singular'=> 'wp_list_text_link', //Singular label
				'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
				'ajax'	=> false //We won't support Ajax for this table
			) );
		}

		function extra_tablenav( $which ) {
			if ( $which == "top" ){
				//The code that goes before the table is here
				echo"Hello, I'm before the table";
			}
			if ( $which == "bottom" ){
				//The code that goes after the table is there
				echo"Hi, I'm after the table";
			}
		}

		function get_columns() {
			$columns = array(
				'first_name' => __('First Name'),
				'last_name' => __('Last Name'),
				'user_email' => __('Email'),
				'user_mobile' => __('Mobile'),
				'date_created' => __('Date Created')
			);

			return $columns;
		}

		function prepare_items() {
			global $wpdb, $_wp_column_headers;
			$screen = get_current_screen();

			/* -- Preparing your query -- */
		        $query = "SELECT * FROM $wpdb->links";

			/* -- Ordering parameters -- */
			    //Parameters that are going to be used to order the result
			    $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
			    $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
			    if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

			/* -- Pagination parameters -- */
		        //Number of elements in your table?
		        $totalitems = $wpdb->query($query); //return the total number of affected rows
		        //How many to display per page?
		        $perpage = 5;
		        //Which page is this?
		        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
		        //Page Number
		        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
		        //How many pages do we have in total?
		        $totalpages = ceil($totalitems/$perpage);
		        //adjust the query to take pagination into account
			    if(!empty($paged) && !empty($perpage)){
				    $offset=($paged-1)*$perpage;
		    		$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
			    }

			/* -- Register the pagination -- */
				$this->set_pagination_args( array(
					"total_items" => $totalitems,
					"total_pages" => $totalpages,
					"per_page" => $perpage,
				) );
				//The pagination links are automatically built according to those parameters

			/* -- Register the Columns -- */
				$columns = $this->get_columns();
				$_wp_column_headers[$screen->id]=$columns;

			/* -- Fetch the items -- */
				$this->items = $wpdb->get_results($query);
		}

	}

	global $wpdb;

	$table_volunteers = $wpdb->prefix . "events_volunteers";
	$table_users = $wpdb->prefix . "users";
	$table_user_meta = $wpdb->prefix . "usermeta";

	$sql = "
		SELECT users.display_name, users.user_email, GROUP_CONCAT( user_meta.meta_key SEPARATOR ' ' ) as meta
		FROM $table_volunteers AS volunteers
		LEFT JOIN $table_users AS users ON users.ID = volunteers.user_id
		LEFT JOIN $wpdb->usermeta AS user_meta ON user_meta.user_id = volunteers.user_id
		WHERE volunteers.event_id = " . $_REQUEST['event_id'] . "
		GROUP BY volunteers.user_id
	";

	$volunteers = $wpdb->get_results($sql); 
?>

<h1>Volunteers</h1>
<ul>
<?php foreach ( $volunteers as $key => $volunteer ): ?>
	<li><?php echo $key+1 ?> <?php echo $volunteer->display_name ?> <?php echo $volunteer->user_email ?> <?php echo $volunteer->user_mobile ?></li>
<?php endforeach; ?>
</ul>