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
        <fieldset>
            <legend>Event Details</legend>
            <div class="dcf-form-group">
                <label for="title">Title <small class="dcf-required">Required</small></label>
                <input id="title" name="title" type="text" value="<?php echo $event->title; ?>" />
            </div>
            <div class="dcf-form-group">
                <label for="subtitle">Subtitle</label>
                <input id="subtitle" name="subtitle" type="text" value="<?php echo $event->subtitle; ?>" />
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
            <fieldset>
                <legend>Target Audience</legend>
                <?php foreach ($context->getAudiences() as $audience): ?>
                    <?php $target_audience_id = 'target-audience-' . $audience->id; ?>
                    <div class="dcf-input-checkbox">
                        <input 
                            id="<?php echo($target_audience_id); ?>"
                            name="<?php echo($target_audience_id); ?>"
                            type="checkbox"
                            value="<?php echo($audience->id); ?>"
                            <?php if (isset($post[$target_audience_id]) && $post[$target_audience_id] == $audience->id) echo CHECKED_INPUT; ?>
                        >
                        <label for="<?php echo($target_audience_id); ?>">
                            <?php echo($audience->name); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </fieldset>
            <div class="dcf-form-group">
                <div class="dcf-input-checkbox">
                    <input id="canceled" name="canceled" type="checkbox" value="1" <?php if ($event->isCanceled()) { echo CHECKED_INPUT; } ?>>
                    <label for="canceled">Event Canceled</label>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>Location, Date &amp; Time</legend>
            <div class="dcf-form-group">
                <label for="location">Location <small class="dcf-required">Required</small></label>
                <select id="location" name="location">
                    <optgroup label="Your saved locations">
                        <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getUserLocations() as $location): ?>
                          <option <?php if (isset($post['location']) && $post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                        <?php endforeach ?>
                      <option <?php if (isset($post['location']) && $post['location'] == 'new') echo 'selected="selected"' ?>value="new">-- New Location --</option>
                    </optgroup>
                    <optgroup label="UNL Campus locations">
                        <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_MAIN) as $location): ?>
                          <option <?php if (isset($post['location']) && $post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                        <?php endforeach ?>
                    </optgroup>
                    <optgroup label="Extension locations">
                        <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_EXTENSION) as $location): ?>
                          <option <?php if (isset($post['location']) && $post['location'] == $location->id) echo 'selected="selected"' ?> value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                        <?php endforeach ?>
                    </optgroup>
                </select>
            </div>
            <fieldset class="dcf-mt-6" id="new-location-fields" style="display: none;">
                <legend>New Location</legend>
                <div class="dcf-form-group">
                    <label for="location-name">Name <small class="dcf-required">Required</small></label>
                    <input id="location-name" name="new_location[name]" type="text" value="<?php echo isset($post['location']['name']) ? $post['location']['name']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-address-1">Address</label>
                    <input id="location-address-1" name="new_location[streetaddress1]" type="text" value="<?php echo isset($post['location']['streetaddress1']) ? $post['location']['streetaddress1']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-address-2">Address 2</label>
                    <input id="location-address-2" name="new_location[streetaddress2]" type="text" value="<?php echo isset($post['location']['streetaddress2']) ? $post['location']['streetaddress2']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-room">Room</label>
                    <input id="location-room" name="new_location[room]" type="text" value="<?php echo isset($post['location']['room']) ? $post['location']['room']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-city">City</label>
                    <input id="location-city" name="new_location[city]" type="text" value="<?php echo isset($post['location']['city']) ? $post['location']['city']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-state">State</label>
                    <input id="location-state" name="new_location[state]" type="text" value="<?php echo isset($post['location']['state']) ? $post['location']['state']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-zip"><abbr title="Zone Improvement Plan">ZIP</abbr> Code</label>
                    <input id="location-zip" name="new_location[zip]" type="text" value="<?php echo isset($post['location']['zip']) ? $post['location']['zip']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-map-url">Map <abbr title="Uniform Resource Locator">URL</abbr></label>
                    <input id="location-map-url" name="new_location[mapurl]" type="text" value="<?php echo isset($post['location']['mapurl']) ? $post['location']['mapurl']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-webpage">Web Page</label>
                    <input id="location-webpage" name="new_location[webpageurl]" type="text" value="<?php echo isset($post['location']['webpageurl']) ? $post['location']['webpageurl']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-hours">Hours</label>
                    <input id="location-hours" name="new_location[hours]" ype="text" value="<?php echo isset($post['location']['hours']) ? $post['location']['hours']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-directions">Directions</label>
                    <textarea id="location-directions" name="new_location[directions]"><?php echo isset($post['location']['directions']) ? $post['location']['directions']: ''; ?></textarea>
                </div>
                <div class="dcf-form-group">
                    <label for="location-additional-public-info">Additional Public Info</label>
                    <input id="location-additional-public-info" name="new_location[additionalpublicinfo]" type="text" value="<?php echo isset($post['location']['additionalpublicinfo']) ? $post['location']['additionalpublicinfo']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-type">Type</label>
                    <input id="location-type" name="new_location[type]" type="text" value="<?php echo isset($post['location']['type']) ?  $post['location']['type']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <label for="location-phone">Phone</label>
                    <input id="location-phone" name="new_location[phone]" type="text" value="<?php echo isset($post['location']['phone']) ? $post['location']['phone']: ''; ?>">
                </div>
                <div class="dcf-form-group">
                    <div class="dcf-input-checkbox">
                        <input id="location-save" name="location_save" type="checkbox" <?php if (isset($post['location_save']) && $post['location_save'] == 'on') { echo CHECKED_INPUT; } ?>>
                        <label for="location-save">Save this location for future events</label>
                    </div>
                </div>
            </fieldset>
            <div class="dcf-form-group">
                <label for="room">Room</label>
                <input id="room" name="room" type="text" value="<?php if (isset($post['room'])) { echo $post['room']; } ?>" />
            </div>
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
                        <fieldset class="dcf-d-flex dcf-ai-center dcf-col-gap-3 dcf-mb-0 dcf-ml-4 dcf-p-0 dcf-b-0 dcf-txt-sm" id="start-time-am-pm">
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
                        <fieldset class="dcf-d-flex dcf-ai-center dcf-col-gap-3 dcf-mb-0 dcf-ml-4 dcf-p-0 dcf-b-0 dcf-txt-sm" id="end-time-am-pm">
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
            <div class="dcf-form-group">
                <label for="directions">Directions</label>
                <textarea id="directions" name="directions"><?php if (isset($post['directions'])) { echo $post['directions']; } ?></textarea>
            </div>
            <div class="dcf-form-group">
                <label for="additional-public-info">Additional Public Info</label>
                <textarea id="additional-public-info" name="additional_public_info"><?php if (isset($post['additional_public_info'])) { echo $post['additional_public_info']; } ?></textarea>
            </div>
        </fieldset>
        <?php echo $savvy->render($context, 'EventFormImageUpload.tpl.php'); ?>
        <fieldset>
            <legend>Sharing</legend>
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
        </fieldset>
        <fieldset>
            <legend>Contact Info</legend>
            <div class="details">
                <div class="dcf-form-group">
                    <label for="contact-name">Name <small class="required-for-main-calendar dcf-required" style="display: none">Required</small></label>
                    <input id="contact-name" name="contact_name" type="text" value="<?php if (isset($post['contact_name'])) { echo $post['contact_name']; } ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="contact-phone">Phone</label>
                    <input id="contact-phone" name="contact_phone" type="text" value="<?php if (isset($post['contact_phone'])) { echo $post['contact_phone']; } ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="contact-email">Email</label>
                    <input id="contact-email" name="contact_email" type="text" value="<?php if (isset($post['contact_email'])) { echo $post['contact_email']; } ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="website">Event Website</label>
                    <input id="website" name="website" type="text" value="<?php echo $event->webpageurl ?>" />
                </div>
            </div>
        </fieldset>
        <button class="dcf-btn dcf-btn-primary" type="submit">Submit Event</button>
    </form>
