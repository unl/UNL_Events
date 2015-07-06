<?php
    $calendar = $context->calendar;
    $event = $context->event;
    $event_type = $event->getFirstType();

    $total_pages = ceil(count($event->getDatetimes()) / 5);

    function ordinal($number) {
    	$mod = $number % 100;
    	if ($mod >= 11 && $mod <= 13) {
    		return $number . 'th';
    	} else if ($mod % 10 == 1) {
    		return $number . 'st';
    	} else if ($mod % 10 == 2) {
    		return $number . 'nd';
    	} else if ($mod % 10 == 3) {
    		return $number . 'rd';
    	} else {
    		return $number . 'th';
    	}
    }

?>

<?php echo $calendar->name ?> &gt; Edit <?php echo $event->title ?>

<?php foreach($event->getDatetimes() as $datetime) : ?>
    <form id="delete-datetime-<?php echo $datetime->id; ?>" class="delete-datetime" method="POST" action="<?php echo $datetime->getDeleteURL($context->calendar) ?>" class="delete-form hidden">
        <input type="hidden" name="event_datetime_id" value="<?php echo $datetime->id ?>" />
    </form>
    <?php if ($datetime->recurringtype != 'none') : ?>
        <?php foreach ($datetime->getRecurrences() as $recurring_date) : ?>
            <form id="delete-datetime-<?php echo $datetime->id; ?>-recurrence-<?php echo $recurring_date->recurrence_id ?>" class="delete-datetime-recurrence" method="POST" action="<?php echo $datetime->getDeleteRecurrenceURL($context->calendar, $recurring_date->recurrence_id) ?>" class="delete-form hidden">
                <input type="hidden" name="event_datetime_id" value="<?php echo $datetime->id ?>" />
                <input type="hidden" name="recurrence_id" value="<?php echo $recurring_date->recurrence_id ?>" />
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endforeach; ?>

