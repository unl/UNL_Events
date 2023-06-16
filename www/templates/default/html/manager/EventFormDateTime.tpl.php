<?php
    $calendar = $context->calendar;
    $event = $context->event;
    $post = $context->post;

    // We need this since the event_datetime gets edited when post is being handled
    if (isset($context->event_datetime)) {
        $datetime = $context->getOriginalDatetime();
    }

    // Sorry that this is messy it is better than a billion if statements

    // We are going to use the post data
    // If that is not set then we check if we can use the datetime data
    // If we can not then we use the default value

    $standard_date_format = 'm/d/Y';

    $timezone = $post['timezone'] ?? $datetime->timezone ?? $calendar->defaulttimezone;

    $datetime_start_time = (isset($datetime) && isset($datetime->starttime)) ? strtotime($datetime->starttime) : '';
    $start_date   = $post['start_date']        ?? ( !empty($datetime_start_time) ? date($standard_date_format, $datetime_start_time) : ''   );
    $start_hour   = $post['start_time_hour']   ?? ( !empty($datetime_start_time) ? date('h'    , $datetime_start_time) : ''   );
    $start_minute = $post['start_time_minute'] ?? ( !empty($datetime_start_time) ? date('i'    , $datetime_start_time) : -1   );
    $start_am_pm  = $post['start_time_am_pm']  ?? ( !empty($datetime_start_time) ? date('a'    , $datetime_start_time) : 'am' );

    $datetime_end_time = (isset($datetime) && isset($datetime->endtime)) ? strtotime($datetime->endtime) : '';
    $end_date   = $post['end_date']        ?? ( !empty($datetime_end_time) ? date($standard_date_format, $datetime_end_time) : ''   );
    $end_hour   = $post['end_time_hour']   ?? ( !empty($datetime_end_time) ? date('h'    , $datetime_end_time) : ''   );
    $end_minute = $post['end_time_minute'] ?? ( !empty($datetime_end_time) ? date('i'    , $datetime_end_time) : -1   );
    $end_am_pm  = $post['end_time_am_pm']  ?? ( !empty($datetime_end_time) ? date('a'    , $datetime_end_time) : 'am' );

    $datetime_recurring_check = (isset($context->recurrence_id));
    $is_recurring             = $post['recurring'] ?? (isset($datetime) && isset($datetime->recurringtype) && strtolower($datetime->recurringtype) !== 'none');
    $recurring_type       = $post['recurring_type']    ?? ( ($is_recurring) ? $datetime->recurringtype : ''                          );
    $recurs_until_date    = $post['recurs_until_date'] ?? ( ($is_recurring) ? date($standard_date_format, strtotime($datetime->recurs_until)) : '' );
    $recurring_month_type = ($is_recurring && $recurring_type == 'monthly') ? $datetime->rectypemonth : '';

    $additional_public_info = $post['additional_public_info'] ?? $datetime->additionalpublicinfo ?? '';

    $datetime_physical_location_check = $post['physical_location_check'] ?? ( (isset($datetime) && isset($datetime->location_id)) ? '1' : '0' );
    $datetime_virtual_location_check  = $post['virtual_location_check']  ?? ( (isset($datetime) && isset($datetime->webcast_id)) ? '1' : '0'  );

    $datetime_location = (isset($datetime) && isset($datetime->location_id)) ? $datetime->getLocation() : '';
    $location          = $post['location'] ?? ( (!empty($datetime_location)) ? $datetime_location->id : '' );
    $location_room                   = $post['room'] ?? $datetime->room ?? '';
    $location_directions             = $post['directions'] ?? $datetime->directions ?? '';
    $location_additional_public_info = $post['l_additional_public_info'] ?? $datetime->location_additionalpublicinfo ?? '';

    $datetime_v_location = (isset($datetime) && isset($datetime->webcast_id)) ? $datetime->getWebcast() : '';
    $v_location          = $post['v_location'] ?? ( (!empty($datetime_v_location)) ? $datetime_v_location->id : '' );
    $v_location_additional_public_info = $post['v_additional_public_info'] ?? $datetime->webcast_additionalpublicinfo ?? '';

?>

