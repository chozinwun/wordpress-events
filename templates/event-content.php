<?php 
	global $wpdb;

	$post_meta = get_post_meta(get_the_ID());
	$blog_title = get_bloginfo();
	$table_name = $wpdb->prefix . "events_volunteers";

	$volunteer = $wpdb->get_row("SELECT * FROM $table_name WHERE user_id = " . get_current_user_id() . " AND event_id = " . get_the_ID() );
?>

<style>.event .wp-post-image { display: none; }</style>

<h2 style="font-weight: 200; font-size: 16px; display: none;">
	<?php echo isset($post_meta['_event_date'][0]) ? $post_meta['_event_date'][0] : ''; ?>
	<?php echo isset($post_meta['_event_start_time'][0]) ? $post_meta['_event_start_time'][0] : ''; ?>
</h2>

<div id="event-description" style="width: 100%; display; block; margin-bottom: 20px;">
<?php echo get_the_content(); ?>
</div>

<div id="event-details">
	<div class="left">
		<h3>Location</h3>
		<table>
			<tr>
				<td>Location</td>
				<td><?php echo isset($post_meta['_event_venue'][0]) ? $post_meta['_event_venue'][0] : "--" ?></td>
			</tr>
			<tr>
				<td>Address</td>
				<td>
					<?php echo isset($post_meta['_event_address'][0]) ? $post_meta['_event_address'][0] : "--" ?><br />
					<?php echo isset($post_meta['_event_city'][0]) ? $post_meta['_event_city'][0] : "--" ?>, 
					<?php echo isset($post_meta['_event_state'][0]) ? $post_meta['_event_state'][0] : "--" ?> 
					<?php echo isset($post_meta['_event_zip'][0]) ? $post_meta['_event_zip'][0] : "--" ?>
				</td>
			</tr>
			<tr>
				<td>Phone</td>
				<td><?php echo isset($post_meta['_event_phone'][0]) ? $post_meta['_event_phone'][0] : "--" ?></td>
			</tr>
		</table>
	</div>
	<div class="right">
		<?php if ( isset($post_meta['_event_price'][0]) && !empty($post_meta['_event_price']) ): ?>
			<h3>Price</h3>
			<h1 class="price"><?php echo isset($post_meta['_event_price'][0]) ? $post_meta['_event_price'][0] : "--" ?></h1>
			<p><?php echo isset($post_meta['_event_price_notes']) ? $post_meta['_event_price_notes'][0] : '' ?></p>
		<?php endif; ?>
	</div>
</div>

<?php if ( isset($post_meta['_event_allow_volunteers']) && ( $post_meta['_event_allow_volunteers'][0] ) ): ?>
	
	<div id="volunteer-box">
		<h2>Volunteer Opportunities</h2>

		<?php if ( $volunteer ): ?>
			<p>You've already signed up to volunteer at this event. If you haven't heard from a representative, please email <a href="mailto:thesummit@newjc.org">thesummit@newjc.org</a>.</p>
		<?php elseif ( is_user_logged_in() ): ?>
			<p><a class="volunteer-button" data-event-id="<?php the_ID(); ?>">Volunteer</a></p>
		<?php else: ?>
			<p>To volunteer, <a href="?show=signup">signup</a> or <a href="?show=login">login</a>.</p>
		<?php endif; ?>
	</div>

<?php endif; ?>