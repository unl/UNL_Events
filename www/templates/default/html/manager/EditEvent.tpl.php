<?php
    $calendar = $context->calendar;
    $event = $context->event;
    $post = $context->post;
    $event_type = $event->getFirstType();

    $total_pages = ceil(count($event->getDatetimes()) / 5);

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
    <form class="delete-datetime delete-form dcf-d-none" id="delete-datetime-<?php echo $datetime->id; ?>" method="POST" action="<?php echo $datetime->getDeleteURL($context->calendar) ?>" >
      <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
      <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
      <input type="hidden" name="event_datetime_id" value="<?php echo $datetime->id ?>" />
    </form>
    <?php if ($datetime->recurringtype != 'none') : ?>
        <?php foreach ($datetime->getRecurrences() as $recurring_date) : ?>
            <form class="delete-datetime-recurrence delete-form dcf-d-none" id="delete-datetime-<?php echo $datetime->id; ?>-recurrence-<?php echo $recurring_date->recurrence_id ?>" method="POST" action="<?php echo $datetime->getDeleteRecurrenceURL($context->calendar, $recurring_date->recurrence_id) ?>">
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
                <input id="canceled" name="canceled" type="checkbox" <?php if ($event->isCanceled()) { ?>checked=checked<?php } ?>value="1">
                <label for="canceled">Event Canceled</label>
            </div>
        </fieldset>
        <fieldset class="event-datetimes">
            <legend>Location, Date, and Time</legend>
            <a class="dcf-btn dcf-btn-primary" href="<?php echo $event->getAddDatetimeURL($context->calendar) ?>">Add Location, Date, and/or Time</a>
            <br><br>
            <div class="edt-header dcf-txt-sm">
                <div class="dates">
                    Dates
                </div>
                <div class="location">
                    Location
                </div>
            </div>

            <?php foreach($event->getDatetimes(5, ($context->page - 1)*5) as $datetime) : ?>
                <div class="edt-record dcf-txt-sm <?php if ($datetime->recurringtype != 'none') echo 'has-recurring' ?>">
                    <div class="dates">
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
                    </div>
                    <div class="location with-controls">
                        <?php echo $datetime->getLocation()->name; ?>
                    </div>
                    <div class="dcf-btn-group controls">
                        <a class="dcf-btn dcf-btn-primary small dcf-mb-2" href="<?php echo $datetime->getEditURL($context->calendar); ?>">Edit</a>
                        <button class="dcf-btn dcf-btn-primary small dcf-mb-2" form="delete-datetime-<?php echo $datetime->id; ?>" type="submit">Delete</button>
                    </div>
                </div>
                <?php if ($datetime->recurringtype != 'none') : ?>
                <div>
                    <?php foreach ($datetime->getRecurrences() as $recurring_date) : ?>
                        <div class="recurring-record">
                            <div class="edt-record recurring">
                                <div class="dates recurring">
                                    <?php echo date('n/d/y', strtotime($recurring_date->recurringdate)) . ' @ ' . date('g:ia', strtotime($datetime->starttime)); ?>
                                </div>
                                <div class="dcf-btn-group controls recurring">
                                    <a class="dcf-btn dcf-btn-primary small edit-recurring-edt" href="<?php echo $datetime->getEditRecurrenceURL($context->calendar, $recurring_date->recurrence_id); ?>">Edit</a>
                                    <button class="dcf-btn dcf-btn-primary small delete-datetime-recurrence" type="submit" form="delete-datetime-<?php echo $datetime->id ?>-recurrence-<?php echo $recurring_date->recurrence_id ?>">Delete</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($total_pages > 1): ?>
            <?php
                $page->addScriptDeclaration("WDN.loadCSS('https://unlcms.unl.edu/wdn/templates_4.1/css/modules/pagination.css');");
            ?>
            <div style="text-align: center;">
                <div style="display: inline-block;">
                    <ul id="pending-pagination" class="wdn_pagination" data-tab="pending" style="padding-left: 0;">
                        <?php if($context->page != 1): ?>
                            <li class="arrow prev"><a href="?page=<?php echo $context->page - 1 ?>" title="Go to the previous page">← prev</a></li>
                        <?php endif; ?>
                        <?php $before_ellipsis_shown = FALSE; $after_ellipsis_shown = FALSE; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == $context->page): ?>
                                    <li class="selected"><span><?php echo $i; ?></span></li>
                                <?php elseif ($i <= 3 || $i >= $total_pages - 2 || $i == $context->page - 1 ||
                                            $i == $context->page - 2 || $i == $context->page + 1 || $i == $context->page + 2): ?>
                                    <li><a href="?page=<?php echo $i ?>" title="Go to page <?php echo $i; ?>"><?php echo $i; ?></a></li>
                                <?php elseif ($i < $context->page && !$before_ellipsis_shown): ?>
                                    <li><span class="ellipsis">...</span></li>
                                    <?php $before_ellipsis_shown = TRUE; ?>
                                <?php elseif ($i > $context->page && !$after_ellipsis_shown): ?>
                                    <li><span class="ellipsis">...</span></li>
                                    <?php $after_ellipsis_shown = TRUE; ?>
                                <?php endif; ?>
                        <?php endfor; ?>
                        <?php if($context->page != $total_pages): ?>
                            <li class="arrow next"><a href="?page=<?php echo $context->page + 1 ?>" title="Go to the next page">next →</a></li>
                        <?php endif; ?>
                    </ul>
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
                        <input id="sharing-private" name="private_public" type="radio" value="private" <?php if (!$event->approvedforcirculation) echo 'checked="checked"' ?>>
                        <label for="sharing-private">Private</label>
                    </div>
                    <div class="dcf-input-radio">
                        <input id="sharing-public" name="private_public" type="radio" value="public" <?php if ($event->approvedforcirculation) echo 'checked="checked"' ?>>
                        <label for="sharing-public">Public</label>
                    </div>
                </fieldset>
                <fieldset class="dcf-col-100% dcf-col-75%-end@sm dcf-mb-0 dcf-p-0 dcf-b-0" id="send_to_main">
                    <legend class="dcf-pb-2">Consider for Main <abbr title="University of Nebraska–Lincoln"">UNL</abbr> Calendar <small class="dcf-required">Required</small></legend>
                    <?php if ($context->on_main_calendar): ?>
                        <img src="<?php echo $base_frontend_url ?>templates/default/html/images/checkmark-16.png" alt="">
                        (event has been sent to main UNL calendar for approval)
                    <?php else: ?>
                        <div class="dcf-input-checkbox">
                          <input type="checkbox" <?php if (isset($post['send_to_main'])) echo 'checked="checked"'; ?> name="send_to_main" id="send-to-main">
                          <label>Yes</label>
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
        if (!window.confirm('Are you sure you want to delete this location, date, and time?')) {
            submit.preventDefault();
        }
    });

    $('.delete-datetime-recurrence').submit(function (submit) {
        if (!window.confirm('Are you sure you want to delete instance of your recurring event? The rest of the recurrences will remain.')) {
            submit.preventDefault();
        }
    });

    $('.edit-recurring-edt').click(function (click) {
        if (!window.confirm('You are editing a single instance of a recurring location, date, and time.')) {
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
?>
