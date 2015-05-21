<?php
    $calendar = $context->calendar;
    $event = $context->event;
    $event_type = $event->getFirstType();
	$nf = new NumberFormatter('en_US', NumberFormatter::ORDINAL);
?>
<?php echo $calendar->name ?> &gt; Edit <?php echo $event->title ?>
<div class="wdn-grid-set">
    <form action="" method="POST">
        <div class="bp1-wdn-col-two-thirds">
            <legend>Details</legend>
            <fieldset>
                <label for="title">Title*</label>
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
            <legend>Location, Date, and Time</legend>
            <fieldset>
            <div>
            	Dates

            	Location
            </div>

            	<?php foreach($event->getDatetimes() as $datetime) { ?>
            	<div>
            		<?php 
				    {
				        if ($datetime->recurringtype == 'none') {
				            echo date('n/d/y @ g:ia', strtotime($datetime->starttime));
				        } else if ($datetime->recurringtype == 'daily') {
				            echo 'Daily @ ' . date('g:ia', strtotime($datetime->starttime)) .
				            	' from ' . date('n/d/y', strtotime($datetime->starttime)) . 
				            	' to ' . date('n/d/y', strtotime($datetime->recurs_until));
				        } else if ($datetime->recurringtype == 'weekly') {
				        	echo 'Weekly @ ' . date('g:ia', strtotime($datetime->starttime)) .
				            	' from ' . date('n/d/y', strtotime($datetime->starttime)) . 
				            	' to ' . date('n/d/y', strtotime($datetime->recurs_until));
				        } else if ($datetime->recurringtype == 'monthly') {
				        	if ($datetime->rectypemonth == 'last') {
				        		echo 'Last day of each month @ ' . date('g:ia', strtotime($datetime->starttime)) . ' from ' . 
					        		date('n/d/y', strtotime($datetime->starttime)) . 
					            	' to ' . date('n/d/y', strtotime($datetime->recurs_until));
				        	} else if ($datetime->rectypemonth == 'date') {
				        		echo $nf->format(date('d', strtotime($datetime->starttime))) . 
				        			'of each month @ ' . date('g:ia', strtotime($datetime->starttime)) . ' from ' . 
				        			date('n/d/y', strtotime($datetime->starttime)) . 
				            		' to ' . date('n/d/y', strtotime($datetime->recurs_until));
				        	} else {
				        		echo ucwords($datetime->rectypemonth) . date('f') . ' of every month' . 
				        			' from ' . date('n/d/y', strtotime($datetime->starttime)) . 
				            		' to ' . date('n/d/y', strtotime($datetime->recurs_until));
				        	}
				        } else if ($datetime->recurringtype == 'annually') {
				        	echo 'Annually @ ' . date('g:ia', strtotime($datetime->starttime)) .
				            	' from ' . date('n/d/y', strtotime($datetime->starttime)) . 
				            	' to ' . date('n/d/y', strtotime($datetime->recurs_until));
				        }
				    }
				    ?>

            		<?php echo $datetime->getLocation()->name ?>

            		<a class="wdn-button wdn-button-brand">Edit</a>
            	</div>
            	<?php } ?>
            </fieldset>
			<a class="wdn-button wdn-button-brand">New Location, Date, and Time</a>            
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