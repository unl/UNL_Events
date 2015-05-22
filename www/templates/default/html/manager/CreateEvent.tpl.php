<?php
    $calendar = $context->calendar;
?>
    <?php echo $calendar->name ?> &gt; Create Event
        <div class="wdn-grid-set">
            <form action="/manager/<?php echo $calendar->shortname ?>/create/" method="POST">
                <div class="bp1-wdn-col-two-thirds">
                    <legend>Details</legend>
                    <fieldset>
                        <label for="title">Title*</label>
                        <input type="text" id="title" name="title" />

                        <label for="subtitle">Subtitle</label>
                        <input type="text" id="subtitle" name="subtitle" />

                        <label for="description">Description</label>
                        <textarea id="description" name="description"></textarea>

                        <label for="type">Type</label>
                        <select id="type" name="type">
                        <?php foreach ($context->getEventTypes() as $type) { ?>
                            <option value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
                        <?php } ?>
                        </select>
                    </fieldset>

                    <legend>Location, Date, and Time</legend>
                    <fieldset>
                        <label for="location">Location*</label>
                        <select id="location" name="location" class="use-select2">
                        <?php foreach ($context->getLocations() as $location) { ?>
                            <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                        <?php } ?>
                            <option value="new">-- New Location --</option>
                        </select>

                        <div id="new-location-fields">
                        <h6>New Location</h6>
                        <label for="location-name">Name</label>
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
                        <input type="text" id="room" name="room" />


                        <label for="start-date" >Start Date &amp; Time</label>
                        <div class="date-time-select"><span class="wdn-icon-calendar"></span>
                            <input id="start-date" onchange="setRecurringOptions()" name="start_date" type="text" class="datepicker" /> @
                            <select id="start-time-hour" name="start_time_hour">
                                <option value=""></option>
                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                <option value="<?php echo $i ?>"><?php echo $i ?></option>
                            <?php } ?>
                            </select> : 

                            <select id="start-time-minute" name="start_time_minute">
                                <option value=""></option>
                                <option value="0">00</option>
                                <option value="5">05</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="35">35</option>
                                <option value="40">40</option>
                                <option value="45">45</option>
                                <option value="50">50</option>
                                <option value="55">55</option>
                            </select>

                            <div id="start-time-am-pm" class="am_pm">
                                <input type="radio" value="am" name="start_time_am_pm">AM<br>
                                <input type="radio" value="pm" name="start_time_am_pm">PM
                            </div>
                        </div>

                        <label for="end-date">End Date &amp; Time (Optional)</label>
                        <div class="date-time-select"><span class="wdn-icon-calendar"></span>
                            <input id="end-date" name="end_date" type="text" class="datepicker" /> @
                            <select id="end-time-hour" name="end_time_hour">
                                <option value=""></option>
                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                <option value="<?php echo $i ?>"><?php echo $i ?></option>
                            <?php } ?>
                            </select> :

                            <select id="end-time-minute" name="end_time_minute">
                                <option value=""></option>
                                <option value="0">00</option>
                                <option value="5">05</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="35">35</option>
                                <option value="40">40</option>
                                <option value="45">45</option>
                                <option value="50">50</option>
                                <option value="55">55</option>
                            </select>

                            <div id="end-time-am-pm" class="am_pm">
                                <input type="radio" value="am" name="end_time_am_pm">AM<br>
                                <input type="radio" value="pm" name="end_time_am_pm">PM
                            </div>
                        </div>

                        <div class="section-container">
                            <input type="checkbox" name="recurring" id="recurring"> 
                            <label for="recurring">This is a recurring event</label>
                            <div class="recurring-container date-time-select">                        
                                <label for="recurring-type">This event recurs </label>
                                <select id="recurring-type" name="recurring_type">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <optgroup label="Monthly" id="monthly-group">
                                    </optgroup>
                                    <option value="annually">Yearly</option>
                                </select>
                                <label for="recurs-until-date">until </label><br>
                                <span class="wdn-icon-calendar"></span>
                                <input id="recurs-until-date" name="recurs_until_date" type="text" class="datepicker" />
                            </div>
                        </div>

                        <label for="directions">Directions</label>
                        <textarea id="directions" name="directions"></textarea>

                        <label for="additional-public-info">Additional Public Info</label>
                        <textarea id="additional-public-info" name="additional_public_info"></textarea>
                    </fieldset>

                </div>

                <div class="bp1-wdn-col-one-third">
                    <div class="visual-island">
                        <div class="vi-header">
                            Sharing
                        </div>
                        <p>
                            <input type="radio" value="private" name="private_public" id="sharing-private" checked="checked"> 
                            <label for="sharing-private">Private</label> 
                            <br>
                        
                            <input type="radio" value="public" name="private_public" id="sharing-public"> 
                            <label for="sharing-public">Public</label> 
                            <br>

                            <input type="checkbox" name="send_to_main" id="send-to-main"> 
                            <label for="send-to-main">Consider for main calendar</label>
                        </p>
                    </div>

                    <div class="visual-island">
                        <div class="vi-header">
                            Contact Info
                        </div>

                        <p>
                            <label for="contact-name">Name</label>
                            <input type="text" id="contact-name" name="contact_name" />

                            <label for="contact-phone">Phone</label>
                            <input type="text" id="contact-phone" name="contact_phone" />

                            <label for="contact-email">Email</label>
                            <input type="text" id="contact-email" name="contact_email" />

                            <label for="website">Event Website</label>
                            <input type="text" id="website" name="website" />
                        </p>
                    </div>
                </div>
                <div class="bp1-wdn-col-two-thirds">
                    <button class="wdn-button wdn-button-brand wdn-pull-left" type="submit">Submit Event</button>
                </div>
            </form>
        </div>
