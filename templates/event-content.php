<?php $meta = get_post_meta(get_the_ID()); ?>

<style>.event .wp-post-image { display: none; }</style>

<h2 style="font-weight: 200; font-size: 16px; display: none;">
	<?php echo isset($meta['_event_date'][0]) ? $meta['_event_date'][0] : ''; ?>
	<?php echo isset($meta['_event_start_time'][0]) ? $meta['_event_start_time'][0] : ''; ?>
</h2>

<div id="event-description" style="width: 100%; display; block; margin-bottom: 20px;">
<?php echo get_the_content(); ?>
</div>

<div id="event-details">
	<div style="float: left; width: 55%; margin-right: 25px; display: block;">
		<h3>Location</h3>
		<table>
			<tr>
				<td>Location</td>
				<td><?php echo isset($meta['_event_venue'][0]) ? $meta['_event_venue'][0] : "--" ?></td>
			</tr>
			<tr>
				<td>Address</td>
				<td>
					<?php echo isset($meta['_event_address'][0]) ? $meta['_event_address'][0] : "--" ?><br />
					<?php echo isset($meta['_event_city'][0]) ? $meta['_event_city'][0] : "--" ?>, 
					<?php echo isset($meta['_event_state'][0]) ? $meta['_event_state'][0] : "--" ?> 
					<?php echo isset($meta['_event_zip'][0]) ? $meta['_event_zip'][0] : "--" ?>
				</td>
			</tr>
			<tr>
				<td>Phone</td>
				<td><?php echo isset($meta['_event_phone'][0]) ? $meta['_event_phone'][0] : "--" ?></td>
			</tr>
		</table>
	</div>
	<div style="float: left; width: 40%; display: none;">
		<h3>Cost</h3>
		<p>This event is <u>free</u> and open to the public</p>
		<button>RSVP</button>
	</div>
</div>