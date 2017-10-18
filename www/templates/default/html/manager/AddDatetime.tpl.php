<?php
    $calendar = $context->calendar;
    $event = $context->event;
    $datetime = $context->event_datetime;

    if ($datetime->starttime == NULL) {
        $start_time = '';
        $start_date =  '';
        $start_hour = '';
        $start_minute = -1;
        $start_am_pm = 'am';
    } else {
        $start_time = strtotime($datetime->starttime);
        $start_date = date('m/d/Y', $start_time);
        $start_hour = date('h', $start_time);
        $start_minute = date('i', $start_time);
        $start_am_pm = date('a', $start_time);
    }

    if ($datetime->endtime == NULL) {
        $end_time = '';
        $end_date =  '';
        $end_hour = '';
        $end_minute = -1;
        $end_am_pm = 'am';
    } else {
        $end_time = strtotime($datetime->endtime);
        $end_date = date('m/d/Y', $end_time);
        $end_hour = date('h', $end_time);
        $end_minute = date('i', $end_time);
        $end_am_pm = date('a', $end_time);
    }

    $recurs_until_date = date('m/d/Y', strtotime($datetime->recurs_until));
?>
<?php
    $last_crumb = NULL;
    if ($context->recurrence_id != NULL) {
        $last_crumb = 'Edit a Single Instance from Recurring Event';
    } else {
        $last_crumb = $datetime->id == NULL ? 'Add a Location, Date, and Time' : 'Edit Location, Date, and Time'; 
    }

    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        'Edit "' . $event->title . '"' => $event->getEditURL($context->calendar),
        $last_crumb => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1 class="wdn-brand">
