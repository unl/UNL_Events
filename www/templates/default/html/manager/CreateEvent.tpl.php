<?php
    $calendar = $context->calendar;
    $event = $context->event;
    $post = $context->post;
?>
<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        "Create Event" => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<br>
<div class="wdn-grid-set">
    <form id="create-event-form" action="" method="POST" enctype="multipart/form-data">
        <div class="bp3-wdn-col-two-thirds">
            <fieldset>
                <legend style="margin-top: 0">Event Details</legend>
                <label for="title"><span class="required">*</span> Title</label>
                <input type="text" id="title" name="title" value="<?php echo $event->title; ?>" />

                <label for="subtitle">Subtitle</label>
                <input type="text" id="subtitle" name="subtitle" value="<?php echo $event->subtitle; ?>" />

                <label for="description">Description</label>
                <textarea rows="4" id="description" name="description"><?php echo $event->description; ?></textarea>

                <label for="type">Type</label>
                <select id="type" name="type" class="use-select2" style="width: 100%;">
                <?php foreach ($context->getEventTypes() as $type) { ?>
                    <option <?php if ($post['type'] == $type->id) echo 'selected="selected"' ?> value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
                <?php } ?>
                </select>
            </fieldset>

            <fieldset>
            <legend style="font-size: 1.6em">Location, Date, and Time</legend>
                <label for="location"><span class="required">*</span> Location</label>
                <select id="location" name="location" class="use-select2" style="width: 100%;">
                    <optgroup label="Your saved locations">
                        <?php foreach ($context->getUserLocations() as $location): ?>
                            <option <?php if ($post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                        <?php endforeach ?>
                        <option <?php if ($post['location'] == 'new') echo 'selected="selected"' ?>value="new">-- New Location --</option>
                    </optgroup>
                    <optgroup label="UNL Campus locations">
                        <?php foreach ($context->getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_MAIN) as $location): ?>
                            <option <?php if ($post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                        <?php endforeach ?>
                    </optgroup>
                    <optgroup label="Extension locations">
                        <?php foreach ($context->getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_EXTENSION) as $location): ?>
                            <option <?php if ($post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                        <?php endforeach ?>
                    </optgroup>
                </select>

                <div id="new-location-fields" style="display: none;">
                    <h6>New Location</h6>
                    <label for="location-name"><span class="required">*</span> Name</label>
                    <input type="text" id="location-name" name="new_location[name]" value="<?php echo $post['new_location']['name']; ?>">

                    <label for="location-address-1">Address</label>
                    <input type="text" id="location-address-1" name="new_location[streetaddress1]" value="<?php echo $post['new_location']['streetaddress1']; ?>">

                    <label for="location-address-2">Address 2</label>
                    <input type="text" id="location-address-2" name="new_location[streetaddress2]" value="<?php echo $post['new_location']['streetaddress2']; ?>">

                    <label for="location-room">Room</label>
                    <input type="text" id="location-room" name="new_location[room]" value="<?php echo $post['new_location']['room']; ?>">

                    <label for="location-city">City</label>
                    <input type="text" id="location-city" name="new_location[city]" value="<?php echo $post['new_location']['city']; ?>">

                    <label for="location-state">State</label>
                    <input type="text" id="location-state" name="new_location[state]" value="<?php echo $post['new_location']['state']; ?>">

                    <label for="location-zip">Zip</label>
                    <input type="text" id="location-zip" name="new_location[zip]" value="<?php echo $post['new_location']['zip']; ?>">

                    <label for="location-map-url">Map URL</label>
                    <input type="text" id="location-map-url" name="new_location[mapurl]" value="<?php echo $post['new_location']['mapurl']; ?>">

                    <label for="location-webpage">Webpage</label>
                    <input type="text" id="location-webpage" name="new_location[webpageurl]" value="<?php echo $post['new_location']['webpageurl']; ?>">

                    <label for="location-hours">Hours</label>
                    <input type="text" id="location-hours" name="new_location[hours]" value="<?php echo $post['new_location']['hours']; ?>">

                    <label for="location-directions">Directions</label>
                    <textarea id="location-directions" name="new_location[directions]"><?php echo $post['new_location']['directions']; ?></textarea>

                    <label for="location-additional-public-info">Additional Public Info</label>
                    <input type="text" id="location-additional-public-info" name="new_location[additionalpublicinfo]" value="<?php echo $post['new_location']['additionalpublicinfo']; ?>">

                    <label for="location-type">Type</label>
                    <input type="text" id="location-type" name="new_location[type]" value="<?php echo $post['new_location']['type']; ?>">

                    <label for="location-phone">Phone</label>
                    <input type="text" id="location-phone" name="new_location[phone]" value="<?php echo $post['new_location']['phone']; ?>">

                    <input <?php if (isset($post['location_save']) && $post['location_save'] == 'on') echo 'checked="checked"'; ?> type="checkbox" id="location-save" name="location_save">
                    <label for="location-save">Save this location for future events</label>
                </div>

                <label for="room">Room</label>
                <input type="text" id="room" name="room" value="<?php echo $post['room']; ?>" />

                <fieldset>
                    <legend style="margin:auto; font-size:.802rem;"><span class="required">*</span> Event duration type</legend>
                    <input <?php if (!isset($post) || $post['event_duration_type'] =='single_day') echo 'checked=checked' ?> id="single-day-event" type="radio" value="single_day" name="event_duration_type">
                    <label for="single-day-event">Single Day</label>&nbsp;
                    <input <?php if ($post['event_duration_type'] == 'multi_day') echo 'checked=checked' ?> id="multi-day-event" type="radio" value="multi_day" name="event_duration_type">
                    <label for="multi-day-event">Multi Day</label>
                </fieldset>

                <div class="wdn-grid-set date-time-select">
                    <div id="start-date-select" class="bp3-wdn-col-one-half date-select">
                        <span class="required">*</span>
                        <label for="start-date" id="start-date-label">Date</label><br/>
                        <span class="wdn-icon-calendar" aria-hidden="true"></span>
                        <input id="start-date" name="start_date" aria-label="Start Date in the format of mm/dd/yyyy" type="text" class="datepicker" value="<?php echo $post['start_date']; ?>" /><br class="hidden small-block">
                    </div>
                    <div id="end-date-select" class="bp3-wdn-col-one-half date-select">
                        <label for="end-date" id="end-date-label">End Date (Optional)</label><br/>
                        <span class="wdn-icon-calendar" aria-hidden="true"></span>
                        <input id="end-date" name="end_date" aria-label="End Date in the format of mm/dd/yyyy" type="text" class="datepicker" value="<?php echo $post['end_date']; ?>" /><br class="hidden small-block">
                    </div>
                    <div id="start-time-select" class="bp3-wdn-col-one-half time-select">
                        <label for="" ><span class="required">*</span> Start Time</label><br/>
                        <select id="start-time-hour" name="start_time_hour" aria-label="Start Time Hour">
                            <option value="">Hour</option>
                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                <option <?php if ($post['start_time_hour'] == $i) echo 'selected="selected"'; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                            <?php } ?>
                        </select> :

                        <select id="start-time-minute" name="start_time_minute" aria-label="Start Time Minute">
                            <option value="">Minute</option>
                            <option <?php if ($post['start_time_minute'] === 0 || $post['start_time_minute'] === '0') echo 'selected="selected"'; ?> value="0">00</option>
                            <option <?php if ($post['start_time_minute'] == 5) echo 'selected="selected"'; ?> value="5">05</option>
                            <option <?php if ($post['start_time_minute'] == 10) echo 'selected="selected"'; ?> value="10">10</option>
                            <option <?php if ($post['start_time_minute'] == 15) echo 'selected="selected"'; ?> value="15">15</option>
                            <option <?php if ($post['start_time_minute'] == 20) echo 'selected="selected"'; ?> value="20">20</option>
                            <option <?php if ($post['start_time_minute'] == 25) echo 'selected="selected"'; ?> value="25">25</option>
                            <option <?php if ($post['start_time_minute'] == 30) echo 'selected="selected"'; ?> value="30">30</option>
                            <option <?php if ($post['start_time_minute'] == 35) echo 'selected="selected"'; ?> value="35">35</option>
                            <option <?php if ($post['start_time_minute'] == 40) echo 'selected="selected"'; ?> value="40">40</option>
                            <option <?php if ($post['start_time_minute'] == 45) echo 'selected="selected"'; ?> value="45">45</option>
                            <option <?php if ($post['start_time_minute'] == 50) echo 'selected="selected"'; ?> value="50">50</option>
                            <option <?php if ($post['start_time_minute'] == 55) echo 'selected="selected"'; ?> value="55">55</option>
                        </select>

                        <div id="start-time-am-pm" class="am_pm">
                            <fieldset>
                                <legend class="wdn-text-hidden">AM/PM</legend>
                                <label><input <?php if (!isset($post) || $post['start_time_am_pm'] == 'am') echo 'checked="checked"'; ?> id="start-time-am-pm-am" title="AM" type="radio" value="am" name="start_time_am_pm">AM</label><br>
                                <label><input <?php if ($post['start_time_am_pm'] == 'pm') echo 'checked="checked"'; ?> id="start-time-am-pm-pm" type="radio" value="pm" name="start_time_am_pm">PM</label>
                            </fieldset>
                        </div>
                    </div>
                    <div id="end-time-select" class="bp3-wdn-col-one-half time-select">
                        <label for="" > End Time (Optional)</label><br/>
                        <select id="end-time-hour" name="end_time_hour" aria-label="End Time Hour">
                            <option value="">Hour</option>
                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                <option <?php if ($post['end_time_hour'] == $i) echo 'selected="selected"'; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                            <?php } ?>
                        </select> :

                        <select id="end-time-minute" name="end_time_minute" aria-label="End Time Minute">
                            <option value="">Minute</option>
                            <option <?php if ($post['end_time_minute'] === 0 || $post['end_time_minute'] === '0') echo 'selected="selected"'; ?> value="0">00</option>
                            <option <?php if ($post['end_time_minute'] == 5) echo 'selected="selected"'; ?> value="5">05</option>
                            <option <?php if ($post['end_time_minute'] == 10) echo 'selected="selected"'; ?> value="10">10</option>
                            <option <?php if ($post['end_time_minute'] == 15) echo 'selected="selected"'; ?> value="15">15</option>
                            <option <?php if ($post['end_time_minute'] == 20) echo 'selected="selected"'; ?> value="20">20</option>
                            <option <?php if ($post['end_time_minute'] == 25) echo 'selected="selected"'; ?> value="25">25</option>
                            <option <?php if ($post['end_time_minute'] == 30) echo 'selected="selected"'; ?> value="30">30</option>
                            <option <?php if ($post['end_time_minute'] == 35) echo 'selected="selected"'; ?> value="35">35</option>
                            <option <?php if ($post['end_time_minute'] == 40) echo 'selected="selected"'; ?> value="40">40</option>
                            <option <?php if ($post['end_time_minute'] == 45) echo 'selected="selected"'; ?> value="45">45</option>
                            <option <?php if ($post['end_time_minute'] == 50) echo 'selected="selected"'; ?> value="50">50</option>
                            <option <?php if ($post['end_time_minute'] == 55) echo 'selected="selected"'; ?> value="55">55</option>
                        </select>

                        <div id="end-time-am-pm" class="am_pm">
                            <fieldset>
                                <legend class="wdn-text-hidden">AM/PM</legend>
                                <label><input <?php if (empty($post) || $post['end_time_am_pm'] == 'am') echo 'checked="checked"'; ?> id="end-time-am-pm-am" type="radio" value="am" name="end_time_am_pm">AM</label><br>
                                <label><input <?php if ($post['end_time_am_pm'] == 'pm') echo 'checked="checked"'; ?> id="end-time-am-pm-pm" type="radio" value="pm" name="end_time_am_pm">PM</label>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <div class="section-container outer-recurring-container">
                    <input <?php if (isset($post['recurring'])) echo 'checked="checked"'; ?> type="checkbox" name="recurring" id="recurring"> 
                    <label for="recurring">This is a recurring event</label>
                    <div class="recurring-container">
                        <label for="recurring-type">This event recurs </label>
                        <select id="recurring-type" name="recurring_type">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="biweekly">Biweekly</option>
                            <optgroup label="Monthly" id="monthly-group">
                            </optgroup>
                            <option value="annually">Yearly</option>
                        </select>
                        <label for="recurs-until-date">until </label><br>
                        <span style="top: .4em" class="wdn-icon-calendar" aria-hidden="true"></span>
                        <input value="<?php echo $post['recurs_until_date']; ?>" id="recurs-until-date" name="recurs_until_date" type="text" class="datepicker" aria-label="until this date in the format of mm/dd/yyyy"/>
                    </div>
                </div>

                <label for="directions">Directions</label>
                <textarea id="directions" name="directions"><?php echo $post['directions'] ?></textarea>

                <label for="additional-public-info">Additional Public Info</label>
                <textarea id="additional-public-info" name="additional_public_info"><?php echo $post['additional_public_info'] ?></textarea>
            </fieldset>

        </div>

        <div class="bp3-wdn-col-one-third">
            <fieldset class="visual-island">
                <legend class="vi-header">
                    Sharing
                </legend>
                <div class="details">
                    <fieldset>
                        <legend class="wdn-text-hidden">Privacy</legend>
                        <label>
                            <input type="radio" value="private" name="private_public" id="sharing-private" <?php if ($post['private_public'] == 'private') echo 'checked="checked"'; ?>> 
                            Private
                        </label> 
                        <br>
                        <label>
                            <input type="radio" value="public" name="private_public" id="sharing-public" <?php if ($post['private_public'] != 'private') echo 'checked="checked"'; ?>> 
                            Public
                        </label>
                    </fieldset>

                    <label>
                        <input <?php if (isset($post['send_to_main'])) echo 'checked="checked"'; ?> type="checkbox" name="send_to_main" id="send-to-main"> 
                        Consider for main UNL calendar
                    </label>
                </div>
            </fieldset>

            <fieldset class="visual-island">
                <legend class="vi-header">
                    Contact Info
                </legend>

                <div class="details">
                    <label for="contact-name">Name</label>
                    <input type="text" id="contact-name" name="contact_name" value="<?php echo $post['contact_name'] ?>" />

                    <label for="contact-phone">Phone</label>
                    <input type="text" id="contact-phone" name="contact_phone" value="<?php echo $post['contact_phone'] ?>" />

                    <label for="contact-email">Email</label>
                    <input type="text" id="contact-email" name="contact_email" value="<?php echo $post['contact_email'] ?>" />

                    <label for="website">Event Website</label>
                    <input type="text" id="website" name="website" value="<?php echo $event->webpageurl ?>" />
                </div>
            </fieldset>

            <fieldset class="visual-island">
                <legend class="vi-header">
                    Image
                </legend>

                <div class="details">
                    <input style="font-size: 10px;" type="file" name="imagedata" id="imagedata" aria-label="Select an Image">
                </div>
            </fieldset>
        </div>
        <div class="bp1-wdn-col-two-thirds">
            <button class="wdn-button wdn-button-brand wdn-pull-left" type="submit">Submit Event</button>
        </div>
    </form>
