<?php
    use UNL\UCBCN\Event\Occurrence;

    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        'Search' => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<?php
	$total_pages = ceil(count($context->events) / 10);
?>
<?php $eventTypes = new UNL\UCBCN\Calendar\EventTypes(array()); ?>
<form class="dcf-form dcf-mb-6">
    <label for="events-search">Search</label>
    <div class="dcf-input-group">
        <select class="dcf-txt-sm" id="event_type_id" name="event_type_id" aria-label="Filter by Activity Type">
            <option value="">Filter by Activity Type</option>
            <?php foreach ($eventTypes as $type) { ?>
                <?php $selected = !empty($context->event_type_id) && $context->event_type_id == $type->id ? ' selected=selected ' : ''; ?>
                <option <?php echo $selected; ?> value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
            <?php } ?>
        </select>
        <input id="events-search" name="search_term" type="text" value="<?php echo $context->search_term ?>"/>
        <button class="dcf-btn dcf-btn-primary" type="submit">Search</button>
    </div>
</form>

<div>
    <?php if (count($context->events) == 0): ?>
        There are no results.
    <?php else: ?>
        <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_PENDING_ID, $context->calendar->id)): ?>
            <div class="medium-hidden dcf-mb-5">
                <select id="bulk-action" title="Bulk Action" class="bulk-search-event-tools dcf-input-select dcf-txt-sm" aria-label="Event Bulk Move Options">
                    <option value="">Bulk Actions</option>
                    <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_UPCOMING_ID, $context->calendar->id)): ?>
                        <option value="move-to-upcoming">Move to Upcoming</option>
                    <?php endif; ?>

                    <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_PENDING_ID, $context->calendar->id)): ?>
                        <option value="move-to-pending">Move to Pending</option>
                    <?php endif; ?>
                </select>
                <form id="bulk-action-form" method="POST" action="<?php echo $context->calendar->getBulkAddActionURL() ?>" class="dcf-form dcf-d-none">
                    <input type="text" title="Bulk Action IDs" id="bulk-action-ids" name="ids">
                    <input type="text" title="Bulk Action Action" id="bulk-action-action" name="action">
                    <input type="hidden" name="source" value="search">
                    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                    <button class="dcf-btn dcf-btn-primary" type="submit">Submit</button>
                </form>
            </div>
        <?php endif; ?>
        <div class="dcf-table dcf-table-bordered dcf-w-100% event-page">
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
                        <th scope="col">Original Calendar</th>
                        <th scope="col">Date/Location</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($context->events as $event): ?>
                        <tr>
                            <td class="medium-hidden dcf-pl-6">
                                <?php if (!$event->getStatusWithCalendar($context->calendar->getRawObject())): ?>
                                <div class="dcf-input-checkbox">
                                    <input type="checkbox" id="select-event-<?php echo $event->id ?>" title="Select Event" class="select-event" data-id="<?php echo $event->id; ?>">
                                    <label for="select-event-<?php echo $event->id ?>"><span class="dcf-sr-only">Check this event</span></label>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="small-hidden">
                                <?php if ($event->userCanEdit()): ?>
                                    <a class="unl-prerender" href="<?php echo $event->getEditURL() ?>"><?php echo $event->title; ?></a>
                                <?php else: ?>
                                    <a class="unl-prerender" href="<?php echo $event->getViewURL($controller->getCalendar()) ?>">
                                        <?php echo $event->title; ?>
                                    </a> (preview)
                                <?php endif; ?>
                            </td>
                            <td class="small-hidden">
                                <?php $calendar = $event->getOriginCalendar() ?>
                                <?php if ($calendar): ?>
                                    <a class="unl-prerender" href="<?php echo $calendar->getFrontendURL() ?>"><?php echo $calendar->name ?></a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small-block dcf-d-none calendar-event-title">
                                    <?php if ($event->userCanEdit()): ?>
                                        <a class="unl-prerender" href="<?php echo $event->getEditURL() ?>">
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

                                            // Set up default values
                                            $recurring_details = '';
                                            $date_details = date(
                                                $date_format,
                                                strtotime($datetime->starttime)
                                            );
                                            $time_details = date(
                                                $time_format,
                                                strtotime($datetime->starttime)
                                            );

                                            // Define recurring details
                                            if ($datetime->recurringtype == 'daily' ||
                                                $datetime->recurringtype == 'weekly' ||
                                                $datetime->recurringtype == 'biweekly' ||
                                                $datetime->recurringtype == 'annually'
                                            ) {
                                                $recurring_details = ucwords($datetime->recurringtype) . ':';
                                            } elseif ($datetime->recurringtype == 'monthly') {
                                                if ($datetime->rectypemonth == 'lastday') {
                                                    $recurring_details = 'Last day of every month:';
                                                } elseif ($datetime->rectypemonth == 'date') {
                                                    $recurring_details = date('jS', strtotime($datetime->starttime)) . ' of every month:';
                                                } else {
                                                    $recurring_details = ucwords($datetime->rectypemonth) . date(' l', strtotime($datetime->starttime)). ' of every month:';
                                                }
                                            }

                                            // Define date range if the recurs until is set
                                            if (
                                                isset($datetime->recurs_until) &&
                                                $datetime->recurs_until > $datetime->starttime
                                            ) {
                                                $date_details .= ' to ' . date(
                                                    $date_format,
                                                    strtotime($datetime->recurs_until)
                                                );
                                            }

                                            // Defines time details depending on time mode
                                            if ($datetime->isAllDay()) {
                                                $time_details = 'All day';
                                            } elseif ($datetime->timemode === Occurrence::TIME_MODE_TBD) {
                                                $time_details = 'Time <abbr title="To Be Determined">TBD</abbr>';
                                            } elseif ($datetime->timemode === Occurrence::TIME_MODE_START_TIME_ONLY ) {
                                                $time_details = 'Starting at ' . $time_details;
                                            } elseif ($datetime->timemode === Occurrence::TIME_MODE_END_TIME_ONLY) {
                                                $time_details = 'Ending at ' . date(
                                                    $time_format,
                                                    strtotime($datetime->endtime)
                                                );
                                            } else {
                                                // If we get here then check if there is an endtime
                                                // and it is after start time
                                                if (
                                                    isset($datetime->endtime) &&
                                                    $datetime->endtime > $datetime->starttime
                                                ) {
                                                    $time_details .= ' to '. date(
                                                        ' g:ia',
                                                        strtotime($datetime->endtime)
                                                    );
                                                }
                                            }
                                        ?>
                                            <div>
                                                <?php if (!empty($recurring_details)): ?>
                                                    <span class="dcf-d-block"><?php echo $recurring_details; ?></span>
                                                <?php endif; ?>
                                                <span class="dcf-d-block"><?php echo $date_details; ?></span>
                                                <span class="dcf-d-block"><?php echo $time_details; ?></span>
                                            </div>
                                            <?php
                                                $location = $datetime->getLocation();
                                                if (isset($datetime->location_id) && $location !== false):
                                            ?>
                                            <div class="dcf-d-flex dcf-ai-center">
                                            <svg
                                                class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current"
                                                aria-hidden="true"
                                                focusable="false"
                                                height="24"
                                                width="24"
                                                viewBox="0 0 24 24"
                                            >
                                                <path d="M12 0C7.589 0 4 3.589 4 8c0 4.245 7.273 15.307
                                                    7.583 15.775 a.497.497 0 00.834 0C12.727 23.307 20
                                                    12.245 20 8c0-4.411-3.589-8-8-8zm0 22.58 C10.434
                                                    20.132 5 11.396 5 8c0-3.86 3.14-7 7-7s7 3.14 7 7c0
                                                    3.395-5.434 12.132-7 14.58z"></path>
                                                <path d="M12 4.5c-1.93 0-3.5 1.57-3.5 3.5s1.57 3.5 3.5
                                                    3.5 3.5-1.57 3.5-3.5-1.57-3.5-3.5-3.5zm0 6 c-1.378
                                                    0-2.5-1.122-2.5-2.5s1.122-2.5 2.5-2.5 2.5 1.122
                                                    2.5 2.5-1.122 2.5-2.5 2.5z"></path>
                                            </svg>
                                            <span class="dcf-sr-only">Physical Location:</span>
                                            <div class="dcf-popup" data-point="true">
                                                <button
                                                    class="dcf-btn dcf-btn-tertiary dcf-btn-popup"
                                                    type="button"
                                                >
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
                                            </div>
                                            <?php
                                                endif;
                                                $getWebcast = $datetime->getWebcast();
                                                if (isset($datetime->webcast_id) && $getWebcast !== false):
                                            ?>
                                            <div class="dcf-d-flex dcf-ai-center">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current"
                                                    aria-hidden="true"
                                                    focusable="false"
                                                    height="24"
                                                    width="24"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path d="M22,1H2C0.897,1,0,1.937,0,3.088v14.824C0,19.063,
                                                        0.897,20,2,20 h9.5v1H5c-0.276,0-0.5,0.224-0.5,
                                                        0.5S4.724,22,5,22h14 c0.276,0,0.5-0.224,0.5-0.5
                                                        S19.276,21,19,21h-6.5v-1H22c1.103,0,2-0.937,
                                                        2-2.088V3.088 C24,1.937,23.103,1,22,1z M2,2h20
                                                        c0.551,0,1,0.488,1,1.088 V15H1V3.088C1,2.488,
                                                        1.449,2,2,2z M22,19H2c-0.551,0-1-0.488-1-1.088
                                                        V16h22v1.912 C23,18.512,22.551,19,22,19z"></path>
                                                    <path d="M12,16.5c-0.551,0-1,0.448-1,1
                                                        s0.449,1,1,1s1-0.448,1-1S12.551,
                                                        16.5,12,16.5z M12,17.5L12,17.5h0.5H12z"></path>
                                                    <g><path fill="none" d="M0 0H24V24H0z"></path></g>
                                                </svg>
                                                <span class="dcf-sr-only">Virtual Location:</span>
                                                <div class="dcf-popup" data-point="true">
                                                    <button
                                                        class="dcf-btn dcf-btn-tertiary dcf-btn-popup"
                                                        type="button"
                                                    >
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
                                            </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            ...and <?php echo (count($datetimes) - 3); ?> more
                                        <?php break; ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                                <div class="small-block dcf-d-none">
                                    <?php if ($status = $event->getStatusWithCalendar($context->calendar->getRawObject())): ?>
                                        <strong><?php echo ucwords($status); ?></strong> on <?php echo $context->calendar->name ?>
                                    <?php else: ?>
                                        <select
                                            id="event-action-<?php echo $event->id ?>-small"
                                            class="searched-event-tools"
                                            data-id="<?php echo $event->id; ?>"
                                            >
                                                <option value="">Select an Action</option>
                                                <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_UPCOMING_ID, $context->calendar->id)): ?>
                                                    <option value="move-to-upcoming">Move to Upcoming</option>
                                                <?php endif; ?>
                                                <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_PENDING_ID, $context->calendar->id)): ?>
                                                    <option value="move-to-pending">Move to Pending</option>
                                                <?php endif; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="small-hidden">
                                <?php if ($status = $event->getStatusWithCalendar($context->calendar->getRawObject())): ?>
                                    <strong><?php echo ucwords($status); ?></strong> on <?php echo $context->calendar->name ?>
                                <?php else: ?>
                                    <select
                                        id="event-action-<?php echo $event->id ?>"
                                        class="dcf-input-select dcf-txt-md searched-event-tools"
                                        data-id="<?php echo $event->id; ?>"
                                        aria-label="Event Move Options"
                                    >
                                      <option value="">Select an Action</option>
                                      <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_UPCOMING_ID, $context->calendar->id)): ?>
                                        <option value="move-to-upcoming">Move to Upcoming</option>
                                      <?php endif; ?>
                                      <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_MOVE_TO_PENDING_ID, $context->calendar->id)): ?>
                                        <option value="move-to-pending">Move to Pending</option>
                                       <?php endif; ?>
                                      </select>
                                    <form id="move-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getMoveURL($context->calendar) ?>" class="dcf-form delete-form dcf-d-none">
                                        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                                        <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                                        <input class="dcf-input-text" type="text" name="new_status" id="move-target-<?php echo $event->id; ?>">
                                        <input class="dcf-input-text" type="text" name="event_id" value="<?php echo $event->id ?>">
                                        <input type="hidden" name="source" value="search">
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="dcf-mt-2 dcf-txt-center">
                <div style="display: inline-block;">
                    <nav class="dcf-pagination">
                        <ol class="dcf-list-bare dcf-list-inline" role="list">
                        <?php if($context->page != 1): ?>
                            <li><a class="dcf-pagination-prev unl-prerender" href="?event_type_id=<?php echo $context->event_type_id ?>&search_term=<?php echo $context->search_term?>&amp;page=<?php echo $context->page - 1 ?>">Prev</a></li>
                        <?php endif; ?>
                        <?php $before_ellipsis_shown = FALSE; $after_ellipsis_shown = FALSE; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == $context->page): ?>
                                    <li><span class="dcf-pagination-selected"><?php echo $i; ?></span></li>
                                <?php elseif ($i <= 3 || $i >= $total_pages - 2 || $i == $context->page - 1 ||
                                    $i == $context->page - 2 || $i == $context->page + 1 || $i == $context->page + 2): ?>
                                    <li><a class="unl-prerender" href="?event_type_id=<?php echo $context->event_type_id ?>&search_term=<?php echo $context->search_term?>&amp;page=<?php echo $i ?>"><?php echo $i; ?></a></li>
                                <?php elseif ($i < $context->page && !$before_ellipsis_shown): ?>
                                    <li><span class="dcf-pagination-ellipsis">...</span></li>
                                    <?php $before_ellipsis_shown = TRUE; ?>
                                <?php elseif ($i > $context->page && !$after_ellipsis_shown): ?>
                                    <li><span class="dcf-pagination-ellipsis">...</span></li>
                                    <?php $after_ellipsis_shown = TRUE; ?>
                                <?php endif; ?>
                        <?php endfor; ?>
                        <?php if($context->page != $total_pages): ?>
                            <li><a class="dcf-pagination-next unl-prerender" href="?event_type_id=<?php echo $context->event_type_id ?>&search_term=<?php echo $context->search_term?>&amp;page=<?php echo $context->page + 1 ?>">Next</a></li>
                        <?php endif; ?>
                        </ol>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
