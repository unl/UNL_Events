<?php
    // Polyfill for is_countable
    if (! function_exists('is_countable')) {
        /**
         * @param mixed $value The value to check
         * @return bool
         */
        function is_countable($value): bool
        {
            return is_array($value) || (is_object($value) && $value instanceof Countable);
        }
    }

    const CHECKED_INPUT = 'checked="checked"';
    $calendar = $context->calendar;
    $event = $context->event;
    $post = $context->post;
    $event_type = $event->getFirstType();

    $datetimeCount = count($event->getDatetimes());
    $allowCanceledDatetime = $datetimeCount > 1;
    $total_pages = ceil($datetimeCount / 5);

    function ordinal($number) {
        $mod = $number % 100;
        if ($mod >= 11 && $mod <= 13) {
            return $number . 'th';
        } else if ($mod % 10 == 1) {
            return $number . 'st';
        } else if ($mod % 10 == 2) {
            return $number . 'nd';
        } else if ($mod % 10 == 3) {
            return $number . 'rd';
        } else {
            return $number . 'th';
        }
    }

?>
<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        'Edit Event' => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<br>

<?php foreach($event->getDatetimes() as $datetime) : ?>
    <?php
        if ($datetime->isCanceled()) {
            // Allow cancel toggle if any datetimes are canceled to allow them to be toggled off
            $allowCanceledDatetime = TRUE;
        }
    ?>
    <form class="dcf-form delete-datetime delete-form dcf-d-none" id="delete-datetime-<?php echo $datetime->id; ?>" method="POST" action="<?php echo $datetime->getDeleteURL($context->calendar) ?>" >
      <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
      <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
      <input type="hidden" name="event_datetime_id" value="<?php echo $datetime->id ?>" />
    </form>
    <?php if ($datetime->recurringtype != 'none') : ?>
        <?php foreach ($datetime->getRecurrences() as $recurring_date) : ?>
            <form class="dcf-form delete-datetime-recurrence delete-form dcf-d-none" id="delete-datetime-<?php echo $datetime->id; ?>-recurrence-<?php echo $recurring_date->recurrence_id ?>" method="POST" action="<?php echo $datetime->getDeleteRecurrenceURL($context->calendar, $recurring_date->recurrence_id) ?>">
                <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                <input type="hidden" name="event_datetime_id" value="<?php echo $datetime->id ?>" />
                <input type="hidden" name="recurrence_id" value="<?php echo $recurring_date->recurrence_id ?>" />
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endforeach; ?>

