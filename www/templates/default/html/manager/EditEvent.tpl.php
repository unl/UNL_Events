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
    $event_targets_audience = $event->getAudiences();

    $datetimeCount = count($event->getDatetimes());
    $allowCanceledDatetime = $datetimeCount > 1;
    $total_pages = ceil($datetimeCount / 5);

    function ordinal($number)
    {
        $mod = $number % 100;
        if ($mod >= 11 && $mod <= 13) {
            return $number . 'th';
        } elseif ($mod % 10 == 1) {
            return $number . 'st';
        } elseif ($mod % 10 == 2) {
            return $number . 'nd';
        } elseif ($mod % 10 == 3) {
            return $number . 'rd';
        } else {
            return $number . 'th';
        }
    }

?>
<?php
    $last_crumb = 'Edit "' . $event->title . '"';
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        $last_crumb => null
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1><?php echo $last_crumb; ?></h1>

<?php foreach($event->getDatetimes() as $datetime) : ?>
    <?php
        if ($datetime->isCanceled()) {
            // Allow cancel toggle if any datetimes are canceled to allow them to be toggled off
            $allowCanceledDatetime = true;
        }
    ?>
    <form
        class="dcf-form delete-datetime delete-form dcf-d-none"
        id="delete-datetime-<?php echo $datetime->id; ?>"
        method="POST"
        action="<?php echo $datetime->getDeleteURL($context->calendar) ?>"
    >
        <input
            type="hidden"
            name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>"
            value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>"
        />
        <input
            type="hidden"
            name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>"
            value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>"
        />
        <input type="hidden" name="event_datetime_id" value="<?php echo $datetime->id ?>" />
    </form>
    <?php if ($datetime->recurringtype != 'none') : ?>
        <?php foreach ($datetime->getRecurrences() as $recurring_date) : ?>
            <form
                class="dcf-form delete-datetime-recurrence delete-form dcf-d-none"
                id="delete-datetime-<?php
                        echo $datetime->id;
                    ?>-recurrence-<?php
                        echo $recurring_date->recurrence_id
                    ?>"
                method="POST"
                action="<?php
                    echo $datetime->getDeleteRecurrenceURL($context->calendar, $recurring_date->recurrence_id)
                ?>"
            >
                <input
                    type="hidden"
                    name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>"
                    value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>"
                />
                <input
                    type="hidden"
                    name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>"
                    value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>"
                />
                <input type="hidden" name="event_datetime_id" value="<?php echo $datetime->id ?>" />
                <input type="hidden" name="recurrence_id" value="<?php echo $recurring_date->recurrence_id ?>" />
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endforeach; ?>