</div>

<script type="text/javascript">
WDN.initializePlugin('jqueryui', [function() {  
    $ = require('jquery');
    $('.datepicker').datepicker();
    $("LINK[href^='//unlcms.unl.edu/wdn/templates_4.0/scripts/plugins/ui/css/jquery-ui.min.css']").remove();

    $('#start-date').change(function (change) {
        setRecurringOptions($(this), $('#monthly-group'));
    });

    setRecurringOptions($('#start-date'), $('#monthly-group'));
    $('#recurring-type').val("<?php echo $post['recurring_type']; ?>");

    $('#location').change(function (change) {
        if ($(this).val() == 'new') {
            $('#new-location-fields').show();
        } else {
            $('#new-location-fields').hide();
        }
    });

    $('#location').change();

    $('#single-day-event').change(function() {
        if($(this).is(':checked')) {
            $('#start-date-label').text("Date");
            $('#recurring').prop('disabled',false);
            $('#end-date-select').hide();
            if($('#end-date').val() != '' && $('#start-date').val() != '') {
                $('#end-date').val($('#start-date').val());
            }
        }
    }).change();

    $('#multi-day-event').change(function() {
        if($(this).is(':checked')) {
            $('#start-date-label').text("Start Date");
            $('#recurring').prop('disabled',true);
            $('#end-date-select').show();
            if ($('#recurring').is(':checked')) {
                $('#recurring').prop('checked', false);
            }
        }
    }).change();

}]);

