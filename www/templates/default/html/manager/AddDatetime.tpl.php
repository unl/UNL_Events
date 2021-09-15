<?php
    const CHECKED_INPUT = 'checked="checked"';

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
        $last_crumb = $datetime->id == NULL ? 'Add a Location, Date & Time' : 'Edit Location, Date & Time';
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
<h1><?php echo $last_crumb ?></h1>
<form class="dcf-form" id="add-datetime-form" action="" method="POST">
  <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
  <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <fieldset class="dcf-mt-6">
        <legend>Location, Date &amp; Time</legend>
        <div class="dcf-form-group">
            <label for="location">Location <small class="dcf-required">Required</small></label>
            <select class="dcf-input-select" id="location" name="location" style="width: 100%;">
                  <?php if ($datetime->id != NULL): ?>
                  <optgroup label="Current location">
                      <option selected="selected" value="<?php echo $datetime->location_id ?>"><?php echo $datetime->getLocation()->name; ?></option>
                  <?php endif; ?>
                  <optgroup label="Your saved locations">
                      <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getUserLocations() as $location): ?>
                          <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                      <?php endforeach; ?>
                      <option value="new">-- New Location --</option>
                  </optgroup>
                  <optgroup label="UNL Campus locations">
                      <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_MAIN) as $location): ?>
                          <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                      <?php endforeach; ?>
                  </optgroup>
                  <optgroup label="Extension locations">
                      <?php foreach (\UNL\UCBCN\Manager\LocationUtility::getStandardLocations(\UNL\UCBCN\Location::DISPLAY_ORDER_EXTENSION) as $location): ?>
                          <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                      <?php endforeach; ?>
                  </optgroup>
            </select>
        </div>

        <fieldset class="dcf-mt-6" id="new-location-fields" style="display: none;">
            <legend>New Location</legend>
            <div class="dcf-form-group">
                <label for="location-name">Name <small class="dcf-required">Required</small></label>
                <input id="location-name" name="new_location[name]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-address-1">Address</label>
                <input id="location-address-1" name="new_location[streetaddress1]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-address-2">Address 2</label>
                <input id="location-address-2" name="new_location[streetaddress2]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-room">Room</label>
                <input id="location-room" name="new_location[room]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-city">City</label>
                <input id="location-city" name="new_location[city]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-state">State</label>
                <input id="location-state" name="new_location[state]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-zip"><abbr title="Zone Improvement Plan">ZIP</abbr> Code</label>
                <input id="location-zip" name="new_location[zip]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-map-url">Map <abbr title="Uniform Resource Locator">URL</abbr></label>
                <input id="location-map-url" name="new_location[mapurl]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-webpage">Web Page</label>
                <input id="location-webpage" name="new_location[webpageurl]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-hours">Hours</label>
                <input id="location-hours" name="new_location[hours]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-directions">Directions</label>
                <textarea id="location-directions" name="new_location[directions]"></textarea>
            </div>
            <div class="dcf-form-group">
                <label for="location-additional-public-info">Additional Public Info</label>
                <input id="location-additional-public-info" name="new_location[additionalpublicinfo]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-type">Type</label>
                <input id="location-type" name="new_location[type]" type="text">
            </div>
            <div class="dcf-form-group">
                <label for="location-phone">Phone</label>
                <input id="location-phone" name="new_location[phone]" type="text">
            </div>
            <div class="dcf-form-group">
                <div class="dcf-input-checkbox">
                    <input id="location-save" name="location_save" type="checkbox">
                    <label for="location-save">Save this location for future events</label>
                </div>
            </div>
        </fieldset>

        <div class="dcf-form-group">
            <label for="room">Room</label>
            <input id="room" name="room" type="text" value="<?php echo $datetime->room; ?>" />
        </div>
        <div class="dcf-form-group">
            <label class="dcf-label" for="timezone">Time Zone <small class="dcf-required">Required</small></label>
            <select id="timezone"" name="timezone" aria-label="Timezone">
              <?php
              $timezone = $calendar->defaulttimezone;
              if (!empty($datetime->timezone)) {
                  $timezone = $datetime->timezone;
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
                    <input id="start-date" name="start_date" type="text" value="<?php echo $start_date; ?>" aria-label="Start Date in the format of mm/dd/yyyy" autocomplete="off" />
                </div>
                <div class="dcf-form-group dcf-d-flex dcf-ai-center dcf-flex-grow-1">
                    <span class="dcf-pr-2">@</span>
                    <select class="dcf-flex-grow-1" id="start-time-hour" name="start_time_hour" aria-label="Start Time Hour">
                        <option value="">Hour</option>
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                          <option <?php if ($i == $start_hour) echo 'selected="selected"'; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                        <?php } ?>
                    </select>
                    <span class="dcf-pr-1 dcf-pl-1">:</span>
                    <select class="dcf-flex-grow-1" id="start-time-minute" name="start_time_minute" aria-label="Start Time Minute">
                        <option value="">Minute</option>
                        <?php for ($i = 0; $i < 60; $i+=5): ?>
                          <option <?php if ($i == $start_minute) echo 'selected="selected"'; ?> value="<?php echo $i; ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                        <?php endfor; ?>
                    </select>
                    <fieldset class="dcf-d-flex dcf-ai-center dcf-col-gap-3 dcf-mb-0 dcf-ml-4 dcf-p-0 dcf-b-0 dcf-txt-sm" id="start-time-am-pm">
                        <legend class="dcf-sr-only">AM/PM</legend>
                        <div class="dcf-input-radio dcf-mb-0">
                            <input id="start-time-am-pm-am" name="start_time_am_pm" type="radio" value="am" <?php if ($start_am_pm == 'am') { echo CHECKED_INPUT; } ?>>
                            <label class="dcf-mt-0" for="start-time-am-pm-am">AM</label>
                        </div>
                        <div class="dcf-input-radio dcf-mb-0">
                            <input id="start-time-am-pm-pm" name="start_time_am_pm" type="radio" value="pm" <?php if ($start_am_pm == 'pm') { echo CHECKED_INPUT; } ?>>
                            <label class="dcf-mt-0" for="start-time-am-pm-pm">PM</label>
                        </div>
                    </fieldset>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>End Date &amp; Time <small class="dcf-pl-1 dcf-txt-xs dcf-italic">Optional</small></legend>
            <div class="dcf-d-flex dcf-flex-wrap dcf-ai-center dcf-col-gap-4">
                <div class="dcf-form-group dcf-datepicker dcf-flex-grow-1">
                    <input id="end-date" name="end_date" type="text" value="<?php echo $end_date; ?>" aria-label="End Date in the format of mm/dd/yyyy" autocomplete="off" />
                </div>
                <div class="dcf-form-group dcf-d-flex dcf-ai-center dcf-flex-grow-1">
                    <span class="dcf-pr-2">@</span>
                    <select class="dcf-flex-grow-1"  id="end-time-hour" name="end_time_hour" aria-label="End time hour">
                        <option value="">Hour</option>
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                          <option <?php if ($i == $end_hour) echo 'selected="selected"'; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                        <?php } ?>
                    </select>
                    <span class="dcf-pr-1 dcf-pl-1">:</span>
                    <select class="dcf-flex-grow-1" id="end-time-minute" name="end_time_minute" aria-label="End Time Minute">
                        <option value="">Minute</option>
                        <?php for ($i = 0; $i < 60; $i+=5): ?>
                            <option <?php if ($i == $end_minute) echo 'selected="selected"'; ?> value="<?php echo $i; ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                        <?php endfor; ?>
                    </select>
                    <fieldset class="dcf-d-flex dcf-ai-center dcf-col-gap-3 dcf-mb-0 dcf-ml-4 dcf-p-0 dcf-b-0 dcf-txt-sm" id="end-time-am-pm">
                        <legend class="dcf-sr-only">AM/PM</legend>
                        <div class="dcf-input-radio dcf-mb-0">
                            <input id="end-time-am-pm-am" name="end_time_am_pm" type="radio" value="am" <?php if ($end_am_pm == 'am') { echo CHECKED_INPUT; } ?>>
                            <label class="dcf-mt-0" for="end-time-am-pm-am">AM</label>
                        </div>
                        <div class="dcf-input-radio dcf-mb-0">
                            <input id="end-time-am-pm-pm" name="end_time_am_pm" type="radio" value="pm" <?php if ($end_am_pm == 'pm') { echo CHECKED_INPUT; } ?>>
                            <label class="dcf-mt-0" for="end-time-am-pm-pm">PM</label>
                        </div>
                    </fieldset>
                </div>
            </div>
        </fieldset>

        <?php if ($context->recurrence_id == NULL) : ?>
            <div class="section-container">
                <div class="dcf-input-checkbox">
                    <input id="recurring" name="recurring" type="checkbox" <?php if ($datetime->recurringtype != 'none' && $datetime->recurringtype != NULL) { echo CHECKED_INPUT; } ?>>
                    <?php $recurringType = (empty($datetime->recurringtype) || strtolower($datetime->recurringtype) == 'none') ? '' : $datetime->recurringtype; ?>
                    <label for="recurring">This is a recurring <?php echo $recurringType; ?> event</label>
                </div>

                <fieldset class="recurring-container date-time-select">
                    <legend class="dcf-sr-only">Recurring Event Details</legend>
                    <div class="dcf-d-flex dcf-flex-wrap dcf-ai-center dcf-col-gap-4">
                      <div class="dcf-form-group">
                          <label class="dcf-label dcf-d-inline-block" for="recurring-type">This event recurs </label>
                          <select class="dcf-input-select" id="recurring-type" name="recurring_type">
                            <option value="daily" <?php if($datetime->recurringtype == "daily") { echo 'selected="selected"'; } ?>>Daily</option>
                            <option value="weekly" <?php if($datetime->recurringtype == "weekly") { echo 'selected="selected"'; } ?>>Weekly</option>
                            <option value="biweekly" <?php if($datetime->recurringtype == "biweekly") { echo 'selected="selected"'; } ?>>Biweekly</option>
                            <optgroup label="Monthly" id="monthly-group">
                            </optgroup>
                            <option value="annually" <?php if($datetime->recurringtype == "biweekly") { echo 'selected="selected"'; } ?>>Yearly</option>
                          </select>
                      </div>
                      <div class="dcf-form-group dcf-datepicker">
                        <label for="recurs-until-date">until </label>
                        <input id="recurs-until-date" name="recurs_until_date" type="text" value="<?php if ($datetime->recurringtype != 'none' && $datetime->recurringtype != NULL) { echo $recurs_until_date; } ?>" aria-label="Until date in the format of mm/dd/yyyy" autocomplete="off" />
                    </div>
                </fieldset>
            </div>
        <?php endif; ?>

        <div class="dcf-form-group">
            <label for="directions">Directions</label>
            <textarea id="directions" name="directions"><?php echo $datetime->directions; ?></textarea>
        </div>
        <div class="dcf-form-group">
            <label for="additional-public-info">Additional Public Info</label>
            <textarea id="additional-public-info" name="additional_public_info"><?php echo $datetime->additionalpublicinfo; ?></textarea>
        </div>
    </fieldset>
    <button class="dcf-btn dcf-btn-primary" type="submit">Submit</button>
