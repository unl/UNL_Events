<?php
    $events = $context->getCategorizedEvents();
    $calendars = $context->getCalendars();
?>
<section class="wdn-grid-set">
    <div class="wdn-col-one-fourth">
        <h3>My Calendars</h3>
        <?php foreach ($calendars as $calendar) { ?>
            <?php if ($calendar->shortname == $context->calendar->shortname) { ?>
                <div style="background: #CCCCCC;"><?php echo $calendar->name ?></div>
                <ul>
                    <li><small><a href="<?php echo $calendar->getFrontendURL() ?>">Live Calendar</a></small></li>
                    <li><small><a href="<?php echo $calendar->getFrontendURL() ?>">Calendar Info</a></small></li>
                    <li><small><a href="<?php echo $calendar->getFrontendURL() ?>">Users &amp; Permissions</a></small></li>
                </ul>
            <?php } else { ?>
                <a href="<?php echo $calendar->getManageURL() ?>"><?php echo $calendar->name ?></a><br>
            <?php } ?>
        <?php } ?>
        <br>
        <a href="#" class="wdn-button wdn-button-brand">+ New Calendar</a>
    </div>
    <div class="wdn-col-three-fourths">
        <ul class="wdn_tabs">
            <li><a href="#pending">Pending (<?php echo count($events['pending']); ?>)</a></li>
            <li><a href="#posted">Posted (<?php echo count($events['posted']); ?>)</a></li>
            <li><a href="#archived">Archived (<?php echo count($events['archived']); ?>)</a></li>
        </ul>
        <div class="wdn_tabs_content">
            <div id="pending">
                <div id="pending-toolbar">
                    <?php echo $context->calendar->name ?> <a href="<?php echo $base_manager_url . $context->calendar->shortname ?>/create/" class="wdn-button wdn-button-brand">+ New Event</a>
                </div><br>
                <?php if (count($events['pending']) == 0) { ?>
                    There are no pending events.
                <?php } else { ?>
                    <select class="all-pending-event-tools">
                        <option value="">Bulk Action</option>
                        <option value="move-to-upcoming">Move to Upcoming</option>
                        <option value="recommend">Recommend</option>
                        <option value="delete">Delete</option>
                    </select>
                    <div class="wdn-grid-set">
                        <div class="wdn-col-one-sixth">
                            <input type="checkbox" id="select-all">
                        </div>
                        <div class="wdn-col-one-sixth">
                            <h6>Title</h6>
                        </div>
                        <div class="wdn-col-one-sixth">
                            <h6>Dates</h6>
                        </div>
                        <div class="wdn-col-one-sixth">
                            <h6>Location</h6>
                        </div>
                    </div>
                    <?php foreach($events['pending'] as $event) { ?>
                    <div class="wdn-grid-set">
                        <div class="wdn-col-one-sixth">
                            <input type="checkbox" class="select-event" data-id="<?php echo $event->id; ?>">
                        </div>
                        <div class="wdn-col-one-sixth">
                            <?php echo $event->title; ?>
                        </div>
                        <div class="wdn-col-one-third">
                            <?php foreach($event->getDateTimes() as $datetime) { ?>
                                <?php echo date('n/j/y @ h:ia', strtotime($datetime->starttime)); ?>
                                <?php echo $datetime->getLocation()->name; ?>
                            <?php } ?>
                        </div>
                        <div class="wdn-col-one-third">
                            <select class="pending-event-tools" data-id="<?php echo $event->id; ?>">
                                <option value="">Select an Action</option>
                                <option value="move-to-upcoming">Move to Upcoming</option>
                                <option value="recommend">Recommend</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <?php } ?>
                <?php } ?>
            </div>
            <div id="upcoming">
                <div id="upcoming-toolbar">
                    <?php echo $context->calendar->name ?> <a href="#" class="wdn-button wdn-button-brand">+ New Event</a>
                </div><br>
                <div class="wdn-grid-set">
                    <div class="wdn-col-two-thirds">
                        <?php if (count($events['posted']) == 0) { ?>
                            There are no upcoming events.
                        <?php } ?>
                        <?php foreach($events['posted'] as $event) { ?>
                            <div class="event" style="border: 1px solid black; padding: 5px;">
                            <h6 style="display: inline-block;"><?php echo $event->title ?></h6>
                            <h6 style="float: right;"></h6>
                            </div>
                            <br>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div id="archived">
                <div id="archived-toolbar">
                    <?php echo $context->calendar->name ?> <a href="#" class="wdn-button wdn-button-brand">+ New Event</a>
                </div><br>
                <div class="wdn-grid-set">
                    <div class="wdn-col-two-thirds">
                        <?php if (count($events['archived']) == 0) { ?>
                            There are no archived events.
                        <?php } ?>
                        <?php foreach($events['archived'] as $event) { ?>
                            <div class="event" style="border: 1px solid black; padding: 5px;">
                            <h6 style="display: inline-block;"><?php echo $event->title ?></h6>
                            <h6 style="float: right;"></h6>
                            </div>
                            <br>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
