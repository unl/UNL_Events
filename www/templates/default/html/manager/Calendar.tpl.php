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
            <?php } else { ?>
                <a href="<?php echo $calendar->getManageURL() ?>"><?php echo $calendar->name ?></a><br>
            <?php } ?>
        <?php } ?>
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
                <div class="wdn-grid-set">
                    <div class="wdn-col-two-thirds">
                        <?php if (count($events['pending']) == 0) { ?>
                            There are no pending events.
                        <?php } ?>
                        <?php foreach($events['pending'] as $event) { ?>
                            <div class="event" style="border: 1px solid black; padding: 5px;">
                            <h6 style="display: inline-block;"><?php echo $event->title ?></h6>
                            <h6 style="float: right;"></h6>
                            </div>
                            <br>
                        <?php } ?>
                    </div>
                    <div class="wdn-col-one-third">
                        <div class="calendar-tools" style="text-align: center; background: #CCCCCC;">
                            <a href="#">Live Calendar</a><br>
                            <a href="#">Account Info</a><br>
                            <a href="#">Calendar Info</a><br>
                            <a href="#">Users &amp; Permissions</a><br>
                            <a href="#">Subscriptions</a><br>
                            <a href="#">InDesign Tags Export</a><br>
                        </div>
                    </div>
                </div>
            </div>
            <div id="posted">
                <div id="posted-toolbar">
                    <?php echo $context->calendar->name ?> <a href="#" class="wdn-button wdn-button-brand">+ New Event</a>
                </div><br>
                <div class="wdn-grid-set">
                    <div class="wdn-col-two-thirds">
                        <?php if (count($events['posted']) == 0) { ?>
                            There are no posted events.
                        <?php } ?>
                        <?php foreach($events['posted'] as $event) { ?>
                            <div class="event" style="border: 1px solid black; padding: 5px;">
                            <h6 style="display: inline-block;"><?php echo $event->title ?></h6>
                            <h6 style="float: right;"></h6>
                            </div>
                            <br>
                        <?php } ?>
                    </div>
                    <div class="wdn-col-one-third">
                        <div class="calendar-tools" style="text-align: center; background: #CCCCCC;">
                            <a href="#">Live Calendar</a><br>
                            <a href="#">Account Info</a><br>
                            <a href="#">Calendar Info</a><br>
                            <a href="#">Users &amp; Permissions</a><br>
                            <a href="#">Subscriptions</a><br>
                            <a href="#">InDesign Tags Export</a><br>
                        </div>
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
                    <div class="wdn-col-one-third">
                        <div class="calendar-tools" style="text-align: center; background: #CCCCCC;">
                            <a href="#">Live Calendar</a><br>
                            <a href="#">Account Info</a><br>
                            <a href="#">Calendar Info</a><br>
                            <a href="#">Users &amp; Permissions</a><br>
                            <a href="#">Subscriptions</a><br>
                            <a href="#">InDesign Tags Export</a><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