<?php echo $last_crumb ?>
</h1>
<form id="add-datetime-form" action="" method="POST">
    <fieldset>
        <label for="location"><span class="required">*</span> Location</label>
        <select id="location" name="location" class="use-select2" style="width: 100%;">
            <?php if ($datetime->id != NULL): ?>
            <optgroup label="Current location">
                <option selected="selected" value="<?php echo $datetime->location_id ?>"><?php echo $datetime->getLocation()->name; ?></option>
            <?php endif; ?>
            <optgroup label="Your saved locations">
                <?php foreach ($context->getUserLocations() as $location): ?>
                    <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                <?php endforeach; ?>
                <option value="new">-- New Location --</option>
            </optgroup>
            <optgroup label="UNL Campus locations">
                <?php foreach ($context->getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_MAIN) as $location): ?>
                    <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                <?php endforeach; ?>
            </optgroup>
            <optgroup label="Extension locations">
                <?php foreach ($context->getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_EXTENSION) as $location): ?>
                    <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                <?php endforeach; ?>
            </optgroup>
        </select>

        <div id="new-location-fields" style="display: none;">
            <h6>New Location</h6>
            <label for="location-name"><span class="required">*</span> Name</label>
            <input type="text" id="location-name" name="new_location[name]">

            <label for="location-address-1">Address</label>
            <input type="text" id="location-address-1" name="new_location[streetaddress1]">

            <label for="location-address-2">Address 2</label>
            <input type="text" id="location-address-2" name="new_location[streetaddress2]">

            <label for="location-room">Room</label>
            <input type="text" id="location-room" name="new_location[room]">

            <label for="location-city">City</label>
            <input type="text" id="location-city" name="new_location[city]">

            <label for="location-state">State</label>
            <input type="text" id="location-state" name="new_location[state]">

            <label for="location-zip">Zip</label>
            <input type="text" id="location-zip" name="new_location[zip]">

            <label for="location-map-url">Map URL</label>
            <input type="text" id="location-map-url" name="new_location[mapurl]">

            <label for="location-webpage">Webpage</label>
            <input type="text" id="location-webpage" name="new_location[webpageurl]">

            <label for="location-hours">Hours</label>
            <input type="text" id="location-hours" name="new_location[hours]">

            <label for="location-directions">Directions</label>
            <textarea id="location-directions" name="new_location[directions]"></textarea>

            <label for="location-additional-public-info">Additional Public Info</label>
            <input type="text" id="location-additional-public-info" name="new_location[additionalpublicinfo]">

            <label for="location-type">Type</label>
            <input type="text" id="location-type" name="new_location[type]">

            <label for="location-phone">Phone</label>
            <input type="text" id="location-phone" name="new_location[phone]">

            <input type="checkbox" id="location-save" name="location_save"> 
            <label for="location-save">Save this location for future events</label>
        </div>

        <label for="room">Room</label>
        <input type="text" id="room" name="room" value="<?php echo $datetime->room; ?>" />


        <label for="start-date" ><span class="required">*</span> Start Date &amp; Time</label>
        <div class="date-time-select"><span class="wdn-icon-calendar" aria-hidden="true"></span>
            <input id="start-date" value="<?php echo $start_date; ?>" 
                name="start_date" type="text" class="datepicker" aria-label="Start date in the format of mm/dd/yyyy"/><br class="hidden small-block"> @
            <select id="start-time-hour" name="start_time_hour" aria-label="Start time hour">
                <option value="">Hour</option>
            <?php for ($i = 1; $i <= 12; $i++) { ?>
                <option <?php if ($i == $start_hour) echo 'selected="selected"'; ?> 
                    value="<?php echo $i ?>"><?php echo $i ?></option>
            <?php } ?>
            </select> : 

            <select id="start-time-minute" name="start_time_minute" aria-label="End time minute">
                <option value="">Minute</option>
                <?php for ($i = 0; $i < 60; $i+=5): ?>
                    <option <?php if ($i == $start_minute) echo 'selected="selected"'; ?> 
                        value="<?php echo $i; ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                <?php endfor; ?>
            </select>

            <div id="start-time-am-pm" class="am_pm">
                <fieldset>
                    <legend class="wdn-text-hidden">AM/PM</legend>
                    <label><input <?php if ($start_am_pm == 'am') echo 'checked="checked"'; ?> 
                        type="radio" value="am" id="start-time-am-pm-am" name="start_time_am_pm">AM</label><br>
                    <label><input <?php if ($start_am_pm == 'pm') echo 'checked="checked"'; ?> 
                    type="radio" value="pm" id="start-time-am-pm-pm" name="start_time_am_pm">PM</label>
                </fieldset>
            </div>
        </div>

        <label for="end-date">End Date &amp; Time (Optional)</label>
        <div class="date-time-select"><span class="wdn-icon-calendar" aria-hidden="true"></span>
            <input id="end-date" value="<?php echo $end_date; ?>"
                name="end_date" type="text" class="datepicker" aria-label="End date in the format of mm/dd/yyyy" /><br class="hidden small-block"> @
            <select id="end-time-hour" name="end_time_hour" aria-label="End time hour">
                <option value="">Hour</option>
            <?php for ($i = 1; $i <= 12; $i++) { ?>
                <option <?php if ($i == $end_hour) echo 'selected="selected"'; ?> 
                    value="<?php echo $i ?>"><?php echo $i ?></option>
            <?php } ?>
            </select> :

            <select id="end-time-minute" name="end_time_minute" aria-label="End time minute">
                <option value="">Minute</option>
                <?php for ($i = 0; $i < 60; $i+=5): ?>
                    <option <?php if ($i == $end_minute) echo 'selected="selected"'; ?> 
                        value="<?php echo $i; ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                <?php endfor; ?>
            </select>

            <div id="end-time-am-pm" class="am_pm">
                <fieldset>
                    <legend class="wdn-text-hidden">AM/PM</legend>
                    <label><input <?php if ($end_am_pm == 'am') echo 'checked="checked"'; ?> 
                        type="radio" value="am" id="end-time-am-pm-am" name="end_time_am_pm">AM</label><br>
                    <label><input <?php if ($end_am_pm == 'pm') echo 'checked="checked"'; ?> 
                        type="radio" value="pm" id="end-time-am-pm-pm" name="end_time_am_pm">PM</label>
                </fieldset>
            </div>
        </div>

        <?php if ($context->recurrence_id == NULL) : ?>
            <div class="section-container">
                <input <?php if ($datetime->recurringtype != 'none' && $datetime->recurringtype != NULL) echo 'checked="checked"' ?> type="checkbox" name="recurring" id="recurring"> 
                <label for="recurring">This is a recurring event</label>
                <div class="recurring-container date-time-select">                        
                    <label for="recurring-type">This event recurs </label>
                    <select id="recurring-type" name="recurring_type">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="biweekly">Biweekly</option>
                        <optgroup label="Monthly" id="monthly-group">
                        </optgroup>
                        <option value="annually">Yearly</option>
                    </select>s
                    <label for="recurs-until-date">until </label><br>
                    <span class="wdn-icon-calendar" style="top: .4em" aria-hidden="true"></span>
                    <input value="<?php if ($datetime->recurringtype != 'none' && $datetime->recurringtype != NULL) echo $recurs_until_date; ?>" id="recurs-until-date" name="recurs_until_date" type="text" class="datepicker" aria-label="Until date in the format of mm/dd/yyyy" />
                </div>
            </div>
        <?php endif; ?>

        <label for="directions">Directions</label>
        <textarea id="directions" name="directions"><?php echo $datetime->directions; ?></textarea>

        <label for="additional-public-info">Additional Public Info</label>
        <textarea id="additional-public-info" name="additional_public_info"><?php echo $datetime->additionalpublicinfo; ?></textarea>
    </fieldset>

    <button class="wdn-button wdn-button-brand wdn-pull-left" type="submit">Submit</button>
</form>
<br>

<script type="text/javascript">
WDN.initializePlugin('jqueryui', [function() {  
    $ = require('jquery');

    $('.datepicker').datepicker();
    $("LINK[href^='//unlcms.unl.edu/wdn/templates_4.0/scripts/plugins/ui/css/jquery-ui.min.css']").remove();

    $('#location').change(function(change) {
        if ($('#location').val() == 'new') {
            $('#new-location-fields').show();
        } else {
            $('#new-location-fields').hide();
        }
    });

    $('#location').change();

    $('#start-date').change(function (change) {
        setRecurringOptions($(this), $('#monthly-group'));
    });

    <?php if ($datetime->recurringtype != 'none' && $datetime->recurringtype != NULL): ?>
    setRecurringOptions($('#start-date'), $('#monthly-group'));
    $('#recurring-type').val('<?php echo $datetime->recurringtype ?>');
    <?php endif; ?>

    $('#add-datetime-form').submit(function (submit) {
        errors = [];

        // validate required fields
        if ($('#location').val() == '' || $('#start-date').val() == '') {
            if ($('#location').val() == '') {
                notifier.mark_input_invalid($('#location'));
            }
            if ($('#start-date').val() == '') {
                notifier.mark_input_invalid($('#start-date'));
            }
            errors.push('<a href="#location">Location</a> and <a href="#start-date">start date</a> are required.');
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

        if (errors.length > 0) {
            submit.preventDefault();
            notifier.alert('Sorry! We couldn\'t create your event', '<ul><li>' + errors.join('</li><li>') + '</li></ul>');
        }
    });
}]);
</script>