<div class="wdn-grid-set">
    <form id="edit-event-form" action="" method="POST">
        <div class="bp1-wdn-col-two-thirds">
            <fieldset>
            	<legend>Details</legend>
                <label for="title"><span class="required">*</span> Title</label>
                <input type="text" id="title" name="title" value="<?php echo $event->title; ?>" />

                <label for="subtitle">Subtitle</label>
                <input type="text" id="subtitle" name="subtitle" value="<?php echo $event->subtitle; ?>" />

                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo $event->description; ?></textarea>

                <label for="type">Type</label>
                <select id="type" name="type">
                <?php foreach ($context->getEventTypes() as $type) { ?>
                    <option <?php if ($event_type->id == $type->id) echo 'selected="selected"'; ?> value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
                <?php } ?>
                </select>
            </fieldset>
            <fieldset class="event-datetimes">
	            <legend>Location, Date, and Time</legend>
	            <div class="edt-header">
                    <div class="dates">
	            	  Dates
                    </div>
                    <div class="location">
	            	  Location
                    </div>
	            </div>

            	<?php foreach($event->getDatetimes(5, ($context->page - 1)*5) as $datetime) : ?>
                	<div class="edt-record <?php if ($datetime->recurringtype != 'none') echo 'has-recurring' ?>">
                        <div class="dates">
                    		<?php 
        				    {
        				        if ($datetime->recurringtype == 'none') {
        				            echo date('n/d/y @ g:ia', strtotime($datetime->starttime));
        				        } else if ($datetime->recurringtype == 'daily' || $datetime->recurringtype == 'weekly' ||
                                        $datetime->recurringtype == 'annually') {
        				            echo ucwords($datetime->recurringtype) . ' @ ' . date('g:ia', strtotime($datetime->starttime)) .
        				            	' from ' . date('n/d/y', strtotime($datetime->starttime)) . 
        				            	' to ' . date('n/d/y', strtotime($datetime->recurs_until));
        				        } else if ($datetime->recurringtype == 'monthly') {
        				        	if ($datetime->rectypemonth == 'lastday') {
        				        		echo 'Last day of each month @ ' . date('g:ia', strtotime($datetime->starttime)) . 
                                            ' from ' . date('n/d/y', strtotime($datetime->starttime)) . 
        					            	' to ' . date('n/d/y', strtotime($datetime->recurs_until));
        				        	} else if ($datetime->rectypemonth == 'date') {
        				        		echo ordinal(date('d', strtotime($datetime->starttime))) . 
        				        			' of each month @ ' . date('g:ia', strtotime($datetime->starttime)) . 
                                            ' from ' . date('n/d/y', strtotime($datetime->starttime)) . 
        				            		' to ' . date('n/d/y', strtotime($datetime->recurs_until));
        				        	} else {
        				        		echo ucwords($datetime->rectypemonth) . date('f', strtotime($datetime->starttime)) . ' of every month' . 
        				        			' from ' . date('n/d/y', strtotime($datetime->starttime)) . 
        				            		' to ' . date('n/d/y', strtotime($datetime->recurs_until));
        				        	}
        				        }
        				    }
        				    ?>
                        </div>
                        <div class="location with-controls">
                		  <?php echo $datetime->getLocation()->name; ?>
                        </div>
                        <div class="controls">
                    		<a href="<?php echo $datetime->getEditURL($context->calendar); ?>" class="wdn-button wdn-button-brand small">Edit</a>
                            <button class="small" form="delete-datetime-<?php echo $datetime->id; ?>" type="submit">Delete</button>
                        </div>
                	</div>
                    <?php if ($datetime->recurringtype != 'none') : ?>
                    <div>
                        <?php foreach ($datetime->getRecurrences() as $recurring_date) : ?>
                            <div class="recurring-record">
                                <div class="edt-record recurring">
                                    <div class="dates recurring">
                                        <?php echo date('n/d/y', strtotime($recurring_date->recurringdate)) . ' @ ' . date('g:ia', strtotime($datetime->starttime)); ?>
                                    </div>
                                    <div class="controls recurring">
                                        <a href="<?php echo $datetime->getEditRecurrenceURL($context->calendar, $recurring_date->recurrence_id); ?>" class="wdn-button wdn-button-brand small edit-recurring-edt">Edit</a>
                                        <button type="submit" form="delete-datetime-<?php echo $datetime->id ?>-recurrence-<?php echo $recurring_date->recurrence_id ?>" class="small delete-datetime-recurrence">Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

            	<?php endforeach; ?>

                <?php if ($total_pages > 1): ?>
                <script type="text/javascript">
                WDN.loadCSS(WDN.getTemplateFilePath('css/modules/pagination.css'));
                </script>
                <div style="text-align: center;">
                    <div style="display: inline-block;">
                        <ul id="pending-pagination" class="wdn_pagination" data-tab="pending" style="padding-left: 0;">
                            <?php if($context->page != 1): ?>
                                <li class="arrow prev"><a href="?page=<?php echo $context->page - 1 ?>" title="Go to the previous page">← prev</a></li>
                            <?php endif; ?>
                            <?php $before_ellipsis_shown = FALSE; $after_ellipsis_shown = FALSE; ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <?php if ($i == $context->page): ?>
                                        <li class="selected"><span><?php echo $i; ?></span></li>
                                    <?php elseif ($i <= 3 || $i >= $total_pages - 2 || $i == $context->page - 1 || 
                                                $i == $context->page - 2 || $i == $context->page + 1 || $i == $context->page + 2): ?>
                                        <li><a href="?page=<?php echo $i ?>" title="Go to page <?php echo $i; ?>"><?php echo $i; ?></a></li>
                                    <?php elseif ($i < $context->page && !$before_ellipsis_shown): ?>
                                        <li><span class="ellipsis">...</span></li>
                                        <?php $before_ellipsis_shown = TRUE; ?>
                                    <?php elseif ($i > $context->page && !$after_ellipsis_shown): ?>
                                        <li><span class="ellipsis">...</span></li>
                                        <?php $after_ellipsis_shown = TRUE; ?>
                                    <?php endif; ?>
                            <?php endfor; ?>
                            <?php if($context->page != $total_pages): ?>
                                <li class="arrow next"><a href="?page=<?php echo $context->page + 1 ?>" title="Go to the next page">next →</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            </fieldset>
			<a class="wdn-button wdn-button-brand" href="<?php echo $event->getAddDatetimeURL($context->calendar) ?>">Add Location, Date, and/or Time</a>            
        </div>
        <div class="bp1-wdn-col-one-third">
            <div class="visual-island">
                <div class="vi-header">
                    Sharing
                </div>
                <p>
                    <input <?php if (!$event->approvedforcirculation) echo 'checked="checked"' ?> type="radio" value="private" name="private_public" id="sharing-private"> 
                    <label for="sharing-private">Private</label> 
                    <br>
                
                    <input <?php if ($event->approvedforcirculation) echo 'checked="checked"' ?> type="radio" value="public" name="private_public" id="sharing-public"> 
                    <label for="sharing-public">Public</label> 
                    <br>

                    <input <?php if ($context->on_main_calendar) echo 'checked="checked"'; ?> type="checkbox" name="send_to_main" id="send-to-main"> 
                    <label for="send-to-main">Consider for main calendar</label>
                </p>
            </div>

            <div class="visual-island">
                <div class="vi-header">
                    Contact Info
                </div>

                <p>
                    <label for="contact-name">Name</label>
                    <input value="<?php echo $event->listingcontactname; ?>" type="text" id="contact-name" name="contact_name" />

                    <label for="contact-phone">Phone</label>
                    <input value="<?php echo $event->listingcontactphone; ?>" type="text" id="contact-phone" name="contact_phone" />

                    <label for="contact-email">Email</label>
                    <input value="<?php echo $event->listingcontactemail; ?>"type="text" id="contact-email" name="contact_email" />

                    <label for="website">Event Website</label>
                    <input value="<?php echo $event->webpageurl; ?>" type="text" id="website" name="website" />
                </p>
            </div>
        </div>
        <div class="bp1-wdn-col-two-thirds">
            <button class="wdn-button wdn-button-brand wdn-pull-left" type="submit">Save Event</button>
        </div>
    </form>
</div>

<script type="text/javascript">
require(['jquery'], function($) {
    $('.delete-datetime').submit(function (submit) {
        if (!window.confirm('Are you sure you want to delete this location, date, and time?')) {
            submit.preventDefault();
        }
    });

    $('.delete-datetime-recurrence').submit(function (submit) {
        if (!window.confirm('Are you sure you want to delete instance of your recurring event? The rest of the recurrences will remain.')) {
            submit.preventDefault();
        }
    });

    $('.edit-recurring-edt').click(function (click) {
        if (!window.confirm('You are editing a single instance of a recurring location, date, and time.')) {
            click.preventDefault();
        }
    });

    $('#edit-event-form').submit(function (submit) {
        // validate required fields
        if ($('#title').val() == '') {
            notifier.mark_input_invalid($('#title'));
            notifier.alert('Sorry! We couldn\'t edit your event', '<a href="#title">Title</a> is required.');
            submit.preventDefault();
        }
    });
});

</script>