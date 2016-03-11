<?php $upcoming_event = Ambassador_Events::get_next_event(); ?>

<div id="upcoming-event" class="container-fluid">
   <div class="container">
      <div class="row">
         <?php if ( $upcoming_event && ( current_time('timestamp') < $upcoming_event->timestamp ) ): ?>
            <section>
               <div id="date-time" class="col-sm-5 col-md-5">
                  <h4>Next Upcoming Event</h4>
                  <h2 class="event-title"><a href="<?php echo $upcoming_event->permalink ?>"><?php echo $upcoming_event->post_title ?></a></h2>
                  <span class="event-location"><?php brand_show_event_location( $upcoming_event->ID ); ?></span>
                  <span class="event-date"><?php echo $upcoming_event->date_formatted ?></span>
                  <p><?php $upcoming_event->excerpt; ?></p>
               </div>
               <div id="countdown" class="col-md-4 col-sm-4 col-xs-12">
                  <h4>Event Begins In</h4>
                  <div class="event-countdown event-countdown-small" data-date="<?php echo $upcoming_event->mysql ?>" style="display: none;">
                     <div class="timer-col"><span id="days" class="label label-primary"></span><span class="timer-type">days</span></div>
                     <div class="timer-col"><span id="hours" class="label label-default"></span><span class="timer-type">hrs</span></div>
                     <div class="timer-col"><span id="minutes" class="label label-default"></span><span class="timer-type">mins</span></div>
                     <div class="timer-col"><span id="seconds" class="label label-default"></span><span class="timer-type">secs</span></div>
                  </div>
               </div>
            </section>
            <div id="upcoming-event-permalinks" class="col-md-12 col-sm-12 col-xs-12 ">
               <span><a type="button" class="button medium btn btn-primary btn-lg base-color" href="<?php echo get_permalink( $upcoming_event->ID ) ?>">View Event Details</a><a href="<?php echo home_url(); ?>/events" class="btn btn-link view-all">View all events</a></span>
            </div>
         <?php elseif ( $upcoming_event && ( $upcoming_event->timestamp < current_time('timestamp') ) ): ?>

            <div class="col-sm-6 col-md-6">
               <h4>Happening Now</h4>
               <h2>
                  <a href="<?php echo $upcoming_event->permalink ?>"><?php echo $upcoming_event->post_title ?></a>
                  <br /><small><?php echo $upcoming_event->date_formatted ?></small>
               </h2>
               <span>Through <?php echo $upcoming_event->end_date_formatted ?></span>
               <p><?php $upcoming_event->excerpt; ?></p>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12 ">
               <span><a type="button" class="button btn btn-primary btn-lg" style="margin-top: 29px;" href="<?php echo get_permalink( $upcoming_event->ID ) ?>">View Event Details</a><a href="<?php echo home_url(); ?>/events" class="btn btn-link">View all events</a></span>
            </div>

         <?php endif; ?>

      </div>
   </div>
</div><!-- #upcoming-event -->