<form class="dcf-form" id="edit-event-form" action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <div style="margin-top: -2.5rem">
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
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?php echo $event->description; ?></textarea>
            </div>
            <div class="dcf-form-group">
                <label for="type">Type</label>
                <select id="type" name="type">
                <?php foreach ($context->getEventTypes() as $type) { ?>
                  <option <?php if ($event_type != NULL && $event_type->id == $type->id) echo 'selected="selected"'; ?> value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
                <?php } ?>
                </select>
            </div>
            <div class="dcf-input-checkbox">
                <input id="canceled" name="canceled" type="checkbox" value="1" <?php if ($event->isCanceled()) { echo 'checked="checked"'; } ?>>
                <label for="canceled">Event Canceled</label>
            </div>
        </fieldset>
        <fieldset>
            <legend>Location, Date &amp; Time</legend>
            <a class="dcf-btn dcf-btn-primary" href="<?php echo $event->getAddDatetimeURL($context->calendar) ?>">Add Location, Date, and/or Time</a>
            <table class="dcf-mt-6 dcf-table dcf-table-striped dcf-table-fixed dcf-w-100% dcf-txt-sm">
                <caption class="dcf-sr-only">Current Location, Date &amp; Times</caption>
                <thead class="edt-header">
                    <tr>
                        <th class="dates" scope="col">Dates</th>
                        <th class="location" scope="col">Location</th>
                        <th class="dcf-txt-right dcf-pr-0" scope="col">Actions</th>
                    </tr>
                </thead>
                <?php foreach($event->getDatetimes(5, ($context->page - 1)*5) as $datetime) : ?>
                <tr class="edt-record <?php if ($datetime->recurringtype != 'none') { echo 'has-recurring'; } ?>">
                    <td class="dcf-txt-middle dates">
                        <?php
                        {
                            if ($datetime->recurringtype == 'none') {
                                echo date('n/d/y @ g:ia', strtotime($datetime->starttime));
                            } else if ($datetime->recurringtype == 'daily' || $datetime->recurringtype == 'weekly' || $datetime->recurringtype == 'biweekly' || $datetime->recurringtype == 'annually') {
                                echo ucwords($datetime->recurringtype) . ' @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                            } else if ($datetime->recurringtype == 'monthly') {
                                if ($datetime->rectypemonth == 'lastday') {
                                    echo 'Last day of each month @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                    ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                    ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                    ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                                } else if ($datetime->rectypemonth == 'date') {
                                    echo ordinal(date('d', strtotime($datetime->starttime))) .
                                    ' of each month @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                    ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                    ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                                } else {
                                    echo ucwords($datetime->rectypemonth) . date(' l', strtotime($datetime->starttime)) . ' of every month' .
                                    ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                    ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                                }
                            }
                        }
                        ?>
                    </td>
                    <td class="dcf-txt-middle location with-controls">
                        <?php echo $datetime->getLocation()->name; ?>
                    </td>
                    <td class="dcf-pr-0 dcf-txt-middle controls">
                        <div class="dcf-d-flex dcf-ai-center dcf-jc-flex-end">
                            <a class="dcf-btn dcf-btn-primary" href="<?php echo $datetime->getEditURL($context->calendar); ?>">Edit</a>
                            <button class="dcf-btn dcf-btn-secondary dcf-ml-1" form="delete-datetime-<?php echo $datetime->id; ?>" type="submit">Delete</button>
                            <?php if ($allowCanceledDatetime === TRUE && $datetime->recurringtype === 'none') : ?>
                                  <div class="dcf-input-checkbox dcf-mr-4 dcf-mb-0 dcf-ml-3 dcf-txt-sm">
                                      <input class="datetime-cancel-toggle" id="datetime-canceled-<?php echo $datetime->id; ?>" type="checkbox" value="1" <?php if ($datetime->isCanceled()) { echo 'checked="checked"'; } ?> data-url="<?php echo $datetime->getEditURL($context->calendar); ?>">
                                      <label for="datetime-canceled-<?php echo $datetime->id; ?>">Canceled</label>
                                  </div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php if ($datetime->recurringtype != 'none') : ?>
                    <?php if (is_countable($datetime->getRecurrences()) && count($datetime->getRecurrences()) > 0): ?>
                        <?php foreach ($datetime->getRecurrences() as $recurring_date) : ?>
                            <tr class="edt-record">
                                <td class="dcf-pl-7 dcf-txt-middle dates recurring" colspan="2">
                                    <?php echo date('n/d/y', strtotime($recurring_date->recurringdate)) . ' @ ' . date('g:ia', strtotime($datetime->starttime)); ?>
                                </td>
                                <td class="dcf-pr-0 dcf-txt-middle controls recurring">
                                    <div class="dcf-d-flex dcf-ai-center dcf-jc-flex-end">
                                        <a class="dcf-btn dcf-btn-primary edit-recurring-edt" href="<?php echo $datetime->getEditRecurrenceURL($context->calendar, $recurring_date->recurrence_id); ?>">Edit</a>
                                        <button class="dcf-btn dcf-btn-secondary dcf-ml-1 delete-datetime-recurrence" type="submit" form="delete-datetime-<?php echo $datetime->id ?>-recurrence-<?php echo $recurring_date->recurrence_id ?>">Delete</button>
                                        <div class="dcf-input-checkbox dcf-mr-4 dcf-mb-0 dcf-ml-3 dcf-txt-sm">
                                            <input class="recurrence-instance-cancel-toggle" id="recurrence-instance-canceled-<?php echo $recurring_date->recurrence_id; ?>" name="canceled" type="checkbox" value="1" <?php if ($recurring_date->isCanceled()) { echo 'checked="checked"'; } ?> data-url="<?php echo $datetime->getEditRecurrenceURL($context->calendar, $recurring_date->recurrence_id); ?>">
                                            <label for="recurrence-instance-canceled-<?php echo $recurring_date->recurrence_id; ?>">Canceled</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">Notice: The above recurrence definition does not have any recurrences and may be deleted.</td></tr>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            </table>

            <?php if ($total_pages > 1): ?>
            <?php $page->addScriptDeclaration("WDN.initializePlugin('pagination');"); ?>
            <div style="text-align: center;">
                <div style="display: inline-block;">
                    <nav class="dcf-pagination">
                        <ol class="dcf-list-bare dcf-list-inline">
                        <?php if($context->page != 1): ?>
                            <li><a class="dcf-pagination-prev" href="?page=<?php echo $context->page - 1 ?>">Prev</a></li>
                        <?php endif; ?>
                        <?php $before_ellipsis_shown = FALSE; $after_ellipsis_shown = FALSE; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == $context->page): ?>
                                    <li><span class="dcf-pagination-selected"><?php echo $i; ?></span></li>
                                <?php elseif ($i <= 3 || $i >= $total_pages - 2 || $i == $context->page - 1 ||
                                            $i == $context->page - 2 || $i == $context->page + 1 || $i == $context->page + 2): ?>
                                    <li><a href="?page=<?php echo $i ?>"><?php echo $i; ?></a></li>
                                <?php elseif ($i < $context->page && !$before_ellipsis_shown): ?>
                                    <li><span class="dcf-pagination-ellipsis">...</span></li>
                                    <?php $before_ellipsis_shown = TRUE; ?>
                                <?php elseif ($i > $context->page && !$after_ellipsis_shown): ?>
                                    <li><span class="dcf-pagination-ellipsis">...</span></li>
                                    <?php $after_ellipsis_shown = TRUE; ?>
                                <?php endif; ?>
                        <?php endfor; ?>
                        <?php if($context->page != $total_pages): ?>
                            <li><a class="dcf-pagination-next" href="?page=<?php echo $context->page + 1 ?>">Next</a></li>
                        <?php endif; ?>
                        </ol>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
        </fieldset>
        <?php echo $savvy->render($context, 'EventFormImageUpload.tpl.php'); ?>

        <fieldset>
            <legend>Sharing</legend>
            <div class="details dcf-grid dcf-col-gap-vw">
                <fieldset class="dcf-col-100% dcf-col-25%-start@sm dcf-p-0 dcf-b-0">
                    <legend class="dcf-pb-2">Privacy</legend>
                    <div class="dcf-input-radio">
                        <input id="sharing-private" name="private_public" type="radio" value="private" <?php if (!$event->approvedforcirculation) { echo CHECKED_INPUT; } ?>>
                        <label for="sharing-private">Private</label>
                    </div>
                    <div class="dcf-input-radio">
                        <input id="sharing-public" name="private_public" type="radio" value="public" <?php if ($event->approvedforcirculation) { echo CHECKED_INPUT; } ?>>
                        <label for="sharing-public">Public</label>
                    </div>
                </fieldset>
                <fieldset class="dcf-col-100% dcf-col-75%-end@sm dcf-mb-0 dcf-p-0 dcf-b-0" id="send_to_main">
                    <legend class="dcf-pb-2">Consider for Main <abbr title="University of Nebraska–Lincoln"">UNL</abbr> Calendar</legend>
                    <?php if ($context->on_main_calendar): ?>
                        <img src="<?php echo $base_frontend_url ?>templates/default/html/images/checkmark-16.png" alt="">
                        (event has been sent to main UNL calendar for approval)
                    <?php else: ?>
                        <div class="dcf-input-checkbox">
                          <input id="send-to-main" name="send_to_main" type="checkbox" <?php if (isset($post['send_to_main'])) { echo CHECKED_INPUT; } ?>>
                          <label for="send-to-main">Yes</label>
                        </div>
                    <?php endif; ?>
                </fieldset>
            </div>
        </fieldset>
        <fieldset>
            <legend>Contact Info</legend>
            <div class="details">
                <div class="dcf-form-group">
                    <label for="contact-name">Name</label>
                    <input id="contact-name" name="contact_name" type="text" value="<?php echo $event->listingcontactname; ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="contact-phone">Phone</label>
                    <input id="contact-phone" name="contact_phone" type="text" value="<?php echo $event->listingcontactphone; ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="contact-email">Email</label>
                    <input id="contact-email" name="contact_email" type="text" value="<?php echo $event->listingcontactemail; ?>" />
                </div>
                <div class="dcf-form-group">
                    <label for="website">Event Website</label>
                    <input id="website" name="website" type="text" value="<?php echo $event->webpageurl; ?>" />
                </div>
            </div>
        </fieldset>
        <button class="dcf-btn dcf-btn-primary" type="submit">Save Event</button>
    </div>
