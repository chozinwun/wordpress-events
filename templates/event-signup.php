<?php 
	global $wpdb;

	$blog_title = get_bloginfo();
	$table_name = $wpdb->prefix . "events_volunteers";
?>

<h2>Volunteer Signup</h2>

<?php if ( !is_user_logged_in() ): ?>
	<p>To volunteer for this event, please complete the form below. We will not share your information without your permission.</p>
	<p>If you aready have an account with <?php echo $blog_title; ?>, you may <a href="?show=login">login now</a> to volunteer.</p>

	<form id="volunteer-signup" class="volunteer-form">
		<input name="action" value="add_event_volunteer" type="hidden" />
		<input name="func" value="register_user" type="hidden" />
		<input name="event_id" value="<?php echo get_the_id(); ?>" type="hidden" />
		<ul>
			<li>
				<label>First Name</label>
				<input name="first_name" type="text" />
			</li>
			<li>
				<label>Last Name</label>
				<input name="last_name" type="text" />
			</li>
			<li>
				<label>Email</label>
				<input name="user_email" type="text" />
			</li>
			<li>
				<label>Mobile #</label>
				<input name="user_mobile" type="text" />
			</li>
			<li>
				<label>Password</label>
				<input name="user_pass" type="password" />
			</li>
			<li>
				<label>Re-type Password</label>
				<input name="user_pass_retype" type="password" />
			</li>
			<li>
				<input type="submit" value="Register &amp; Volunteer" />
			</li>
		</ul>
	</form>
<?php else: ?>
	<?php $volunteer = $wpdb->get_row("SELECT * FROM $table_name WHERE user_id = " . get_current_user_id() . " AND event_id = " . get_the_ID() ); ?>
	<?php if ( $volunteer ): ?>
		<p>You've already signed up to volunteer at this event. If you haven't heard from a representative, please email <a href="mailto:thesummit@newjc.org">thesummit@newjc.org</a>.</p>
	<?php else: ?>
		<p><button class="volunteer-button" data-event-id="<?php the_ID(); ?>">Volunteer</button></p>
	<?php endif; ?>
<?php endif; ?>

<p><a href="<?php echo $_SERVER['REDIRECT_URL'] ?>">Back to <?php echo get_the_title(); ?></a></p>