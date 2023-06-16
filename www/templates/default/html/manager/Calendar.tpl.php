<?php
use UNL\UCBCN\Permission;

    const ARIA_SELECTED = 'aria-selected="true"';
    // Disable Promote since not being used on main calendar homepage.
    const ALLOW_PROMOTE = FALSE;
    $user = \UNL\UCBCN\Manager\Auth::getCurrentUser();
    $events = $context->getEvents();
    $categorized_events = $context->getCategorizedEvents();

    $total_pages = NULL;
    switch ($context->tab) {
        case 'pending':
            $total_pages = ceil(count($categorized_events['pending']) / 10);
            break;
        case 'upcoming':
            $total_pages = ceil(count($categorized_events['posted']) / 10);
            break;
        case 'past':
            $total_pages = ceil(count($categorized_events['archived']) / 10);
            break;
        default:
            $total_pages = 1;
    }

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
        $context->calendar->name => NULL
    );
    $crumbs->search = TRUE;
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<form class="dcf-form" id="search-form" action="<?php echo $context->calendar->getSearchURL(); ?>" style="display: none;">
    <label class="dcf-label" for="events-search">Search</label>
    <div class="dcf-input-group">
        <select class="dcf-txt-sm" id="event_type_id" name="event_type_id" aria-label="Event Bulk Move Options">
            <option value="">Activity by Type</option>
            <?php $eventTypes = new UNL\UCBCN\Calendar\EventTypes(array()); ?>
            <?php foreach ($eventTypes as $type) { ?>
                <option value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
            <?php } ?>
        </select>
        <input type="text" name="search_term" id="events-search" />
        <button type="submit" class="dcf-btn dcf-btn-primary">Search</button>
    </div>