<div class="calendar"></div>

<script type="text/javascript">
WDN.initializePlugin('jqueryui', [function() {  

    $ = require('jquery');

    $('.datepicker').datepicker();

    $("LINK[href='//unlcms.unl.edu/wdn/templates_4.0/scripts/plugins/ui/css/jquery-ui.min.css']").remove();


    setRecurringOptions = function(pre, togg, post) {

        // get startdate info

        var weekdays = Array("Sunday", "Monday", "Tuesday",
            "Wednesday", "Thursday", "Friday", "Saturday");
        var startelem = $("#start-date");
        var startyear = startelem.val().substring(6, 10);
        var startmonth = startelem.val().substring(0, 2);
        var startday = startelem.val().substring(3, 5);
        var startdate = new Date(startyear, startmonth - 1, startday);
        var startweekday = weekdays[startdate.getDay()];

        togg = $("#recurs-until-date");

        $recurringType = $("#monthly-group");

        var weekday = weekdays[startdate.getDay()];
        // get week in month
        var nth = {
            "1": "First",
            "2": "Second",
            "3": "Third",
            "4": "Fourth",
            "5": "Last"
        };
        var week = 0;
        for (var i = 1; i <= startday; i++) {
            var d = new Date(startyear, startmonth - 1, i);
            if (weekdays[d.getDay()] == startweekday) {
                week++;


            }
        }
        // get total of day in month
        var total = 0;
        var i = 1;
        var d = new Date(startyear, startmonth - 1, 1);
        while (i == d.getDate()) {
            if (weekdays[d.getDay()] == weekday) {
                total++;
            }
            d = new Date(startyear, startmonth - 1, ++i);
        }
        console.log(total)
            // get number of days (28, 29, 30, 31) in month
        var daysinmonth = 28;
        d = new Date(startyear, startmonth - 1, 28);
        while (daysinmonth == d.getDate()) {
            d = new Date(startyear, startmonth - 1, ++daysinmonth);
        }
        daysinmonth--;
        // remove options, if any
        $recurringType.children(".dynamicRecurring").remove();
        // populate rectypemonth with appropriate options
        var op;

        $recurringType.prepend("<option class='dynamicRecurring' value='" + nth[week].toLowerCase() + "'>" + nth[week] + " " + startweekday + " of every month</option>")

        if (week == 4 && total == 4) {

            $recurringType.prepend("<option class='dynamicRecurring' value='last'>" + "Last " + startweekday + " of month</option>")

        }

        if (daysinmonth == startday) {

            $recurringType.prepend("<option class='dynamicRecurring' value='lastday'>Last day of month</option>");

        }

        var text = '';

        if (startday.substr(1) == "1" || startday.substr(1) == "2" || startday.substr(1) == "3") {
            text = startday + nth[startday.substr(1)].substr(-2) + ' of every month';
        } else {
            text = startday + 'th of every month'
        }
        if (startday.substr(0, 1) == "0") {
            text = text.substr(1);
        }

        $recurringType.prepend("<option class='dynamicRecurring' value='date'>" + text + "</option>");



    }




}]);


</script>