require(['jquery'], function ($) {
    $('#create-event-form').submit(function (submit) {
        errors = [];

        // validate required fields
        if ($('#title').val() == '' || $('#location').val() == '' || $('#start-date').val() == '') {
            if ($('#title').val() == '') {
                notifier.mark_input_invalid($('#title'));
            }
            if ($('#location').val() == '') {
                notifier.mark_input_invalid($('#location'));
            }
            if ($('#start-date').val() == '') {
                notifier.mark_input_invalid($('#start-date'));
            }
            errors.push('<a href="#title">Title</a>, <a href="#location">location</a>, and <a href="#start-date">start date</a> are required.');
        }

        var start = new Date($('#start-date').val());
        if ($('#start-date').val() != '') {
            // validate end date is after start date and the time is afterward accordingly
            if ($('#end-date').val() != '') {
                var end = new Date($('#end-date').val());

                // translate times from inputs. Blank hour = 12, blank minute = 0, blank am/pm = am
                var start_am_pm = $('#start-time-am-pm-pm').is(':checked') ? 'pm' : 'am';
                var start_hour = $('#start-time-hour').val() != '' ? parseInt($('#start-time-hour').val()) % 12 : 0;
                start_hour = start_am_pm == 'pm' ? start_hour + 12 : start_hour;
                var start_minute = $('#start-time-minute').val() != '' ? parseInt($('#start-time-minute').val()) : 0;
                start.setHours(start_hour);
                start.setMinutes(start_minute);

                var end_am_pm = $('#end-time-am-pm-pm').is(':checked') ? 'pm' : 'am';
                var end_hour = $('#end-time-hour').val() != '' ? parseInt($('#end-time-hour').val()) % 12 : 0;
                end_hour = end_am_pm == 'pm' ? end_hour + 12 : end_hour;
                var end_minute = $('#end-time-minute').val() != '' ? parseInt($('#end-time-minute').val()) : 0;
                end.setHours(end_hour);
                end.setMinutes(end_minute);

                if (start > end) {
                    notifier.mark_input_invalid($('#end-date'));
                    errors.push('Your <a href="#end-date">end date/time</a> must be on or after the <a href="#start-date">start date/time</a>.');
                }
            }
        }

        // if recurring is checked, there must be a recurring type and the recurs_until date must be on
        // or after the start date
        if ($('#start-date').val() != '') {
            if ($('#recurring').is(':checked')) {
                if ($('#recurring-type').val() == '' || $('#recurs-until-date').val() == '') {
                    if ($('#recurring-type').val() == '') {
                        notifier.mark_input_invalid($('#recurring-type'));
                    }
                    if ($('#recurs-until-date').val() == '') {
                        notifier.mark_input_invalid($('#recurs-until-date'));
                    }
                    errors.push('Recurring events require a <a href="#recurring-type">recurring type</a> and <a href="#recurs-until-date">date</a> that they recur until.');
                }

                // check that the recurs until date is on or after the start date
                start.setHours(0);
                start.setMinutes(0);
                var until = new Date($('#recurs-until-date').val());

                if (start > until) {
                    notifier.mark_input_invalid($('#recurs-until-date'));
                    errors.push('The <a href="#recurs-until-date">"recurs until date"</a> must be on or after the start date.');
                }
            }
        }

        // new locations must have a name
        if ($('#location').val() == 'new' && $('#location-name').val() == '') {
            notifier.mark_input_invalid($('#location-name'));
            errors.push('You must give your new location a <a href="#location-name">name</a>.');
        }

        //A room number is required if recommended to unl main calendar. Check the room number on the event or the room number on new location if created
        if ($('#send-to-main').is(':checked')) {
            if($('#room').val() == '' && $('#location-room').val() == '') {
                notifier.mark_input_invalid($('#room'));
                if($('#location').val() == 'new') {
                    notifier.mark_input_invalid($('#location-room'));
                    errors.push('You must give either a <a href="#room">event room</a> or your new location a <a href="#location-room">room</a> to recommend to UNL Main Calendar.');
                }
                else {
                    errors.push('You must give a <a href="#room">room</a> to recommend to UNL Main Calendar.');
                }
            }
        }

        if (errors.length > 0) {
            submit.preventDefault();
            notifier.alert('Sorry! We couldn\'t create your event', '<ul><li>' + errors.join('</li><li>') + '</li></ul>');
        }
    });
});

</script>