</div>
<?php
$recurringType = !empty($post['recurring_type']) ? $post['recurring_type'] : 'none';
$page->addScriptDeclaration("

function isUrlValid(url) {
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}

require(['jquery'], function ($) {

    // DCF Date Picker
    WDN.initializePlugin('datepickers');

    $('#recurring').change(function () {
        if (this.checked) {
            setRecurringOptions($('#start-date'), $('#monthly-group'), '" . $recurringType . "');
        }
    });

    $('#start-date').change(function () {
        setRecurringOptions($(this), $('#monthly-group'), '" . $recurringType . "');
    });

    setRecurringOptions($('#start-date'), $('#monthly-group'));
    $('#recurring-type').val(\"". $recurringType ."\");

    $('#location').change(function (change) {
        if ($(this).val() == 'new') {
            $('#new-location-fields').show();
        } else {
            $('#new-location-fields').hide();
        }
    });

    $('#location').change();

    $('input[type=radio][name=send_to_main]').change(function() {
        if (this.value == 'on') {
            $('.required-for-main-calendar').show();
        }
        else if (this.value == 'off') {
            $('.required-for-main-calendar').hide();
        }
    });

    $('#create-event-form').submit(function (submit) {
        var errors = [];

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
            errors.push('<a href=\"#title\">Title</a>, <a href=\"#location\">location</a>, and <a href=\"#start-date\">start date</a> are required.');
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
                    errors.push('Your <a href=\"#end-date\">end date/time</a> must be on or after the <a href=\"#start-date\">start date/time</a>.');
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
                    errors.push('Recurring events require a <a href=\"#recurring-type\">recurring type</a> and <a href=\"#recurs-until-date\">date</a> that they recur until.');
                }

                if ($('#end-date').val() != '') {
                    var instanceStart = new Date($('#start-date').val());
                    var instanceEnd = new Date($('#end-date').val());
                    if (instanceStart && instanceEnd && instanceStart.getDate() != instanceEnd.getDate()) {
                        errors.push('A recurring event instance start and end date must be the same day. If you need multiple multi-day (ongoing) occurrences, you must define them as separate datetime instances.');
                    }
                }

                // check that the recurs until date is on or after the start date
                start.setHours(0);
                start.setMinutes(0);
                var until = new Date($('#recurs-until-date').val());

                if (start > until) {
                    notifier.mark_input_invalid($('#recurs-until-date'));
                    errors.push('The <a href=\"#recurs-until-date\">\"recurs until date\"</a> must be on or after the start date.');
                }
            }
        }

        // new locations must have a name
        if ($('#location').val() == 'new' && $('#location-name').val() == '') {
            notifier.mark_input_invalid($('#location-name'));
            errors.push('You must give your new location a <a href=\"#location-name\">name</a>.');
        }

        // Must select whether to consider for main calendar
        if ($('input[name=\"send_to_main\"]:checked').val() === undefined) {
            notifier.mark_input_invalid($('#send_to_main_on'));
            errors.push('<a href=\"#send_to_main\">Consider for main calendar</a> is required.');
        } else if ($('input[name=\"send_to_main\"]:checked').val() === 'on') {
            if ($('#description').val().trim() == '') {
                notifier.mark_input_invalid($('#description'));
                errors.push('<a href=\"#description\">Description</a> is required when event is considered for main calendar.');
            }
            if ($('#contact-name').val().trim() == '') {
                notifier.mark_input_invalid($('#contact-name'));
                errors.push('<a href=\"#contact-name\">Contact Name</a> is required when event is considered for main calendar.');
            }
            if ($('#cropped-image-data').val().trim() == '' && $('#imagedata').val().trim() == '') {
                notifier.mark_input_invalid($('#imagedata'));
                errors.push('<a href=\"#imagedata\">Image</a> is required when event is considered for main calendar.');
            }
        }

        var websiteURL = $('#website').val();
        if (websiteURL != '' && !isUrlValid(websiteURL)) {
            notifier.mark_input_invalid($('#website'));
            errors.push('<a href=\"#website\">Event Website</a> is not a valid URL.');
        }

        if (errors.length > 0) {
            submit.preventDefault();
            notifier.alert('Sorry! We couldn\'t create your event', '<ul><li>' + errors.join('</li><li>') + '</li></ul>');
        }
    });
});");
?>