<form class="dcf-form" id="edit-event-form" action="" method="POST" enctype="multipart/form-data">
    <input
        type="hidden"
        name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>"
        value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>"
    />
    <input
        type="hidden"
        name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>"
        value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>"
    />
    <h2>Event Details</h2>
    <section  class="dcf-mb-8 dcf-ml-5">
        <div class="dcf-form-group">
            <label for="title">Title <small class="dcf-required">Required</small></label>
            <input id="title" name="title" type="text" class="dcf-w-100%" value="<?php echo $event->title; ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="subtitle">Subtitle</label>
            <input
                id="subtitle"
                name="subtitle"
                type="text"
                class="dcf-w-100%"
                value="<?php echo $event->subtitle; ?>"
            >
        </div>
        <div class="dcf-form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?php echo $event->description; ?></textarea>
        </div>
        <div class="dcf-form-group">
            <label for="website">Website</label>
            <input
                id="website"
                name="website"
                type="text"
                class="dcf-w-100%"
                value="<?php echo $event->webpageurl; ?>"
            />
        </div>
        <div class="dcf-form-group">
            <label for="type">Type <small class="dcf-required">Required</small></label>
            <select id="type" name="type">
                <option
                    <?php if (empty($context->getEventTypes())) { echo 'selected="selected"'; } ?>
                    disabled="disabled"
                    value=""
                >
                    Please Select One
                </option>
                <?php foreach ($context->getEventTypes() as $type) { ?>
                    <option
                        <?php
                            if ($event_type != null && $event_type->id == $type->id) {
                                echo 'selected="selected"';
                            }
                        ?>
                        value="<?php echo $type->id; ?>"
                    >
                        <?php echo $type->name; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <fieldset>
            <legend>Target Audience</legend>
            <div class="target-audience-grid">
                <?php foreach ($context->getAudiences() as $audience): ?>

                    <?php
                        // Find whether the audience is associated with the event
                        // If so it will be used to check the input
                        $audience_match = false;
                        foreach ($event_targets_audience as $target_audience) {
                            if ($audience->id === $target_audience->audience_id) {
                                $audience_match = true;
                                break;
                            }
                        }
                    ?>

                    <?php $target_audience_id = 'target-audience-' . $audience->id; ?>
                    <div class="dcf-input-checkbox">
                        <input
                            id="<?php echo $target_audience_id; ?>"
                            name="<?php echo $target_audience_id; ?>"
                            type="checkbox"
                            value="<?php echo $audience->id; ?>"
                            <?php if ($audience_match) { echo CHECKED_INPUT; } ?>
                        >
                        <label for="<?php echo $target_audience_id; ?>">
                            <?php echo $audience->name; ?>
                        </label>
                    </div>
            <?php endforeach; ?>
            </div>
        </fieldset>
        <div class="dcf-input-checkbox">
            <input
                id="canceled"
                name="canceled"
                type="checkbox"
                value="1"
                <?php if ($event->isCanceled()) { echo CHECKED_INPUT; } ?>
            />
            <label for="canceled">Event Canceled</label>
        </div>
        <hr>
    </section>

    <h2>Event Instances</h2>
    <section  class="dcf-mb-8 dcf-ml-5">
        <a
            class="dcf-btn dcf-btn-primary"
            href="<?php echo $event->getAddDatetimeURL($context->calendar) ?>"
        >
            Add New Instance
        </a>
        <table class="dcf-mt-6 dcf-table dcf-table-striped dcf-table-fixed dcf-w-100% dcf-txt-sm">
            <caption class="dcf-sr-only">Current Event Instances</caption>
            <thead class="edt-header">
                <tr>
                    <th class="dates" scope="col">Dates</th>
                    <th class="location" scope="col">Physical Location</th>
                    <th class="v_location" scope="col">Virtual Location</th>
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
                            } elseif ($datetime->recurringtype == 'daily' ||
                                        $datetime->recurringtype == 'weekly' ||
                                        $datetime->recurringtype == 'biweekly' ||
                                        $datetime->recurringtype == 'annually') {

                                echo ucwords($datetime->recurringtype)
                                . ' @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                            } elseif ($datetime->recurringtype == 'monthly') {
                                if ($datetime->rectypemonth == 'lastday') {
                                    echo 'Last day of each month @ ' .
                                    date('g:ia', strtotime($datetime->starttime)) .
                                    ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                    ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                    ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                                } elseif ($datetime->rectypemonth == 'date') {
                                    echo ordinal(date('d', strtotime($datetime->starttime))) .
                                    ' of each month @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                    ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                    ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                                } else {
                                    echo ucwords($datetime->rectypemonth) .
                                    date(' l', strtotime($datetime->starttime)) . ' of every month' .
                                    ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                    ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                                }
                            }
                        }
                        ?>
                    </td>
                    <?php $location = $datetime->getLocation(); ?>
                    <?php if (isset($location) && !empty($location)): ?>
                        <td
                            class="dcf-txt-middle location with-controls"
                            data-id="<?php echo $location->id; ?>"
                            data-microdata="<?php echo json_encode($location->microdataCheck()); ?>"
                        >
                            <div class="dcf-popup dcf-w-100%" data-hover="true" data-point="true">
                                <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-w-100%">
                                    <?php echo $location->name; ?>
                                </button>
                                <div
                                    class="dcf-popup-content unl-cream unl-bg-blue dcf-p-3 dcf-rounded"
                                    style="width: 100%; min-width: 25ch;"
                                >
                                    <dl>
                                        <?php if(isset($location->name) && !empty($location->name)): ?>
                                            <dt>Name</dt>
                                            <dd><?php echo $location->name; ?></dd>
                                        <?php endif; ?>

                                        <?php
                                            if(isset($location->streetaddress1) && !empty($location->streetaddress1)):
                                        ?>
                                            <dt>Street Address 1</dt>
                                            <dd><?php echo $location->streetaddress1; ?></dd>
                                        <?php endif; ?>

                                        <?php
                                            if(isset($location->streetaddress2) && !empty($location->streetaddress2)):
                                        ?>
                                            <dt>Street Address 2</dt>
                                            <dd><?php echo $location->streetaddress2; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($location->city) && !empty($location->city)): ?>
                                            <dt>City</dt>
                                            <dd><?php echo $location->city; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($location->state) && !empty($location->state)): ?>
                                            <dt>State</dt>
                                            <dd><?php echo $location->state; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($location->zip) && !empty($location->zip)): ?>
                                            <dt>Zip</dt>
                                            <dd><?php echo $location->zip; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($location->room) && !empty($location->room)): ?>
                                            <dt>Room</dt>
                                            <dd><?php echo $location->room; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($datetime->room) && !empty($datetime->room)): ?>
                                            <dt>Room</dt>
                                            <dd><?php echo $datetime->room; ?></dd>
                                        <?php elseif(isset($location->room) && !empty($location->room)): ?>
                                            <dt>Room</dt>
                                            <dd><?php echo $location->room; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($datetime->directions) && !empty($datetime->directions)): ?>
                                            <dt>Directions</dt>
                                            <dd><?php echo $datetime->directions; ?></dd>
                                        <?php elseif(isset($location->directions) && !empty($location->directions)): ?>
                                            <dt>Directions</dt>
                                            <dd><?php echo $location->directions; ?></dd>
                                        <?php endif; ?>

                                        <?php
                                            if(isset($datetime->location_additionalpublicinfo) &&
                                                !empty($datetime->location_additionalpublicinfo)
                                            ):
                                        ?>
                                            <dt>Additional Public Info</dt>
                                            <dd><?php echo $datetime->location_additionalpublicinfo; ?></dd>
                                        <?php
                                            elseif(isset($location->additionalpublicinfo) &&
                                                !empty($location->additionalpublicinfo)
                                            ):
                                        ?>
                                            <dt>Additional Public Info</dt>
                                            <dd><?php echo $location->additionalpublicinfo; ?></dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>
                        </td>
                    <?php else: ?>
                        <td class="dcf-txt-middle location with-controls" data-id="" data-microdata="false">
                            <?php echo "None"; ?>
                        </td>
                    <?php endif;?>

                    <?php $getWebcast = $datetime->getWebcast(); ?>
                    <?php if (isset($getWebcast) && !empty($getWebcast)): ?>
                        <td
                            class="dcf-txt-middle v_location with-controls"
                            data-id="<?php echo $getWebcast->id; ?>"
                            data-microdata="<?php echo json_encode($getWebcast->microdataCheck()); ?>"
                        >
                            <div class="dcf-popup dcf-w-100%" data-hover="true" data-point="true">
                                <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-w-100%">
                                    <?php echo $getWebcast->title; ?>
                                </button>
                                <div
                                    class="dcf-popup-content unl-cream unl-bg-blue dcf-p-3 dcf-rounded"
                                    style="min-width: 25ch;"
                                >
                                    <dl>
                                        <?php if(isset($getWebcast->title) && !empty($getWebcast->title)): ?>
                                            <dt>Name</dt>
                                            <dd><?php echo $getWebcast->title; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($getWebcast->url) && !empty($getWebcast->url)): ?>
                                            <dt>URL</dt>
                                            <dd><?php echo $getWebcast->url; ?></dd>
                                        <?php endif; ?>

                                        <?php
                                            if(isset($datetime->webcast_additionalpublicinfo) &&
                                                !empty($datetime->webcast_additionalpublicinfo)
                                            ):
                                        ?>
                                            <dt>Additional Public Info</dt>
                                            <dd><?php echo $datetime->webcast_additionalpublicinfo; ?></dd>
                                        <?php
                                            elseif(isset($getWebcast->additionalinfo) &&
                                                !empty($getWebcast->additionalinfo)
                                            ):
                                        ?>
                                            <dt>Additional Public Info</dt>
                                            <dd><?php echo $getWebcast->additionalinfo; ?></dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>
                        </td>
                    <?php else: ?>
                        <td class="dcf-txt-middle v_location with-controls" data-id="" data-microdata="false">
                            <?php echo "None"; ?>
                        </td>
                    <?php endif;?>

                    <td class="dcf-pr-0 dcf-txt-middle controls">
                        <div class="dcf-d-flex dcf-ai-center dcf-jc-flex-end">
                            <a
                                class="dcf-btn dcf-btn-primary"
                                href="<?php echo $datetime->getEditURL($context->calendar); ?>"
                            >
                                Edit
                            </a>
                            <button
                                class="dcf-btn dcf-btn-secondary dcf-ml-1"
                                form="delete-datetime-<?php echo $datetime->id; ?>"
                                type="submit"
                            >
                                Delete
                            </button>
                            <?php if ($allowCanceledDatetime === true && $datetime->recurringtype === 'none') : ?>
                                    <div class="dcf-input-checkbox dcf-mr-4 dcf-mb-0 dcf-ml-3 dcf-txt-sm">
                                        <input
                                            class="datetime-cancel-toggle"
                                            id="datetime-canceled-<?php echo $datetime->id; ?>"
                                            type="checkbox"
                                            value="1"
                                            <?php if ($datetime->isCanceled()) { echo CHECKED_INPUT; } ?>
                                            data-url="<?php echo $datetime->getEditURL($context->calendar); ?>"
                                        >
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
                                <td class="dcf-pl-7 dcf-txt-middle dates recurring" colspan="3">
                                    <?php
                                        echo date('n/d/y', strtotime($recurring_date->recurringdate)) .
                                        ' @ ' . date('g:ia', strtotime($datetime->starttime));
                                        ?>
                                </td>
                                <td class="dcf-pr-0 dcf-txt-middle controls recurring">
                                    <div class="dcf-d-flex dcf-ai-center dcf-jc-flex-end">
                                        <a
                                            class="dcf-btn dcf-btn-primary edit-recurring-edt"
                                            href="<?php
                                                echo $datetime->getEditRecurrenceURL(
                                                    $context->calendar,
                                                    $recurring_date->recurrence_id
                                                );
                                            ?>"
                                        >
                                            Edit
                                        </a>
                                        <button
                                            class="dcf-btn dcf-btn-secondary dcf-ml-1 delete-datetime-recurrence"
                                            type="submit"
                                            form="delete-datetime-<?php
                                                    echo $datetime->id
                                                ?>-recurrence-<?php
                                                    echo $recurring_date->recurrence_id
                                                ?>"
                                        >
                                            Delete
                                        </button>
                                        <div class="dcf-input-checkbox dcf-mr-4 dcf-mb-0 dcf-ml-3 dcf-txt-sm">
                                            <input
                                                class="recurrence-instance-cancel-toggle"
                                                id="recurrence-instance-canceled-<?php
                                                        echo $recurring_date->recurrence_id;
                                                    ?>"
                                                name="recurring-canceled"
                                                type="checkbox"
                                                value="1"
                                                <?php if ($recurring_date->isCanceled()) { echo CHECKED_INPUT; } ?>
                                                data-url="<?php
                                                    echo $datetime->getEditRecurrenceURL(
                                                        $context->calendar,
                                                        $recurring_date->recurrence_id
                                                    );
                                                ?>"
                                            >
                                            <label
                                                for="recurrence-instance-canceled-<?php
                                                        echo $recurring_date->recurrence_id;
                                                    ?>"
                                            >
                                                Canceled
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">
                                Notice: The above recurrence definition does not
                                have any recurrences and may be deleted.
                            </td>
                        </tr>
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
                            <li>
                                <a
                                    class="dcf-pagination-prev"
                                    href="?page=<?php echo $context->page - 1 ?>"
                                >
                                    Prev
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php $before_ellipsis_shown = false; $after_ellipsis_shown = false; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == $context->page): ?>
                                    <li><span class="dcf-pagination-selected"><?php echo $i; ?></span></li>
                                <?php elseif ($i <= 3 ||
                                                $i >= $total_pages - 2 ||
                                                $i == $context->page - 1 ||
                                                $i == $context->page - 2 ||
                                                $i == $context->page + 1 ||
                                                $i == $context->page + 2): ?>
                                    <li><a href="?page=<?php echo $i ?>"><?php echo $i; ?></a></li>
                                <?php elseif ($i < $context->page && !$before_ellipsis_shown): ?>
                                    <li><span class="dcf-pagination-ellipsis">...</span></li>
                                    <?php $before_ellipsis_shown = true; ?>
                                <?php elseif ($i > $context->page && !$after_ellipsis_shown): ?>
                                    <li><span class="dcf-pagination-ellipsis">...</span></li>
                                    <?php $after_ellipsis_shown = true; ?>
                                <?php endif; ?>
                        <?php endfor; ?>
                        <?php if($context->page != $total_pages): ?>
                            <li>
                                <a
                                    class="dcf-pagination-next"
                                    href="?page=<?php echo $context->page + 1 ?>"
                                >
                                    Next
                                </a>
                            </li>
                        <?php endif; ?>
                        </ol>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
        <hr>
    </section>

    <?php echo $savvy->render($context, 'EventFormImageUpload.tpl.php'); ?>

    <h2>Sharing</h2>
    <section class="dcf-mb-8 dcf-ml-5">
        <div class="details dcf-grid dcf-col-gap-vw">
            <fieldset class="dcf-col-100% dcf-col-25%-start@sm dcf-p-0 dcf-b-0">
                <legend class="dcf-pb-2">
                        Privacy
                        <div class="dcf-popup dcf-d-inline" data-point="true">
                            <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-p-0">
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
                                    If private this event will not show up in "All Calendar" results.
                                </p>
                            </div>
                        </div>
                    </legend>
                <div class="dcf-input-radio">
                    <input
                        id="sharing-private"
                        name="private_public"
                        type="radio"
                        value="private"
                        <?php if (!$event->approvedforcirculation) { echo CHECKED_INPUT; } ?>
                    />
                    <label for="sharing-private">Private</label>
                </div>
                <div class="dcf-input-radio">
                    <input
                        id="sharing-public"
                        name="private_public"
                        type="radio"
                        value="public"
                        <?php if ($event->approvedforcirculation) { echo CHECKED_INPUT; } ?>
                    />
                    <label for="sharing-public">Public</label>
                </div>
            </fieldset>
            <fieldset class="dcf-col-100% dcf-col-75%-end@sm dcf-mb-0 dcf-p-0 dcf-b-0" id="send_to_main">
                <legend
                    class="dcf-pb-2"
                >
                    Consider for Main
                        <abbr title="University of Nebraska–Lincoln"">UNL</abbr>
                    Calendar
                </legend>
                <?php if ($context->on_main_calendar): ?>
                    <img
                        src="<?php echo $base_frontend_url ?>templates/default/html/images/checkmark-16.png"
                        alt=""
                    >
                    (event has been sent to main UNL calendar for approval)
                <?php else: ?>
                    <div class="dcf-input-checkbox">
                        <input
                            id="send-to-main"
                            name="send_to_main"
                            type="checkbox"
                            <?php if (isset($post['send_to_main'])) { echo CHECKED_INPUT; } ?>
                        />
                        <label for="send-to-main">Yes</label>
                    </div>
                <?php endif; ?>
            </fieldset>
        </div>
        <hr>
    </section>

    <h2>Organizer Contact Info</h2>
    <section class="dcf-mb-8 dcf-ml-5">
        <div class="details dcf-d-grid dcf-grid-full dcf-grid-halves@md dcf-col-gap-vw">
            <div class="dcf-form-group">
                <label for="contact-name">Name</label>
                <input
                    id="contact-name"
                    name="contact_name"
                    type="text"
                    class="dcf-w-100%"
                    value="<?php echo $event->listingcontactname; ?>"
                />
            </div>
            <div class="dcf-form-group">
                <label for="contact-email">Email</label>
                <input
                    id="contact-email"
                    name="contact_email"
                    type="text"
                    class="dcf-w-100%"
                    value="<?php echo $event->listingcontactemail; ?>"
                />
            </div>
            <div class="dcf-form-group">
                <label for="contact-phone">Phone</label>
                <input
                    id="contact-phone"
                    name="contact_phone"
                    type="text"
                    class="dcf-w-100%"
                    value="<?php echo $event->listingcontactphone; ?>"
                />
            </div>
            <div class="dcf-form-group">
                <label for="contact-website">Website</label>
                <input
                    id="contact-website"
                    name="contact_website"
                    type="text"
                    class="dcf-w-100%"
                    value="<?php echo $event->listingcontacturl; ?>"
                />
            </div>
            <fieldset class="dcf-mb-0 dcf-p-0 dcf-b-0" id="contact-type">
                <legend class="dcf-pb-2">
                        Organizer Type
                        <div class="dcf-popup dcf-d-inline" data-point="true">
                            <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-p-0">
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
                                    This is used to format organizer data for google microdata.
                                </p>
                            </div>
                        </div>
                    </legend>
                <div class="dcf-input-radio">
                    <input
                        id="contact-type-person"
                        name="contact_type"
                        type="radio"
                        value="person"
                        <?php
                            if (isset($event->listingcontacttype) &&
                                $event->listingcontacttype === 'person'
                            ) { echo CHECKED_INPUT; }
                        ?>
                    >
                    <label for="contact-type-person">Person</label>
                </div>
                <div class="dcf-input-radio">
                    <input
                        id="contact-type-organization"
                        name="contact_type"
                        type="radio"
                        value="organization"
                        <?php
                            if (isset($event->listingcontacttype) &&
                                $event->listingcontacttype === 'organization'
                            ) { echo CHECKED_INPUT; }
                        ?>
                    >
                    <label for="contact-type-organization">Organization</label>
                </div>
            </fieldset>
        </div>
        <hr>
    </section>

    <button class="dcf-btn dcf-btn-primary" type="submit">Save Event</button>
</form>

<?php
$page->addScriptDeclaration("
require(['jquery'], function($) {
    $('.delete-datetime').submit(function (submit) {
        if (!window.confirm('Are you sure you want to delete this instance?')) {
            submit.preventDefault();
        }
    });

    $('.delete-datetime-recurrence').submit(function (submit) {
        if (!window.confirm(
            'Are you sure you want to delete this occurrence of your recurring instance?' +
            ' The rest of the recurrences will remain.'
        )) {
            submit.preventDefault();
        }
    });

    $('.edit-recurring-edt').click(function (click) {
        if (!window.confirm('You are editing a single occurrence of a recurring instance.')) {
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