</form>

<?php
$page->addScriptDeclaration("
require(['jquery'], function($) {
    $('.delete-datetime').submit(function (submit) {
        if (!window.confirm('Are you sure you want to delete this location, date and time?')) {
            submit.preventDefault();
        }
    });

    $('.delete-datetime-recurrence').submit(function (submit) {
        if (!window.confirm('Are you sure you want to delete instance of your recurring event? The rest of the recurrences will remain.')) {
            submit.preventDefault();
        }
    });

    $('.edit-recurring-edt').click(function (click) {
        if (!window.confirm('You are editing a single instance of a recurring location, date and time.')) {
            click.preventDefault();
        }
    });

    $('#edit-event-form').submit(function (submit) {
        // validate required fields
        if ($('#title').val() == '') {
            notifier.mark_input_invalid($('#title'));
            notifier.alert('Sorry! We couldn\'t edit your event', '<a href=\"#title\">Title</a> is required.');
            submit.preventDefault();
        }
    });
});");

$tokenNameKey = $controller->getCSRFHelper()->getTokenNameKey();
$tokenNameValue = $controller->getCSRFHelper()->getTokenName();
$tokenValueKey = $controller->getCSRFHelper()->getTokenValueKey();
$tokenValueValue = $controller->getCSRFHelper()->getTokenValue();
$tokenString = $tokenNameKey . '=' . $tokenNameValue . '&' . $tokenValueKey . '=' . $tokenValueValue;
$page->addScriptDeclaration("
    var datetimeCancelToggles = document.getElementsByClassName('datetime-cancel-toggle');
    var i;
    for (i = 0; i < datetimeCancelToggles.length; i++) {
        datetimeCancelToggles[i].addEventListener('change', function() {
            var token = '" . $tokenString . "';
            var xhr = new XMLHttpRequest();
            xhr.responseType = 'json';
            xhr.open('POST', this.dataset.url);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                displayCancelToggleMessage(xhr.response);
            };
            xhr.send('toggle-cancel=1&canceled=' + this.checked + '&' + token);
        });
    }
    var instanceCancelToggles = document.getElementsByClassName('recurrence-instance-cancel-toggle');
    for (i = 0; i < instanceCancelToggles.length; i++) {
        instanceCancelToggles[i].addEventListener('change', function() {
            var token = '" . $tokenString . "';
            var xhr = new XMLHttpRequest();
            xhr.responseType = 'json';
            xhr.open('POST', this.dataset.url);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                displayCancelToggleMessage(xhr.response);
            };
            xhr.send('toggle-cancel=1&canceled=' + this.checked + '&' + token);
        });
    }

    function displayCancelToggleMessage(response) {
        if (response.success) {
            var cancelAction = response.canceled ? 'canceled' : 'uncanceled';
            notifier.success('Event Instance Updated', 'Event instance has been successfully ' + cancelAction + '.');
        } else {
            notifier.alert('Event Instance Update Error', 'Update of cancel state has failed.');
        }
    }
");
?>