<h2>Date &amp; Time</h2>
<section class="dcf-mb-8 dcf-ml-5">
    <div id="datetime_container">

        <div class="dcf-form-group">
            <label class="dcf-mt-2" for="timezone">
                Time Zone <small class="dcf-required">Required</small>
            </label>
            <select id="timezone" name="timezone" aria-label="Time Zone">
                <?php foreach (UNL\UCBCN::getTimezoneOptions() as $tzName => $tzValue) : ?>
                    <option
                        <?php if ($timezone == $tzValue) { echo SELECTED_INPUT; } ?>
                        value="<?php echo $tzValue; ?>"
                    >
                        <?php echo $tzName; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <fieldset>
            <legend>
                Start Date &amp; Time
                <small class="dcf-required">Required</small>
            </legend>
            <div class="dcf-d-flex dcf-flex-wrap dcf-ai-center dcf-col-gap-4">
                <div class="dcf-form-group dcf-datepicker dcf-flex-grow-1">
                    <input
                        id="start-date"
                        name="start_date"
                        type="text"
                        value="<?php echo $start_date; ?>"
                        aria-label="Start Date in the format of mm/dd/yyyy"
                        autocomplete="off"
                    >
                </div>
                <div class="dcf-form-group dcf-d-flex dcf-ai-center dcf-flex-grow-1">
                    <span class="dcf-pr-2">@</span>
                    <select
                        class="dcf-flex-grow-1"
                        id="start-time-hour"
                        name="start_time_hour"
                        aria-label="Start Time Hour"
                    >
                        <option value="">Hour</option>
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <option
                                <?php if ($start_hour == $i) { echo SELECTED_INPUT; } ?>
                                value="<?php echo $i; ?>"
                            >
                                <?php echo $i; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <span class="dcf-pr-1 dcf-pl-1">:</span>
                    <select
                        class="dcf-flex-grow-1"
                        id="start-time-minute"
                        name="start_time_minute"
                        aria-label="Start Time Minute"
                    >
                        <option value="">Minute</option>
                        <?php for ($i = 0; $i < 60; $i+=5): ?>
                            <option
                                <?php if ($start_minute == $i) { echo SELECTED_INPUT; } ?>
                                value="<?php echo $i; ?>"
                            >
                                <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <fieldset
                        class="dcf-d-flex
                            dcf-flex-col
                            dcf-row-gap-3
                            dcf-ai-center
                            dcf-col-gap-3
                            dcf-mb-0
                            dcf-ml-4
                            dcf-p-0
                            dcf-b-0
                            dcf-txt-sm"
                        id="start-time-am-pm"
                    >
                        <legend class="dcf-sr-only">AM/PM</legend>
                        <div class="dcf-input-radio dcf-mb-0">
                            <input
                                id="start-time-am-pm-am"
                                name="start_time_am_pm"
                                type="radio"
                                value="am"
                                <?php if ($start_am_pm == 'am') { echo CHECKED_INPUT; } ?>
                            >
                            <label class="dcf-mb-0" for="start-time-am-pm-am">AM</label>
                        </div>
                        <div class="dcf-input-radio dcf-mb-0">
                            <input
                                id="start-time-am-pm-pm"
                                name="start_time_am_pm"
                                type="radio"
                                value="pm"
                                <?php if ($start_am_pm == 'pm') { echo CHECKED_INPUT; } ?>
                            >
                            <label class="dcf-mb-0" for="start-time-am-pm-pm">PM</label>
                        </div>
                    </fieldset>
                </div>

            </div>
        </fieldset>

        <fieldset>
            <legend>End Date &amp; Time <small class="dcf-pl-1 dcf-txt-xs dcf-italic">Optional</small></legend>
            <div class="dcf-d-flex dcf-flex-wrap dcf-ai-center dcf-col-gap-4">
                <div class="dcf-form-group dcf-datepicker dcf-flex-grow-1">
                    <input
                        id="end-date"
                        name="end_date"
                        type="text"
                        value="<?php echo $end_date; ?>"
                        aria-label="End Date in the format of mm/dd/yyyy"
                        autocomplete="off"
                    >
                </div>
                <div class="dcf-form-group dcf-d-flex dcf-ai-center dcf-flex-grow-1">
                    <span class="dcf-pr-2">@</span>
                    <select class="dcf-flex-grow-1" id="end-time-hour" name="end_time_hour" aria-label="End Time Hour">
                        <option value="">Hour</option>
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <option
                                <?php if ($end_hour == $i) { echo SELECTED_INPUT; } ?>
                                value="<?php echo $i; ?>"
                            >
                                <?php echo $i; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <span class="dcf-pr-1 dcf-pl-1">:</span>
                    <select
                        class="dcf-flex-grow-1"
                        id="end-time-minute"
                        name="end_time_minute"
                        aria-label="End Time Minute"
                    >
                        <option value="">Minute</option>
                        <?php for ($i = 0; $i < 60; $i+=5): ?>
                            <option
                                <?php if ($i == $end_minute) { echo SELECTED_INPUT; } ?>
                                value="<?php echo $i; ?>"
                            >
                                <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <fieldset
                        class="dcf-d-flex
                            dcf-flex-col
                            dcf-row-gap-3
                            dcf-ai-center
                            dcf-col-gap-3
                            dcf-mb-0
                            dcf-ml-4
                            dcf-p-0
                            dcf-b-0
                            dcf-txt-sm"
                        id="end-time-am-pm"
                    >
                        <legend class="dcf-sr-only">AM/PM</legend>
                        <div class="dcf-input-radio dcf-mb-0">
                            <input
                                id="end-time-am-pm-am"
                                name="end_time_am_pm"
                                type="radio"
                                value="am"
                                <?php if ($end_am_pm == 'am') { echo CHECKED_INPUT; } ?>
                            >
                            <label class="dcf-mb-0" for="end-time-am-pm-am">AM</label>
                        </div>
                        <div class="dcf-input-radio dcf-mb-0">
                            <input
                                id="end-time-am-pm-pm"
                                name="end_time_am_pm"
                                type="radio"
                                value="pm"
                                <?php if ($end_am_pm == 'pm') { echo CHECKED_INPUT; } ?>
                            >
                            <label class="dcf-mb-0" for="end-time-am-pm-pm">PM</label>
                        </div>
                    </fieldset>
                </div>
            </div>
        </fieldset>

        <?php if (!$datetime_recurring_check) : ?>
            <div class="section-container">
                <div class="dcf-input-checkbox">
                    <input
                        id="recurring"
                        name="recurring"
                        type="checkbox"
                        <?php if ($is_recurring) { echo CHECKED_INPUT; } ?>
                    >
                    <label for="recurring">This is a recurring <?php echo $recurring_type; ?> event</label>
                </div>
                <fieldset class="recurring-container date-time-select">
                    <legend class="dcf-sr-only">Recurring Event Details</legend>
                    <div class="dcf-d-flex dcf-flex-wrap dcf-ai-center dcf-col-gap-4">
                    <div class="dcf-form-group">
                        <label for="recurring-type">This event recurs</label>
                        <select id="recurring-type" name="recurring_type">
                            <option
                                value="daily"
                                <?php if($recurring_type == "daily") { echo SELECTED_INPUT; } ?>
                            >
                                Daily
                            </option>
                            <option
                                value="weekly"
                                <?php if($recurring_type == "weekly") { echo SELECTED_INPUT; } ?>
                            >
                                Weekly
                            </option>
                            <option
                                value="biweekly"
                                <?php if($recurring_type == "biweekly") { echo SELECTED_INPUT; } ?>
                            >
                                Biweekly
                            </option>
                            <optgroup label="Monthly" id="monthly-group">
                            </optgroup>
                            <option
                                value="annually"
                                <?php if($recurring_type == "annually") { echo SELECTED_INPUT; } ?>
                            >
                                Yearly
                            </option>
                        </select>
                    </div>
                    <div class="dcf-form-group dcf-datepicker">
                        <label for="recurs-until-date">until </label>
                        <input
                            id="recurs-until-date"
                            name="recurs_until_date"
                            type="text"
                            value="<?php echo $recurs_until_date; ?>"
                            aria-label="until this date in the format of mm/dd/yyyy"
                            autocomplete="off"
                        >
                    </div>
                    </div>
                </fieldset>
            </div>
        <?php endif; ?>

        <div class="dcf-form-group">
            <label for="datetime-additional-public-info">Additional Public Info For This Date & Time</label>
            <textarea
                id="datetime-additional-public-info"
                name="additional_public_info"
            ><?php
                echo $additional_public_info;
            ?></textarea>
        </div>
    </div>
    <hr>