</form>
<br>
<div class="dcf-tabs dcf-tabs-responsive dcf-mt-0 dcf-mb-9">
    <ul class="dcf-tabs-list dcf-list-bare dcf-mb-0" role="tablist">
        <li class="dcf-tabs-list-item dcf-mb-0 <?php if ($context->tab == 'pending') { echo 'selected'; } ?>" role="presentation"><a class="dcf-tab dcf-d-block" role="tab" <?php if ($context->tab == 'pending') { echo ARIA_SELECTED; } ?> href="<?php echo $context->tab == 'pending' ? '#pending' : '?tab=pending'; ?>">Pending (<?php echo count($categorized_events['pending']); ?>)</a></li>
        <li class="dcf-tabs-list-item dcf-mb-0 <?php if ($context->tab == 'upcoming') { echo 'selected'; } ?>" role="presentation"><a class="dcf-tab dcf-d-block" role="tab" <?php if ($context->tab == 'upcoming') { echo ARIA_SELECTED; } ?> href="<?php echo $context->tab == 'upcoming' ? '#upcoming' : '?tab=upcoming'; ?>">Upcoming (<?php echo count($categorized_events['posted']); ?>)</a></li>
        <li class="dcf-tabs-list-item dcf-mb-0 <?php if ($context->tab == 'past') { echo 'selected'; } ?>" role="presentation"><a class="dcf-tab dcf-d-block" role="tab" <?php if ($context->tab == 'past') { echo ARIA_SELECTED; } ?> href="<?php echo $context->tab == 'past' ? '#past' : '?tab=past'; ?>">Past (<?php echo count($categorized_events['archived']); ?>)</a></li>
    </ul>
    <div class="dcf-tabs-panel"  role="tabpanel">
        <div id="<?php echo $context->tab; ?>">
            <?php if (count($events) == 0): ?>
                There are no <?php echo $context->tab ?> events.
            <?php else: ?>
                <?php if ($context->tab == 'past' && $user->hasPermission(\UNL\UCBCN\Permission::EVENT_DELETE_ID, $context->calendar->id)): ?>
                    <div class="dcf-mb-5 medium-hidden">
                        <a class="dcf-btn dcf-btn-secondary" href="<?php echo $context->calendar->getCleanupURL() ?>">Clean Up Old Events</a>
                    </div>
                <?php endif; ?>

                <div class="small-hidden dcf-mb-4">
                    <h2 class="dcf-txt-xs">Actions Summary</h2>
                    <ul class="dcf-txt-2xs">
                        <?php if ($context->tab != 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_PENDING_ID, $context->calendar->id)): ?>
                        <li>Move to Pending - Moves event to pending state and will not display on current calendar.</li>
	                    <?php elseif ($context->tab == 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_UPCOMING_ID, $context->calendar->id)): ?>
                        <li>Move to Upcoming - Moves event to upcoming/past state and will display on current calendar.</li>
                        <?php endif; ?>
	                    <?php if ($user->hasPermission(Permission::EVENT_FEATURE_ID, $context->calendar->id)): ?>
                        <li>Featured - The most current non-pending featured events will display on the featured and home pages.</li>
                        <li>Pinned - The most current non-pending pinned event will always display on the featured and home pages.</li>
                        <?php endif; ?>
	                    <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_RECOMMEND_ID, $context->calendar->id)): ?>
                        <li>Recommend - You may recommend an event to any calendar you have access to or any calendar with the same account as current calendar which allows it.</li>
                        <?php endif; ?>
	                    <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_DELETE_ID, $context->calendar->id)): ?>
                        <li>Delete - Removes the event from current calendar and if event originated in current calendar it will remove it from all system calendars.</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="dcf-mb-5 medium-hidden">
                  <select id="bulk-action" title="Bulk Action" class="bulk-<?php echo $context->tab; ?>-event-tools dcf-input-select dcf-txt-sm">
                      <option value="">Bulk Actions</option>
                      <?php if ($context->tab == 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_UPCOMING_ID, $context->calendar->id)): ?>
                        <option value="move-to-upcoming">Move to Upcoming</option>
                      <?php elseif ($context->tab != 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_PENDING_ID, $context->calendar->id)): ?>
                        <option value="move-to-pending">Move to Pending</option>
                      <?php endif; ?>

                      <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_DELETE_ID, $context->calendar->id)): ?>
                        <option value="delete">Delete</option>
                      <?php endif; ?>
                  </select>
                    <form id="bulk-action-form" method="POST" action="<?php echo $context->calendar->getBulkMoveActionURL() ?>" class="dcf-form delete-form dcf-d-none">
                      <input type="text" title="Bulk Action IDs" id="bulk-action-ids" name="ids">
                      <input type="text" title="Bulk Action Action" id="bulk-action-action" name="action">
                      <input type="text" name="status" value="<?php echo $context->tab ?>">
                      <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                      <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                      <button class="dcf-btn dcf-btn-primary" type="submit">Submit</button>
                    </form>
                </div>
                <div class="event-page">
                    <table class="dcf-table dcf-table-bordered dcf-w-100% event-list">
                        <thead class="small-hidden">
                            <tr>
                                <th scope="col" class="medium-hidden dcf-pl-6 dcf-w-4">
                                    <div class="dcf-input-checkbox">
                                        <input type="checkbox" id="checkbox-toggle" title="Toggle All Events">
                                        <label for="checkbox-toggle"><span class="dcf-sr-only">Toggle all events</span></label>
                                    </div>
                                </th>
                                <th scope="col">Title</th>
                                <th scope="col">Date/Location</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php try { ?>
                            <?php foreach($events as $event): ?>
                                <?php
                                    $featured =  $event->isFeaturedWithCalendar($context->calendar->getRawObject());
                                    $pinned = $event->isPinnedWithCalendar($context->calendar->getRawObject());
                                ?>
                                <tr>
                                    <td class="medium-hidden dcf-pl-6">
                                        <div class="dcf-input-checkbox">
                                            <input type="checkbox" id="select-event-<?php echo $event->id ?>" title="Select Event" class="select-event" data-id="<?php echo $event->id; ?>">
                                            <label for="select-event-<?php echo $event->id ?>"><span class="dcf-sr-only">Check this event</span></label>
                                        </div>
                                    </td>
                                    <td class="small-hidden">
                                        <?php if ($event->userCanEdit()): ?>
                                            <a href="<?php echo $event->getEditURL($controller->getCalendar()) ?>">
                                            <?php echo $event->title; ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo $event->getViewURL($controller->getCalendar()) ?>">
                                            <?php echo $event->title; ?>
                                            </a> (preview)
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="small-block dcf-d-none calendar-event-title">
                                            <?php if ($event->userCanEdit()): ?>
                                                <a href="<?php echo $event->getEditURL($controller->getCalendar()) ?>">
                                                <?php echo $event->title; ?>
                                                </a>
                                            <?php else: ?>
                                                <?php echo $event->title; ?>
                                            <?php endif; ?>
                                        </div>
                                        <ul>
                                        <?php $datetimes = $event->getDateTimes(); ?>
                                        <?php $count = 0; ?>
                                        
                                        <?php foreach($datetimes as $datetime): ?>
                                            <li>
                                                <?php if (++$count <= 3) :
                                                    $date_format = 'n/d/y';
                                                    $time_format = 'g:ia';
                                                    $formatted_start_time = date(
                                                        $time_format,
                                                        strtotime($datetime->starttime)
                                                    );
                                                    $formatted_start_date = date(
                                                        $time_format,
                                                        strtotime($datetime->starttime)
                                                    );
                                                ?>
                                                    <div>
                                                        <?php
                                                        {
                                                            if ($datetime->recurringtype == 'none') {
                                                                echo date(
                                                                    $date_format . ' @ ' . $time_format,
                                                                    strtotime($datetime->starttime)
                                                                );
                                                            } elseif ($datetime->recurringtype == 'daily' ||
                                                                $datetime->recurringtype == 'weekly' ||
                                                                $datetime->recurringtype == 'biweekly' ||
                                                                $datetime->recurringtype == 'annually'
                                                            ) {
                                                                echo ucwords($datetime->recurringtype) .
                                                                    ' @ ' .
                                                                    date(
                                                                        $time_format,
                                                                        strtotime($datetime->starttime)
                                                                    ) .
                                                                    ':<br>' .
                                                                    $formatted_start_date .
                                                                    ' - ' .
                                                                    date(
                                                                        $date_format,
                                                                        strtotime($datetime->recurs_until)
                                                                    );
                                                            } elseif ($datetime->recurringtype == 'monthly') {
                                                                if ($datetime->rectypemonth == 'lastday') {
                                                                    echo 'Last day of every month @ ' .
                                                                        $formatted_start_time .
                                                                        ':<br>' .
                                                                        $formatted_start_date .
                                                                        ' - ' .
                                                                        date(
                                                                            $date_format,
                                                                            strtotime($datetime->recurs_until)
                                                                        );
                                                                } elseif ($datetime->rectypemonth == 'date') {
                                                                    echo ordinal(
                                                                            date('d', strtotime($datetime->starttime))
                                                                        ) .
                                                                        ' of every month @ ' .
                                                                        $formatted_start_time .
                                                                        ':<br>' .
                                                                        $formatted_start_date .
                                                                        ' - ' .
                                                                        date(
                                                                            $date_format,
                                                                            strtotime($datetime->recurs_until)
                                                                        );
                                                                } else {
                                                                    echo ucwords($datetime->rectypemonth) .
                                                                        date(' l', strtotime($datetime->starttime)) .
                                                                        ' of every month' .
                                                                        ':<br>' .
                                                                        $formatted_start_date .
                                                                        ' - ' .
                                                                        date(
                                                                            $date_format,
                                                                            strtotime($datetime->recurs_until)
                                                                        );
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                        $location = $datetime->getLocation();
                                                        if (isset($location) && !empty($location)):
                                                    ?>
                                                    <div class="dcf-popup" data-point="true">
                                                        <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup" type="button">
                                                            <?php echo $location->name; ?>
                                                        </button>
                                                        <div
                                                            class="dcf-popup-content
                                                                unl-cream
                                                                unl-bg-blue
                                                                dcf-p-3 dcf-rounded"
                                                            style="width: 100%; min-width: 25ch;"
                                                        >
                                                            <dl>
                                                                <?php
                                                                    if(isset($location->name) &&
                                                                    !empty($location->name)):
                                                                ?>
                                                                    <dt>Name</dt>
                                                                    <dd><?php echo $location->name; ?></dd>
                                                                <?php endif; ?>

                                                                <?php
                                                                    if(isset($location->streetaddress1) &&
                                                                    !empty($location->streetaddress1)):
                                                                ?>
                                                                    <dt>Street Address 1</dt>
                                                                    <dd><?php echo $location->streetaddress1; ?></dd>
                                                                <?php endif; ?>

                                                                <?php
                                                                    if(isset($location->streetaddress2) &&
                                                                    !empty($location->streetaddress2)):
                                                                ?>
                                                                    <dt>Street Address 2</dt>
                                                                    <dd><?php echo $location->streetaddress2; ?></dd>
                                                                <?php endif; ?>

                                                                <?php
                                                                    if(isset($location->city) &&
                                                                    !empty($location->city)):
                                                                ?>
                                                                    <dt>City</dt>
                                                                    <dd><?php echo $location->city; ?></dd>
                                                                <?php endif; ?>

                                                                <?php
                                                                    if(isset($location->state) &&
                                                                    !empty($location->state)):
                                                                ?>
                                                                    <dt>State</dt>
                                                                    <dd><?php echo $location->state; ?></dd>
                                                                <?php endif; ?>

                                                                <?php
                                                                    if(isset($location->zip) &&
                                                                    !empty($location->zip)):
                                                                ?>
                                                                    <dt>Zip</dt>
                                                                    <dd><?php echo $location->zip; ?></dd>
                                                                <?php endif; ?>

                                                                <?php
                                                                    if(isset($location->room) &&
                                                                    !empty($location->room)):
                                                                ?>
                                                                    <dt>Room</dt>
                                                                    <dd><?php echo $location->room; ?></dd>
                                                                <?php endif; ?>

                                                                <?php
                                                                    if(isset($datetime->room) &&
                                                                    !empty($datetime->room)):
                                                                ?>
                                                                    <dt>Room</dt>
                                                                    <dd><?php echo $datetime->room; ?></dd>
                                                                <?php
                                                                    elseif(isset($location->room) &&
                                                                        !empty($location->room)):
                                                                ?>
                                                                    <dt>Room</dt>
                                                                    <dd><?php echo $location->room; ?></dd>
                                                                <?php endif; ?>

                                                                <?php
                                                                    if(isset($datetime->directions) &&
                                                                        !empty($datetime->directions)):
                                                                ?>
                                                                    <dt>Directions</dt>
                                                                    <dd><?php echo $datetime->directions; ?></dd>
                                                                <?php
                                                                    elseif(isset($location->directions) &&
                                                                        !empty($location->directions)):
                                                                ?>
                                                                    <dt>Directions</dt>
                                                                    <dd><?php echo $location->directions; ?></dd>
                                                                <?php endif; ?>

                                                                <?php
                                                                    if(
                                                                        isset(
                                                                            $datetime->location_additionalpublicinfo
                                                                        ) &&
                                                                        !empty($datetime->location_additionalpublicinfo)
                                                                    ):
                                                                ?>
                                                                    <dt>Additional Public Info</dt>
                                                                    <dd>
                                                                        <?php
                                                                            echo $datetime
                                                                                ->location_additionalpublicinfo;
                                                                        ?>
                                                                    </dd>
                                                                <?php
                                                                    elseif(isset($location->additionalpublicinfo) &&
                                                                        !empty($location->additionalpublicinfo)
                                                                    ):
                                                                ?>
                                                                    <dt>Additional Public Info</dt>
                                                                    <dd>
                                                                        <?php
                                                                            echo $location->additionalpublicinfo;
                                                                        ?>
                                                                    </dd>
                                                                <?php endif; ?>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                    <?php
                                                        endif;
                                                        $getWebcast = $datetime->getWebcast();
                                                        if (isset($getWebcast) && !empty($getWebcast)):
                                                    ?>
                                                        <div class="dcf-popup" data-point="true">
                                                            <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup" type="button">
                                                                <?php echo $getWebcast->title; ?>
                                                            </button>
                                                            <div
                                                                class="dcf-popup-content
                                                                    unl-cream
                                                                    unl-bg-blue
                                                                    dcf-p-3 dcf-rounded"
                                                                style="min-width: 25ch;"
                                                            >
                                                                <dl>
                                                                    <?php
                                                                        if(isset($getWebcast->title) &&
                                                                            !empty($getWebcast->title)):
                                                                    ?>
                                                                        <dt>Name</dt>
                                                                        <dd><?php echo $getWebcast->title; ?></dd>
                                                                    <?php endif; ?>

                                                                    <?php
                                                                        if(isset($getWebcast->url) &&
                                                                            !empty($getWebcast->url)):
                                                                    ?>
                                                                        <dt>URL</dt>
                                                                        <dd><?php echo $getWebcast->url; ?></dd>
                                                                    <?php endif; ?>

                                                                    <?php
                                                                        if(
                                                                            isset(
                                                                                $datetime->webcast_additionalpublicinfo
                                                                            ) &&
                                                                            !empty(
                                                                                $datetime->webcast_additionalpublicinfo
                                                                            )
                                                                        ):
                                                                    ?>
                                                                        <dt>Additional Public Info</dt>
                                                                        <dd>
                                                                            <?php
                                                                                echo $datetime
                                                                                    ->webcast_additionalpublicinfo;
                                                                            ?>
                                                                        </dd>
                                                                    <?php
                                                                        elseif(isset($getWebcast->additionalinfo) &&
                                                                            !empty($getWebcast->additionalinfo)
                                                                        ):
                                                                    ?>
                                                                        <dt>Additional Public Info</dt>
                                                                        <dd>
                                                                            <?php
                                                                                echo $getWebcast->additionalinfo;
                                                                            ?>
                                                                        </dd>
                                                                    <?php endif; ?>
                                                                </dl>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    ...and <?php echo (count($datetimes) - 3); ?> more
                                                <?php break; ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                        </ul>
                                        <select
                                            id="event-action-<?php echo $event->id ?>-small"
                                            class="<?php echo $context->tab ?>-event-tools small-block dcf-input-select dcf-d-none dcf-txt-md"
                                            title="Select an Action"
                                            data-id="<?php echo $event->id; ?>"
                                            data-recommend-url="<?php echo $event->getRecommendURL($controller->getCalendar()) ?>"
                                        >
                                          <option value="">Select an Action</option>
                                            <?php if ($context->tab == 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_UPCOMING_ID, $context->calendar->id)): ?>
                                              <option value="move-to-upcoming">Move to Upcoming</option>
                                            <?php elseif ($context->tab != 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_PENDING_ID, $context->calendar->id)): ?>
                                              <option value="move-to-pending">Move to Pending</option>
                                            <?php endif; ?>

                                            <?php if (ALLOW_PROMOTE && $context->calendar->id == UNL\UCBCN::$main_calendar_id && $context->tab != 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_ID, $context->calendar->id)): ?>
                                              <option value="promote">Promote Event</option>
                                              <option value="hide-promo">Remove from Promo Bar</option>
                                            <?php endif; ?>

                                            <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_RECOMMEND_ID, $context->calendar->id)): ?>
                                              <option value="recommend">Recommend</option>
                                            <?php endif; ?>

                                            <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_DELETE_ID, $context->calendar->id)): ?>
                                              <option value="delete">Delete</option>
                                            <?php endif; ?>
                                        </select>
                                        <br class="small-block dcf-d-none">
                                    </td>
                                    <td class="small-hidden">
                                      <select
                                          id="event-action-<?php echo $event->id ?>"
                                          class="<?php echo $context->tab ?>-event-tools dcf-input-select dcf-txt-md"
                                          title="Select an Action"
                                          data-id="<?php echo $event->id; ?>"
                                          data-recommend-url="<?php echo $event->getRecommendURL($controller->getCalendar()) ?>"
                                      >
                                        <option value="">Select an Action</option>
                                          <?php if ($context->tab == 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_UPCOMING_ID, $context->calendar->id)): ?>
                                            <option value="move-to-upcoming">Move to Upcoming</option>
                                          <?php elseif ($context->tab != 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_PENDING_ID, $context->calendar->id)): ?>
                                            <option value="move-to-pending">Move to Pending</option>
                                          <?php endif; ?>

                                          <?php if (ALLOW_PROMOTE && $context->calendar->id == UNL\UCBCN::$main_calendar_id && $context->tab != 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_ID, $context->calendar->id)): ?>
                                            <option value="promote">Promote Event</option>
                                            <option value="hide-promo">Remove from Promo Bar</option>
                                          <?php endif; ?>

                                          <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_RECOMMEND_ID, $context->calendar->id)): ?>
                                            <option value="recommend">Recommend</option>
                                          <?php endif; ?>

                                          <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_DELETE_ID, $context->calendar->id)): ?>
                                            <option value="delete">Delete</option>
                                          <?php endif; ?>
                                        </select>

                                        <?php if ($user->hasPermission(Permission::EVENT_FEATURE_ID, $context->calendar->id)): ?>
                                        <div class="dcf-p-3">
                                            <?php
                                                $featuredChecked = ($featured === TRUE) ? ' checked' : '';
                                                $pinnedChecked = ($pinned === TRUE) ? ' checked' : '';
                                            ?>
                                            <div class="dcf-input-checkbox">
                                                <input class="feature_event_input" id="featured-event-<?php echo $event->id; ?>" data-event-id="<?php echo $event->id; ?>"  data-url="<?php echo $event->getToggleFeatureEventAttributeURL($controller->getCalendar()); ?>" type="checkbox" value="1"<?php echo $featuredChecked; ?>>
                                                <label for="featured-event-<?php echo $event->id; ?>">Featured</label>
                                            </div>
                                            <div class="dcf-input-checkbox">
                                                <input class="pin_event_input" id="pinned-event-<?php echo $event->id; ?>" data-event-id="<?php echo $event->id; ?>" data-url="<?php echo $event->getToggleFeatureEventAttributeURL($controller->getCalendar()); ?>" type="checkbox" value="1"<?php echo $pinnedChecked; ?>>
                                                <label for="pinned-event-<?php echo $event->id; ?>">Pinned</label>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <form id="move-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getMoveURL($controller->getCalendar()) ?>" class="dcf-form delete-form dcf-d-none">
                                          <input type="text" title="New Status" name="new_status" id="move-target-<?php echo $event->id; ?>">
                                          <input type="text" title="Event ID" name="event_id" value="<?php echo $event->id ?>">
                                          <input type="text" name="status" value="<?php echo $context->tab ?>">
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                                          <button class="dcf-btn dcf-btn-primary" type="submit">Submit</button>
                                        </form>
                                        <form id="delete-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getDeleteURL($controller->getCalendar()) ?>" class="dcf-form delete-form dcf-d-none">
                                          <input type="text" name="status" value="<?php echo $context->tab ?>">
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                                          <button class="dcf-btn dcf-btn-primary" type="submit">Submit</button>
                                        </form>
                                        <form id="promote-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getPromoteURL($controller->getCalendar()) ?>" class="dcf-form delete-form dcf-d-none">
                                          <input type="text" id="promote-target-<?php echo $event->id; ?>" name="status" value="promote">
                                          <input type="text" title="Event ID" name="event_id" value="<?php echo $event->id ?>">
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                                          <button class="dcf-btn dcf-btn-primary" type="submit">Submit</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php } catch (\OutOfBoundsException $e) {} ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                    <?php $page->addScriptDeclaration("WDN.initializePlugin('pagination');"); ?>
                    <div class="dcf-mt-2" style="text-align: center;">
                        <div style="display: inline-block;">
                            <nav class="dcf-pagination">
                                <ol class="dcf-list-bare dcf-list-inline">
                                <?php if($context->page != 1): ?>
                                    <li><a class="dcf-pagination-prev" href="?tab=<?php echo $context->tab?>&amp;page=<?php echo $context->page - 1 ?>">Prev</a></li>
                                <?php endif; ?>
                                <?php $before_ellipsis_shown = FALSE; $after_ellipsis_shown = FALSE; ?>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <?php if ($i == $context->page): ?>
                                            <li><span class="dcf-pagination-selected"><?php echo $i; ?></span></li>
                                        <?php elseif ($i <= 3 || $i >= $total_pages - 2 || $i == $context->page - 1 ||
                                                    $i == $context->page - 2 || $i == $context->page + 1 || $i == $context->page + 2): ?>
                                            <li><a href="?tab=<?php echo $context->tab?>&amp;page=<?php echo $i ?>"><?php echo $i; ?></a></li>
                                        <?php elseif ($i < $context->page && !$before_ellipsis_shown): ?>
                                            <li><span class="dcf-pagination-ellipsis">...</span></li>
                                            <?php $before_ellipsis_shown = TRUE; ?>
                                        <?php elseif ($i > $context->page && !$after_ellipsis_shown): ?>
                                            <li><span class="dcf-pagination-ellipsis">...</span></li>
                                            <?php $after_ellipsis_shown = TRUE; ?>
                                        <?php endif; ?>
                                <?php endfor; ?>
                                <?php if($context->page != $total_pages): ?>
                                    <li><a class="dcf-pagination-next" href="?tab=<?php echo $context->tab?>&amp;page=<?php echo $context->page + 1 ?>">Next</a></li>
                                <?php endif; ?>
                                </ol>
                            </nav>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$page->addScriptDeclaration("WDN.loadCSS('https://wdn.unl.edu/wdn/templates_5.3/js/js-css/tabs.css');");
$tokenNameKey = $controller->getCSRFHelper()->getTokenNameKey();
$tokenNameValue = $controller->getCSRFHelper()->getTokenName();
$tokenValueKey = $controller->getCSRFHelper()->getTokenValueKey();
$tokenValueValue = $controller->getCSRFHelper()->getTokenValue();
$tokenString = $tokenNameKey . '=' . $tokenNameValue . '&' . $tokenValueKey . '=' . $tokenValueValue;
$page->addScriptDeclaration("
    var featureEventInputs = document.getElementsByClassName('feature_event_input');
    var i;
    for (i = 0; i < featureEventInputs.length; i++) {
        featureEventInputs[i].addEventListener('change', function() {
            var token = '" . $tokenString . "';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', this.dataset.url);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.send('type=feature&event_id=' + this.dataset.eventId + '&featured=' + this.checked + '&' + token);
        });
    }
    var pinEventInputs = document.getElementsByClassName('pin_event_input');
    for (i = 0; i < pinEventInputs.length; i++) {
        pinEventInputs[i].addEventListener('change', function() {
            var token = '" . $tokenString . "';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', this.dataset.url);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.send('type=pin&event_id=' + this.dataset.eventId + '&pinned=' + this.checked + '&' + token);
        });
    }
");
?>
