<?php
    const CHECKED_INPUT = 'checked="checked"';

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
<div>
    <form class="dcf-form" id="create-event-form" action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">

        <h2>Event Details</h2>
        <section class="dcf-mb-8 dcf-ml-5">
            <div class="dcf-form-group">
                <label for="title">Title <small class="dcf-required">Required</small></label>
                <input id="title" name="title" type="text" class="dcf-w-100%" value="<?php echo $event->title; ?>" />
            </div>
            <div class="dcf-form-group">
                <label for="subtitle">Subtitle</label>
                <input id="subtitle" name="subtitle" type="text" class="dcf-w-100%" value="<?php echo $event->subtitle; ?>" />
            </div>
            <div class="dcf-form-group">
                <label for="description">Description <small class="required-for-main-calendar dcf-required" style="display: none">Required</small></label>
                <textarea id="description" name="description" rows="4" ><?php echo $event->description; ?></textarea>
            </div>
            <div class="dcf-form-group">
                <label for="type">Type</label>
                <select class="dcf-w-100%" id="type" name="type">
                    <?php foreach ($context->getEventTypes() as $type) { ?>
                        <option <?php if (isset($post['type']) && $post['type'] == $type->id) echo 'selected="selected"' ?> value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="dcf-form-group">
                <div class="dcf-input-checkbox">
                    <input id="canceled" name="canceled" type="checkbox" value="1" <?php if ($event->isCanceled()) { echo CHECKED_INPUT; } ?>>
                    <label for="canceled">Event Canceled</label>
                </div>
            </div>
            <hr>
        </section>
        

        <h2>Date &amp; Time</h2>
        <section class="dcf-mb-8 dcf-ml-5">
            <div id="datetime_container">
                <div class="dcf-form-group">
                    <label class="dcf-mt-2" for="timezone">Time Zone <small class="dcf-required">Required</small></label>
                    <select id="timezone"" name="timezone" aria-label="Time Zone">
                    <?php
                    $timezone = $calendar->defaulttimezone;
                    if (!empty($post['timezone'])) {
                        $timezone = $post['timezone'];
                    };
                    foreach (UNL\UCBCN::getTimezoneOptions() as $tzName => $tzValue) { ?>
                    <option <?php if ($timezone == $tzValue) echo 'selected="selected"'; ?> value="<?php echo $tzValue ?>"><?php echo $tzName ?></option>
                    <?php } ?>
                    </select>
                </div>
                <fieldset>
                    <legend>Start Date &amp; Time <small class="dcf-required">Required</small></legend>
                    <div class="dcf-d-flex dcf-flex-wrap dcf-ai-center dcf-col-gap-4">
                        <div class="dcf-form-group dcf-datepicker dcf-flex-grow-1">
                            <input id="start-date" name="start_date" type="text" value="<?php if (isset($post['start_date'])) { echo $post['start_date']; } ?>" aria-label="Start Date in the format of mm/dd/yyyy" autocomplete="off" />
                        </div>
                        <div class="dcf-form-group dcf-d-flex dcf-ai-center dcf-flex-grow-1">
                            <span class="dcf-pr-2">@</span>
                            <select class="dcf-flex-grow-1" id="start-time-hour" name="start_time_hour" aria-label="Start Time Hour">
                                <option value="">Hour</option>
                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                <option <?php if (isset($post['start_time_hour']) && $post['start_time_hour'] == $i) echo 'selected="selected"'; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                            <?php } ?>
                            </select>
                            <span class="dcf-pr-1 dcf-pl-1">:</span>
                            <select class="dcf-flex-grow-1" id="start-time-minute" name="start_time_minute" aria-label="Start Time Minute">
                                <?php //TODO: Change this to a loop ?>
                                <option value="">Minute</option>
                                <option <?php if (isset($post['start_time_minute']) && ($post['start_time_minute'] === 0 || $post['start_time_minute'] === '0')) echo 'selected="selected"'; ?> value="0">00</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 5) echo 'selected="selected"'; ?> value="5">05</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 10) echo 'selected="selected"'; ?> value="10">10</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 15) echo 'selected="selected"'; ?> value="15">15</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 20) echo 'selected="selected"'; ?> value="20">20</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 25) echo 'selected="selected"'; ?> value="25">25</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 30) echo 'selected="selected"'; ?> value="30">30</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 35) echo 'selected="selected"'; ?> value="35">35</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 40) echo 'selected="selected"'; ?> value="40">40</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 45) echo 'selected="selected"'; ?> value="45">45</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 50) echo 'selected="selected"'; ?> value="50">50</option>
                                <option <?php if (isset($post['start_time_minute']) && $post['start_time_minute'] == 55) echo 'selected="selected"'; ?> value="55">55</option>
                            </select>
                            <fieldset class="dcf-d-flex dcf-flex-col dcf-row-gap-3 dcf-ai-center dcf-col-gap-3 dcf-mb-0 dcf-ml-4 dcf-p-0 dcf-b-0 dcf-txt-sm" id="start-time-am-pm">
                                <legend class="dcf-sr-only">AM/PM</legend>
                                <div class="dcf-input-radio dcf-mb-0">
                                    <input id="start-time-am-pm-am" name="start_time_am_pm" type="radio" value="am" <?php if (!isset($post) || $post['start_time_am_pm'] == 'am') { echo CHECKED_INPUT; } ?>>
                                    <label class="dcf-mb-0" for="start-time-am-pm-am">AM</label>
                                </div>
                                <div class="dcf-input-radio dcf-mb-0">
                                    <input id="start-time-am-pm-pm" name="start_time_am_pm" type="radio" value="pm" <?php if (isset($post['start_time_am_pm']) && $post['start_time_am_pm'] == 'pm') { echo CHECKED_INPUT; } ?>>
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
                            <input id="end-date" name="end_date" type="text" value="<?php if (isset($post['end_date'])) { echo $post['end_date']; } ?>" aria-label="End Date in the format of mm/dd/yyyy" autocomplete="off" />
                        </div>
                        <div class="dcf-form-group dcf-d-flex dcf-ai-center dcf-flex-grow-1">
                            <span class="dcf-pr-2">@</span>
                            <select class="dcf-flex-grow-1" id="end-time-hour" name="end_time_hour" aria-label="End Time Hour">
                                <option value="">Hour</option>
                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                <option <?php if (isset($post['end_time_hour']) && $post['end_time_hour'] == $i) echo 'selected="selected"'; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                            <?php } ?>
                            </select>
                            <span class="dcf-pr-1 dcf-pl-1">:</span>
                            <select class="dcf-flex-grow-1" id="end-time-minute" name="end_time_minute" aria-label="End Time Minute">
                                <?php //TODO: Change this to a loop ?>
                                <option value="">Minute</option>
                                <option <?php if (isset($post['end_time_minute']) && ($post['end_time_minute'] === 0 || $post['end_time_minute'] === '0')) echo 'selected="selected"'; ?> value="0">00</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 5) echo 'selected="selected"'; ?> value="5">05</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 10) echo 'selected="selected"'; ?> value="10">10</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 15) echo 'selected="selected"'; ?> value="15">15</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 20) echo 'selected="selected"'; ?> value="20">20</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 25) echo 'selected="selected"'; ?> value="25">25</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 30) echo 'selected="selected"'; ?> value="30">30</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 35) echo 'selected="selected"'; ?> value="35">35</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 40) echo 'selected="selected"'; ?> value="40">40</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 45) echo 'selected="selected"'; ?> value="45">45</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 50) echo 'selected="selected"'; ?> value="50">50</option>
                                <option <?php if (isset($post['end_time_minute']) && $post['end_time_minute'] == 55) echo 'selected="selected"'; ?> value="55">55</option>
                            </select>
                            <fieldset class="dcf-d-flex dcf-flex-col dcf-row-gap-3 dcf-ai-center dcf-col-gap-3 dcf-mb-0 dcf-ml-4 dcf-p-0 dcf-b-0 dcf-txt-sm" id="end-time-am-pm">
                                <legend class="dcf-sr-only">AM/PM</legend>
                                <div class="dcf-input-radio dcf-mb-0">
                                    <input id="end-time-am-pm-am" name="end_time_am_pm" type="radio" value="am" <?php if (empty($post) || $post['end_time_am_pm'] == 'am') { echo CHECKED_INPUT; } ?>>
                                    <label class="dcf-mb-0" for="end-time-am-pm-am">AM</label>
                            </div>
                            <div class="dcf-input-radio dcf-mb-0">
                                    <input id="end-time-am-pm-pm" name="end_time_am_pm" type="radio" value="pm" <?php if (isset($post['end_time_am_pm']) && $post['end_time_am_pm'] == 'pm') { echo CHECKED_INPUT; } ?>>
                                    <label class="dcf-mb-0" for="end-time-am-pm-pm">PM</label>
                            </div>
                            </fieldset>
                        </div>
                    </div>
                </fieldset>
                <div class="section-container">
                    <div class="dcf-input-checkbox">
                        <input id="recurring" name="recurring" type="checkbox" <?php if (isset($post['recurring'])) { echo CHECKED_INPUT; } ?>>
                        <label for="recurring">This is a recurring event</label>
                    </div>
                    <fieldset class="recurring-container date-time-select">
                        <legend class="dcf-sr-only">Recurring Event Details</legend>
                        <div class="dcf-d-flex dcf-flex-wrap dcf-ai-center dcf-col-gap-4">
                        <div class="dcf-form-group">
                            <label for="recurring-type">This event recurs</label>
                            <select id="recurring-type" name="recurring_type">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="biweekly">Biweekly</option>
                                <optgroup label="Monthly" id="monthly-group">
                                </optgroup>
                                <option value="annually">Yearly</option>
                            </select>
                        </div>
                        <div class="dcf-form-group dcf-datepicker">
                            <label for="recurs-until-date">until </label>
                            <input id="recurs-until-date" name="recurs_until_date" type="text" value="<?php if (isset($post['recurs_until_date'])) { echo $post['recurs_until_date']; } ?>" aria-label="until this date in the format of mm/dd/yyyy" autocomplete="off" />
                        </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <hr>
        </section>

        <h2>Physical Location</h2>
        <section id="physical_location_section" class="dcf-mb-8 dcf-ml-5">
            <button id="physical_location_add_button" class="dcf-btn dcf-btn-primary events-btn-location" type="button" data-controls="physical_location" data-add="true">Add Physical Location</button>
            <input type="hidden" id="physical_location_check" name="physical_location_check" value="0">
            <div id="physical_location_container" class="dcf-mt-3">

            </div>
            <template id="physical_location_template">
                <div class="dcf-form-group">
                    <label for="location">Location <small class="dcf-required">Required</small></label>
                    <select id="location" name="location">
                        <optgroup label="Your saved locations">
                            <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getUserLocations() as $location): ?>
                                <option <?php if (isset($post['location']) && $post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>" data-microdata="<?php echo json_encode($location->microdata_check()); ?>"><?php echo $location->name ?></option>
                            <?php endforeach ?>
                        <option <?php if (isset($post['location']) && $post['location'] == 'new') echo 'selected="selected"' ?>value="new">-- New Location --</option>
                        </optgroup>
                        <optgroup label="UNL Campus locations">
                            <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_MAIN) as $location): ?>
                                <option <?php if (isset($post['location']) && $post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>" data-microdata="<?php echo json_encode($location->microdata_check()); ?>"><?php echo $location->name ?></option>
                            <?php endforeach ?>
                        </optgroup>
                        <optgroup label="Extension locations">
                            <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_EXTENSION) as $location): ?>
                                <option <?php if (isset($post['location']) && $post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>" data-microdata="<?php echo json_encode($location->microdata_check()); ?>"><?php echo $location->name ?></option>
                            <?php endforeach ?>
                        </optgroup>
                    </select>
                </div>
                <fieldset class="dcf-mt-6" id="new-location-fields" style="display: none;">
                    <legend>New Location</legend>
                    <?php //These names need to match /UNL/UCBCN/Manager/LocationUtility ?>
                    <div class="dcf-d-grid dcf-grid-full dcf-grid-halves@md dcf-col-gap-5">
                        <div class="dcf-form-group" style="grid-column: span 2;">
                            <label for="location-name">Name <small class="dcf-required">Required</small></label>
                            <input id="location-name" class="dcf-w-100%" name="new_location[name]" type="text" value="<?php echo isset($post['location']['name']) ? $post['location']['name']: ''; ?>">
                        </div>

                        <div class="dcf-form-group">
                            <label for="location-address-1">Address <small class="dcf-required">Required</small></label>
                            <input id="location-address-1" class="dcf-w-100%" name="new_location[streetaddress1]" type="text" value="<?php echo isset($post['location']['streetaddress1']) ? $post['location']['streetaddress1']: ''; ?>">
                        </div>
                        <div class="dcf-form-group">
                            <label for="location-address-2">Address 2</label>
                            <input id="location-address-2" class="dcf-w-100%" name="new_location[streetaddress2]" type="text" value="<?php echo isset($post['location']['streetaddress2']) ? $post['location']['streetaddress2']: ''; ?>">
                        </div>
                        
                        <div class="dcf-form-group">
                            <label for="location-city">City <small class="dcf-required">Required</small></label>
                            <input id="location-city" class="dcf-w-100%" name="new_location[city]" type="text" value="<?php echo isset($post['location']['city']) ? $post['location']['city']: ''; ?>">
                        </div>
                        <div class="dcf-form-group">
                            <label for="location-state">State <small class="dcf-required">Required</small></label>
                            <?php $states = array(
                                            "AL" => "Alabama"       ,
                                            "AK" => "Alaska"        ,
                                            "AZ" => "Arizona"       ,
                                            "AR" => "Arkansas"      ,
                                            "CA" => "California"    ,
                                            "CO" => "Colorado"      ,
                                            "CT" => "Connecticut"   ,
                                            "DE" => "Delaware"      ,
                                            "FL" => "Florida"       ,
                                            "GA" => "Georgia"       ,
                                            "HI" => "Hawaii"        ,
                                            "ID" => "Idaho"         ,
                                            "IL" => "Illinois"      ,
                                            "IN" => "Indiana"       ,
                                            "IA" => "Iowa"          ,
                                            "KS" => "Kansas"        ,
                                            "KY" => "Kentucky"      ,
                                            "LA" => "Louisiana"     ,
                                            "ME" => "Maine"         ,
                                            "MD" => "Maryland"      ,
                                            "MA" => "Massachusetts" ,
                                            "MI" => "Michigan"      ,
                                            "MN" => "Minnesota"     ,
                                            "MS" => "Mississippi"   ,
                                            "MO" => "Missouri"      ,
                                            "MT" => "Montana"       ,
                                            "NE" => "Nebraska"      ,
                                            "NV" => "Nevada"        ,
                                            "NH" => "New Hampshire" ,
                                            "NJ" => "New Jersey"    ,
                                            "NM" => "New Mexico"    ,
                                            "NY" => "New York"      ,
                                            "NC" => "North Carolina",
                                            "ND" => "North Dakota"  ,
                                            "OH" => "Ohio"          ,
                                            "OK" => "Oklahoma"      ,
                                            "OR" => "Oregon"        ,
                                            "PA" => "Pennsylvania"  ,
                                            "RI" => "Rhode Island"  ,
                                            "SC" => "South Carolina",
                                            "SD" => "South Dakota"  ,
                                            "TN" => "Tennessee"     ,
                                            "TX" => "Texas"         ,
                                            "UT" => "Utah"          ,
                                            "VT" => "Vermont"       ,
                                            "VA" => "Virginia"      ,
                                            "WA" => "Washington"    ,
                                            "WV" => "West Virginia" ,
                                            "WI" => "Wisconsin"     ,
                                            "WY" => "Wyoming"
                                        );
                            ?>
                            <select name="new_location[state]" id="location-state">
                                <?php foreach($states as $abbr => $state): ?>
                                    <option value="<?php echo $abbr; ?>"
                                        <?php if (isset($post['location']['state']) && $post['location']['state'] == $abbr): ?>
                                            selected="selected"
                                        <?php elseif (!isset($post['location']['state']) && $abbr == 'NE'): ?>
                                            selected="selected"
                                        <?php endif; ?>
                                    >
                                        <?php echo $state; ?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                        </div>

                        <div class="dcf-form-group">
                            <label for="location-zip"><abbr title="Zone Improvement Plan">ZIP</abbr> Code <small class="dcf-required">Required</small></label>
                            <input id="location-zip" class="dcf-w-100%" name="new_location[zip]" type="text" value="<?php echo isset($post['location']['zip']) ? $post['location']['zip']: ''; ?>">
                        </div>
                        

                        <hr class="dcf-mb-5" style="grid-column: span 2;">

                        <div class="dcf-form-group">
                            <label for="location-map-url">Map <abbr title="Uniform Resource Locator">URL</abbr></label>
                            <input id="location-map-url" class="dcf-w-100%" name="new_location[mapurl]" type="text" value="<?php echo isset($post['location']['mapurl']) ? $post['location']['mapurl']: ''; ?>">
                        </div>
                        <div class="dcf-form-group">
                            <label for="location-webpage">Web Page</label>
                            <input id="location-webpage" class="dcf-w-100%" name="new_location[webpageurl]" type="text" value="<?php echo isset($post['location']['webpageurl']) ? $post['location']['webpageurl']: ''; ?>">
                        </div>

                        <div class="dcf-form-group">
                            <label for="location-hours">Hours</label>
                            <input id="location-hours" class="dcf-w-100%" name="new_location[hours]" ype="text" value="<?php echo isset($post['location']['hours']) ? $post['location']['hours']: ''; ?>">
                        </div>
                        <div class="dcf-form-group">
                            <label for="location-phone">Phone</label>
                            <input id="location-phone" class="dcf-w-100%" name="new_location[phone]" type="text" value="<?php echo isset($post['location']['phone']) ? $post['location']['phone']: ''; ?>">
                        </div>

                        <hr class="dcf-mb-5" style="grid-column: span 2;">

                        <div class="dcf-form-group" style="grid-column: span 2;">
                            <label for="location-room">Location Default - Room</label>
                            <input id="location-room" name="new_location[room]" type="text" value="<?php echo isset($post['location']['room']) ? $post['location']['room']: ''; ?>">
                        </div>
                        
                        <div class="dcf-form-group" style="grid-column: span 2;">
                            <label for="location-directions">Location Default - Directions</label>
                            <textarea id="location-directions" name="new_location[directions]"><?php echo isset($post['location']['directions']) ? $post['location']['directions']: ''; ?></textarea>
                        </div>

                        <div class="dcf-form-group" style="grid-column: span 2;">
                            <label for="location-additional-public-info">Location Default - Additional Public Info</label>
                            <textarea id="location-additional-public-info" name="new_location[additionalpublicinfo]"><?php echo isset($post['location']['additionalpublicinfo']) ? $post['location']['additionalpublicinfo']: ''; ?></textarea>
                        </div>
                        
                        <div class="dcf-form-group dcf-mt-3" style="grid-column: span 2;">
                            <div class="dcf-input-checkbox">
                                <input id="location-save" name="location_save" type="checkbox" <?php if (isset($post['location_save']) && $post['location_save'] == 'on') { echo CHECKED_INPUT; } ?>>
                                <label for="location-save">Save this location for your future events</label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="dcf-form-group">
                    <label for="room">Event Specific - Room</label>
                    <input id="room" name="room" type="text" value="<?php if (isset($post['room'])) { echo $post['room']; } ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="directions">Event Specific - Directions</label>
                    <textarea id="directions" name="directions"><?php if (isset($post['directions'])) { echo $post['directions']; } ?></textarea>
                </div>
                <div class="dcf-form-group">
                    <label for="additional-public-info">Event Specific - Additional Public Info</label>
                    <textarea id="additional-public-info" name="additional_public_info"><?php if (isset($post['additional_public_info'])) { echo $post['additional_public_info']; } ?></textarea>
                </div>
            </template>
            <hr>
        </section>

        <h2>Virtual Location</h2>
        <section id="virtual_location_section" class="dcf-mb-8 dcf-ml-5">
            <button id="virtual_location_add_button" class="dcf-btn dcf-btn-primary events-btn-location" type="button" data-controls="virtual_location" data-add="true">Add Virtual Location</button>
            <input type="hidden" id="virtual_location_check" name="virtual_location_check" value="0">
            <div id="virtual_location_container" class="dcf-mt-3">

            </div>
            <template id="virtual_location_template">
                <div class="dcf-form-group">
                    <label for="v-location">Virtual Location <small class="dcf-required">Required</small></label>
                    <select id="v-location" name="v_location">
                        <optgroup label="Your saved virtual locations">
                            <option <?php if (isset($post['v_location']) && $post['v_location'] == 'new') echo 'selected="selected"' ?>value="new">-- New Location --</option>
                            <option value="tn_zoom">Tommy Neumann Zoom</option>
                        </optgroup>
                    </select>
                </div>
                <fieldset class="dcf-mt-6" id="new-v-location-fields">
                    <legend>New Virtual Location</legend>
                    <?php //These names need to match /UNL/UCBCN/Manager/WebcastUtility ?>
                    <div class="dcf-form-group">
                        <label for="new-v-location-name">Name <small class="dcf-required">Required</small></label>
                        <input id="new-v-location-name" name="new_v_location[title]" type="text" class="dcf-w-100%" value="<?php echo isset($post['new_v_location']['title']) ? $post['new_v_location']['title']: ''; ?>">
                    </div>
                    <div class="dcf-form-group">
                        <label for="new-v-location-url">URL<small class="dcf-required">Required</small></label>
                        <input id="new-v-location-url" name="new_v_location[url]" type="text" class="dcf-w-100%" value="<?php echo isset($post['new_v_location']['url']) ? $post['new_v_location']['url']: ''; ?>">
                    </div>
                    <div class="dcf-form-group">
                        <label for="new-v-location-additional-public-info">Location Default - Additional Public Info</label>
                        <textarea id="new-v-location-additional-public-info" name="new_v_location[additionalinfo]"><?php if (isset($post['new_v_location']['additionalinfo'])) { echo $post['new_v_location']['additionalinfo']; } ?></textarea>
                    </div>
                    <div class="dcf-form-group dcf-mt-3">
                        <div class="dcf-input-checkbox">
                            <input id="v-location-save" name="v_location_save" type="checkbox" <?php if (isset($post['v_location_save']) && $post['v_location_save'] == 'on') { echo CHECKED_INPUT; } ?>>
                            <label for="v-location-save">Save this location for your future events</label>
                        </div>
                    </div>
                </fieldset>
                <div class="dcf-form-group">
                    <label for="v-additional-public-info">Event Specific - Additional Public Info</label>
                    <textarea id="v-additional-public-info" name="v_additional_public_info"><?php if (isset($post['v_additional_public_info'])) { echo $post['v_additional_public_info']; } ?></textarea>
                </div>
            </template>
            <hr>
        </section>


        <?php echo $savvy->render($context , 'EventFormImageUpload.tpl.php'); ?>

        <h2>Sharing</h2>
        <section class="dcf-mb-8 dcf-ml-5">
            <div class="details dcf-grid dcf-col-gap-vw">
                <fieldset class="dcf-col-100% dcf-col-25%-start@sm dcf-p-0 dcf-b-0">
                    <legend class="dcf-pb-2">Privacy</legend>
                    <div class="dcf-input-radio">
                        <input id="sharing-private" name="private_public" type="radio" value="private" <?php if (!empty($post['private_public']) && $post['private_public'] == 'private') { echo CHECKED_INPUT; } ?>>
                        <label for="sharing-private">Private</label>
                    </div>
                    <div class="dcf-input-radio">
                        <input id="sharing-public" name="private_public" type="radio" value="public" <?php if (!empty($post['private_public']) && $post['private_public'] != 'private') { echo CHECKED_INPUT; } ?>>
                        <label for="sharing-public">Public</label>
                    </div>
                </fieldset>
                <fieldset class="dcf-col-100% dcf-col-75%-end@sm dcf-mb-0 dcf-p-0 dcf-b-0" id="send_to_main">
                    <legend class="dcf-pb-2">Consider for Main <abbr title="University of Nebraska–Lincoln"">UNL</abbr> Calendar <small class="dcf-required">Required</small></legend>
                    <div class="dcf-input-radio">
                        <input id="send_to_main_on" name="send_to_main" type="radio" value="on" <?php if (!empty($post['send_to_main']) && $post['send_to_main'] == 'on') { echo CHECKED_INPUT; } ?>/>
                        <label for="send_to_main_on">Yes</label>
                    </div>
                    <div class="dcf-input-radio">
                        <input id="send_to_main_off" name="send_to_main" type="radio" value="off" <?php if (!empty($post['send_to_main']) && $post['send_to_main'] == 'off') { echo CHECKED_INPUT; } ?>/>
                        <label for="send_to_main_off">No</label>
                    </div>
                </fieldset>
            </div>
            <hr>
        </section>

        <h2>Contact Info</h2>
        <section class="dcf-mb-8 dcf-ml-5">
            <div class="details dcf-d-grid dcf-grid-full dcf-grid-halves@md dcf-col-gap-vw">
                <div class="dcf-form-group">
                    <label for="contact-name">Name <small class="required-for-main-calendar dcf-required" style="display: none">Required</small></label>
                    <input id="contact-name" name="contact_name" type="text" class="dcf-w-100%" value="<?php if (isset($post['contact_name'])) { echo $post['contact_name']; } ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="contact-email">Email</label>
                    <input id="contact-email" name="contact_email" type="text" class="dcf-w-100%" value="<?php if (isset($post['contact_email'])) { echo $post['contact_email']; } ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="contact-phone">Phone</label>
                    <input id="contact-phone" name="contact_phone" type="text" class="dcf-w-100%" value="<?php if (isset($post['contact_phone'])) { echo $post['contact_phone']; } ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="website">Event Website</label>
                    <input id="website" name="website" type="text" class="dcf-w-100%" value="<?php echo $event->webpageurl ?>" />
                </div>
            </div>
            <hr>
        </section>
        <button class="dcf-btn dcf-btn-primary" type="submit">Submit Event</button>
        <button id="google-microdata-button" class="dcf-btn-toggle-modal dcf-btn unl-cream unl-cream@dark dcf-mt-3" title="Learn More" style="background-color:var(--bg-brand-eta); border-color: var(--bg-brand-eta);" type="button" data-toggles-modal="google-microdata-modal" disabled>
            ! Your event does not reach google microdata requirements !
        </button>

        <div class="dcf-modal" id="google-microdata-modal" hidden>
            <div class="dcf-modal-wrapper">
                <div class="dcf-modal-header">
                    <h2>Info About Google Microdata</h2>
                    <button class="dcf-btn-close-modal">Close</button>
                </div>
                <div class="dcf-modal-content">
                    Info about google microdata
                    <div class="dcf-mt-5" id="google-microdata-modal-output">
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<?php
$recurringType = !empty($post['recurring_type']) ? $post['recurring_type'] : 'none';
$page->addScriptDeclaration("const recurringType = '" . $recurringType . "';");
$page->addScript($base_frontend_url .'templates/default/html/js/manager-create-event.min.js?v='.UNL\UCBCN\Frontend\Controller::$version);