</section>

<h2>Physical Location</h2>
<section id="physical_location_section" class="dcf-mb-8 dcf-ml-5">
    <button
        id="physical_location_add_button"
        class="dcf-btn dcf-btn-primary events-btn-location"
        type="button"
        data-controls="physical_location"
        data-add="true"
    >
        Add Physical Location
    </button>
    <input
        type="hidden"
        id="physical_location_check"
        name="physical_location_check"
        value="<?php echo $datetime_physical_location_check; ?>"
    >
    <div id="physical_location_container" class="dcf-mt-3">

    </div>
    <template id="physical_location_template">
        <div class="dcf-form-group">
            <label for="location">Location <small class="dcf-required">Required</small></label>
            <select id="location" name="location">
                <?php
                    $user_locations = \UNL\UCBCN\Manager\LocationUtility::getUserLocations();
                    $calendar_locations = \UNL\UCBCN\Manager\LocationUtility::getCalendarLocations($calendar->id);
                    $campus_locations = \UNL\UCBCN\Manager\LocationUtility::getStandardLocations(
                        \UNL\UCBCN\Location::DISPLAY_ORDER_MAIN
                    );
                    $extensions_locations = \UNL\UCBCN\Manager\LocationUtility::getStandardLocations(
                        \UNL\UCBCN\Location::DISPLAY_ORDER_EXTENSION
                    );

                    // If the location is in the standard or saved sections we want to select those
                    $standard_or_saved_check = true;
                    if (!empty($datetime_location)) {
                        $standard_or_saved_check = $datetime_location->isSavedOrStandard();
                    }
                ?>

                <option
                    <?php if (empty($location) || $location == 'new') { echo SELECTED_INPUT; } ?>
                    value="new"
                >
                    -- New Location --
                </option>

                <?php if (!$standard_or_saved_check): ?>
                    <?php //If not standard or saved we will display it ?>
                    <optgroup label="Current Location">
                        <option
                            <?php if ($location == $datetime_location->id) { echo SELECTED_INPUT; } ?>
                            value="<?php echo $datetime_location->id; ?>"
                        ><?php echo $datetime_location->name; ?></option>
                    </optgroup>
                <?php endif; ?>

                <?php if (count($user_locations) == 0): ?>
                    <optgroup label="Your saved locations (None Available)"></optgroup>
                <?php else: ?>
                    <optgroup label="Your saved locations">
                        <?php foreach ($user_locations as $loop_location): ?>
                            <option
                                <?php if ($location == $loop_location->id) { echo SELECTED_INPUT; } ?>
                                value="<?php echo $loop_location->id; ?>"
                                data-microdata="<?php echo json_encode($loop_location->microdataCheck()); ?>"
                                ><?php echo $loop_location->name; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if (count($calendar_locations) == 0): ?>
                    <optgroup label="This calendar's saved locations (None Available)"></optgroup>
                <?php else: ?>
                    <optgroup label="This calendar's saved locations">
                        <?php foreach ($calendar_locations as $loop_location): ?>
                            <option
                                <?php if ($location == $loop_location->id) { echo SELECTED_INPUT; } ?>
                                value="<?php echo $loop_location->id; ?>"
                                data-microdata="<?php echo json_encode($loop_location->microdataCheck()); ?>"
                                ><?php echo $loop_location->name; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <optgroup label="UNL Campus locations">
                    <?php foreach ($campus_locations as $loop_location): ?>
                        <option
                            <?php if ($location == $loop_location->id) { echo SELECTED_INPUT; } ?>
                            value="<?php echo $loop_location->id; ?>"
                            data-microdata="<?php echo json_encode($loop_location->microdataCheck()); ?>"
                        >
                            <?php echo $loop_location->name; ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>

                <optgroup label="Extension locations">
                    <?php foreach ($extensions_locations as $loop_location): ?>
                        <option
                            <?php if ($location == $loop_location->id) { echo SELECTED_INPUT; } ?>
                            value="<?php echo $loop_location->id; ?>"
                            data-microdata="<?php echo json_encode($loop_location->microdataCheck()); ?>"
                        >
                            <?php echo $loop_location->name; ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
        </div>
        <fieldset class="dcf-mt-6" id="new-location-fields" style="display: none;">
            <legend>New Location</legend>

            <?php echo $savvy->render($post, 'PhysicalLocationForm.tpl.php'); ?>

            <div class="dcf-d-grid dcf-grid-full dcf-grid-halves@md dcf-col-gap-5">
                <div class="dcf-form-group dcf-mt-3">
                    <div class="dcf-input-checkbox">
                        <input
                            id="location-save"
                            name="location_save"
                            type="checkbox"
                            <?php
                                if (isset($post['location_save']) &&
                                    $post['location_save'] == 'on'
                                ) { echo CHECKED_INPUT; }
                            ?>
                        >
                        <label for="location-save">
                            Save this location for your future events
                            <div class="dcf-popup dcf-d-inline" data-point="true">
                                <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-p-0" type="button">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="dcf-d-block dcf-h-5 dcf-w-5 dcf-fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path d="M11.5,1C5.159,1,0,6.159,0,12.5C0,18.841,5.159,24,11.5,24
                                            S23,18.841,23,12.5C23,6.159,17.841,1,11.5,1z M11.5,23 C5.71,23,1,18.29,1,12.5
                                            C1,6.71,5.71,2,11.5,2S22,6.71,22,12.5C22,18.29,17.29,23,11.5,23z"></path>
                                        <path d="M14.5,19H12v-8.5c0-0.276-0.224-0.5-0.5-0.5h-2
                                            C9.224,10,9,10.224,9,10.5S9.224,11,9.5,11H11v8H8.5 C8.224,19,8,19.224,8,19.5
                                            S8.224,20,8.5,20h6c0.276,0,0.5-0.224,0.5-0.5S14.776,19,14.5,19z"></path>
                                        <circle cx="11" cy="6.5" r="1"></circle>
                                        <g>
                                            <path fill="none" d="M0 0H24V24H0z"></path>
                                        </g>
                                    </svg>
                                </button>
                                <div class="dcf-popup-content unl-bg-blue dcf-p-1 dcf-rounded" style="min-width: 25ch;">
                                    <p class="dcf-m-0 dcf-regular">
                                        This allows for you to recall and edit this location easily
                                        and it will be included in the dropdown above.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="dcf-form-group dcf-mt-3">
                    <div class="dcf-input-checkbox">
                        <input
                            id="location-save-calendar"
                            name="location_save_calendar"
                            type="checkbox"
                            <?php
                                if (isset($post['location_save_calendar']) &&
                                    $post['location_save_calendar'] == 'on'
                                ) { echo CHECKED_INPUT; }
                            ?>
                        >
                        <label for="location-save-calendar">
                            Save this location for this calendar's future events
                            <div class="dcf-popup dcf-d-inline" data-point="true">
                                <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-p-0" type="button">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="dcf-d-block dcf-h-5 dcf-w-5 dcf-fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path d="M11.5,1C5.159,1,0,6.159,0,12.5C0,18.841,5.159,24,11.5,24
                                            S23,18.841,23,12.5C23,6.159,17.841,1,11.5,1z M11.5,23 C5.71,23,1,18.29,1,12.5
                                            C1,6.71,5.71,2,11.5,2S22,6.71,22,12.5C22,18.29,17.29,23,11.5,23z"></path>
                                        <path d="M14.5,19H12v-8.5c0-0.276-0.224-0.5-0.5-0.5h-2
                                            C9.224,10,9,10.224,9,10.5S9.224,11,9.5,11H11v8H8.5 C8.224,19,8,19.224,8,19.5
                                            S8.224,20,8.5,20h6c0.276,0,0.5-0.224,0.5-0.5S14.776,19,14.5,19z"></path>
                                        <circle cx="11" cy="6.5" r="1"></circle>
                                        <g>
                                            <path fill="none" d="M0 0H24V24H0z"></path>
                                        </g>
                                    </svg>
                                </button>
                                <div class="dcf-popup-content unl-bg-blue dcf-p-1 dcf-rounded" style="min-width: 25ch;">
                                    <p class="dcf-m-0 dcf-regular">
                                        This allows for you or anyone else on the calendar to recall and edit 
                                        this location easily and it will be included in the dropdown above.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </fieldset>
        <div class="dcf-form-group">
            <label for="room">Event Specific - Room</label>
            <input id="room" name="room" type="text" value="<?php echo $location_room; ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="directions">Event Specific - Directions</label>
            <textarea id="directions" name="directions"><?php echo $location_directions; ?></textarea>
        </div>
        <div class="dcf-form-group">
            <label for="additional-public-info">Event Specific - Additional Public Info</label>
            <textarea
                id="additional-public-info"
                name="l_additional_public_info"
            ><?php
                echo $location_additional_public_info;
            ?></textarea>
        </div>
    </template>
    <hr>