</form>
<br>

<?php
$recurringCode = '';
$rectypemonth = '';
if ($datetime->recurringtype != 'none' && $datetime->recurringtype != NULL) {
  if ($datetime->recurringtype == 'monthly') {
      $rectypemonth = $datetime->rectypemonth;
  }
  $recurringCode = "setRecurringOptions($('#start-date'), $('#monthly-group'), '" . $rectypemonth. "');";
}
$page->addScriptDeclaration("
require(['jquery'], function ($) {

    // DCF Date Picker
    WDN.initializePlugin('datepickers');

    $('#location').change(function(change) {
        if ($('#location').val() == 'new') {
            $('#new-location-fields').show();
        } else {
            $('#new-location-fields').hide();
        }
    });

    $('#location').change();

    $('#recurring').change(function () {
        if (this.checked) {
            setRecurringOptions($('#start-date'), $('#monthly-group'), '" . $recurringType . "');
        }
    });

    $('#start-date').change(function (change) {
        setRecurringOptions($(this), $('#monthly-group'), '" . $rectypemonth. "');
    });

    " . $recurringCode . "

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
            errors.push('<a href=\"#location\">Location</a> and <a href=\"#start-date\">start date</a> are required.');
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

        if (errors.length > 0) {
            submit.preventDefault();
            notifier.alert('Sorry! We couldn\'t create your event', '<ul><li>' + errors.join('</li><li>') + '</li></ul>');
        }
    });
});");

?>