</section>

<h2>Virtual Location</h2>
<section id="virtual_location_section" class="dcf-mb-8 dcf-ml-5">
    <button
        id="virtual_location_add_button"
        class="dcf-btn dcf-btn-primary events-btn-location"
        type="button"
        data-controls="virtual_location"
        data-add="true"
    >
        Add Virtual Location
    </button>
    <input
        type="hidden"
        id="virtual_location_check"
        name="virtual_location_check"
        value="<?php echo $datetime_virtual_location_check; ?>"
    >
    <div id="virtual_location_container" class="dcf-mt-3">

    </div>
    <template id="virtual_location_template">
        <div class="dcf-form-group">
            <label for="v-location">
                Virtual Location
                <small class="dcf-required">Required</small>
            </label>
            <select id="v-location" name="v_location">
                <?php
                    $user_webcasts = \UNL\UCBCN\Manager\WebcastUtility::getUserWebcasts();
                    $calendar_webcasts = \UNL\UCBCN\Manager\WebcastUtility::getCalendarWebcasts($calendar->id);

                    // If the webcast is in the saved section we want to select that one
                    $standard_or_saved_check = true;
                    if (!empty($datetime_v_location)) {
                        $standard_or_saved_check = $datetime_v_location->isSaved();
                    }
                ?>

                <option
                    <?php if (empty($v_location) || $v_location == 'new') { echo SELECTED_INPUT; } ?>
                    value="new"
                >
                    -- New Location --
                </option>

                <?php if (!$standard_or_saved_check): ?>
                    <?php //If not standard or saved we will display it ?>
                    <optgroup label="Current Location">
                        <option
                            <?php if ($v_location == $datetime_v_location->id) { echo SELECTED_INPUT; } ?>
                            value="<?php echo $datetime_v_location->id; ?>"

                        ><?php echo $datetime_v_location->title; ?></option>
                    </optgroup>
                <?php endif; ?>

                <?php if (count($user_webcasts) == 0):?>
                    <optgroup label="Your saved locations (None Available)"></optgroup>
                <?php else: ?>
                    <optgroup label="Your saved locations">
                        <?php foreach ($user_webcasts as $webcast): ?>
                            <option
                                <?php if ($v_location == $webcast->id) { echo SELECTED_INPUT; } ?>
                                value="<?php echo $webcast->id; ?>"
                            >
                                <?php echo $webcast->title; ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

                <?php if (count($calendar_webcasts) == 0):?>
                    <optgroup label="This calendar's saved locations (None Available)"></optgroup>
                <?php else: ?>
                    <optgroup label="This calendar's saved locations">
                        <?php foreach ($calendar_webcasts as $webcast): ?>
                            <option
                                <?php if ($v_location == $webcast->id) { echo SELECTED_INPUT; } ?>
                                value="<?php echo $webcast->id; ?>"
                            >
                                <?php echo $webcast->title; ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>

            </select>
        </div>
        <fieldset class="dcf-mt-6" id="new-v-location-fields">
            <legend>New Virtual Location</legend>

            <?php echo $savvy->render($post, 'VirtualLocationForm.tpl.php'); ?>

            <div class="dcf-d-grid dcf-grid-full dcf-grid-halves@md">
                <div class="dcf-form-group dcf-mt-3">
                    <div class="dcf-input-checkbox">
                        <input
                            id="v-location-save"
                            name="v_location_save"
                            type="checkbox"
                            <?php
                                if (isset($post['v_location_save']) &&
                                    $post['v_location_save'] == 'on'
                                ) { echo CHECKED_INPUT; }
                            ?>
                        >
                        <label for="v-location-save">
                            Save this location for your future events
                            <div class="dcf-popup dcf-d-inline" data-point="true">
                                <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-p-0" type="button">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="dcf-d-block dcf-h-5 dcf-w-5 dcf-fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path d="M11.5,1C5.159,1,0,6.159,0,12.5C0,18.841,5.159,24,11.5,24
                                            S23,18.841,23,12.5C23,6.159,17.841,1,11.5,1z M11.5,23 C5.71,23,1,18.29,1,12.5
                                            C1,6.71,5.71,2,11.5,2S22,6.71,22,12.5C22,18.29,17.29,23,11.5,23z"></path>
                                        <path d="M14.5,19H12v-8.5c0-0.276-0.224-0.5-0.5-0.5h-2
                                            C9.224,10,9,10.224,9,10.5S9.224,11,9.5,11H11v8H8.5 C8.224,19,8,19.224,8,19.5
                                            S8.224,20,8.5,20h6c0.276,0,0.5-0.224,0.5-0.5S14.776,19,14.5,19z"></path>
                                        <circle cx="11" cy="6.5" r="1"></circle>
                                        <g>
                                            <path fill="none" d="M0 0H24V24H0z"></path>
                                        </g>
                                    </svg>
                                </button>
                                <div class="dcf-popup-content unl-bg-blue dcf-p-1 dcf-rounded" style="min-width: 25ch;">
                                    <p class="dcf-m-0 dcf-regular">
                                        This allows for you to recall and edit this virtual 
                                        location easily and it will be included in the dropdown above.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="dcf-form-group dcf-mt-3">
                    <div class="dcf-input-checkbox">
                        <input
                            id="v-location-save-calendar"
                            name="v_location_save_calendar"
                            type="checkbox"
                            <?php
                                if (isset($post['v_location_save_calendar']) &&
                                    $post['v_location_save_calendar'] == 'on'
                                ) { echo CHECKED_INPUT; }
                            ?>
                        >
                        <label for="v-location-save-calendar">
                            Save this location for this calendar's future events
                            <div class="dcf-popup dcf-d-inline" data-point="true">
                                <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-p-0" type="button">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="dcf-d-block dcf-h-5 dcf-w-5 dcf-fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path d="M11.5,1C5.159,1,0,6.159,0,12.5C0,18.841,5.159,24,11.5,24
                                            S23,18.841,23,12.5C23,6.159,17.841,1,11.5,1z M11.5,23 C5.71,23,1,18.29,1,12.5
                                            C1,6.71,5.71,2,11.5,2S22,6.71,22,12.5C22,18.29,17.29,23,11.5,23z"></path>
                                        <path d="M14.5,19H12v-8.5c0-0.276-0.224-0.5-0.5-0.5h-2
                                            C9.224,10,9,10.224,9,10.5S9.224,11,9.5,11H11v8H8.5 C8.224,19,8,19.224,8,19.5
                                            S8.224,20,8.5,20h6c0.276,0,0.5-0.224,0.5-0.5S14.776,19,14.5,19z"></path>
                                        <circle cx="11" cy="6.5" r="1"></circle>
                                        <g>
                                            <path fill="none" d="M0 0H24V24H0z"></path>
                                        </g>
                                    </svg>
                                </button>
                                <div class="dcf-popup-content unl-bg-blue dcf-p-1 dcf-rounded" style="min-width: 25ch;">
                                    <p class="dcf-m-0 dcf-regular">
                                        This allows for you or anyone else on the calendar to recall and edit 
                                        this virtual location easily and it will be included in the dropdown above.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </fieldset>
        <div class="dcf-form-group">
            <label for="v-additional-public-info">Event Specific - Additional Public Info</label>
            <textarea
                id="v-additional-public-info"
                name="v_additional_public_info"
            ><?php
                echo $v_location_additional_public_info;
            ?></textarea>
        </div>
    </template>
    <hr>
</section>

<?php
//Set up javascript stuff
$js_recurring_type = $recurring_type ?? '';
$js_recurring_month = $recurring_month_type ?? '';

$page->addScriptDeclaration("const recurringType = '" . $js_recurring_type . "';");
$page->addScriptDeclaration("const recurringMonth = '" . $js_recurring_month . "';");

$page->addScript(
    $base_frontend_url .
    'templates/default/html/js/manager-event-form-date-time.min.js?v='.
    UNL\UCBCN\Frontend\Controller::$version
);
