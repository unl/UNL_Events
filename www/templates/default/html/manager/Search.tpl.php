<?php
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
                                    <a href="<?php echo $event->getEditURL() ?>"><?php echo $event->title; ?></a>
                                <?php else: ?>
                                    <a href="<?php echo $event->getViewURL($controller->getCalendar()) ?>">
                                        <?php echo $event->title; ?>
                                    </a> (preview)
                                <?php endif; ?>
                            </td>
                            <td class="small-hidden">
                                <?php $calendar = $event->getOriginCalendar() ?>
                                <?php if ($calendar): ?>
                                    <a href="<?php echo $calendar->getFrontendURL() ?>"><?php echo $calendar->name ?></a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small-block dcf-d-none calendar-event-title">
                                    <?php if ($event->userCanEdit()): ?>
                                        <a href="<?php echo $event->getEditURL() ?>">
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
                                        <?php if (++$count <= 3) : ?>
                                            <?php
                                            {
                                                if ($datetime->recurringtype == 'none') {
                                                    echo date('n/d/y @ g:ia', strtotime($datetime->starttime));
                                                } else if ($datetime->recurringtype == 'daily' || $datetime->recurringtype == 'weekly' || $datetime->recurringtype == 'biweekly' ||
                                                        $datetime->recurringtype == 'annually') {
                                                    echo ucwords($datetime->recurringtype) . ' @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                                        ':<br>' . date('n/d/y', strtotime($datetime->starttime)) .
                                                        ' - ' . date('n/d/y', strtotime($datetime->recurs_until));
                                                } else if ($datetime->recurringtype == 'monthly') {
                                                    if ($datetime->rectypemonth == 'lastday') {
                                                        echo 'Last day of every month @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                                            ':<br>' . date('n/d/y', strtotime($datetime->starttime)) .
                                                            ' - ' . date('n/d/y', strtotime($datetime->recurs_until));
                                                    } else if ($datetime->rectypemonth == 'date') {
                                                        echo ordinal(date('d', strtotime($datetime->starttime))) .
                                                            ' of every month @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                                            ':<br>' . date('n/d/y', strtotime($datetime->starttime)) .
                                                            ' - ' . date('n/d/y', strtotime($datetime->recurs_until));
                                                    } else {
                                                        echo ucwords($datetime->rectypemonth) . date(' l', strtotime($datetime->starttime)) . ' of every month' .
                                                            ':<br>' . date('n/d/y', strtotime($datetime->starttime)) .
                                                            ' - ' . date('n/d/y', strtotime($datetime->recurs_until));
                                                    }
                                                }
                                            }
                                            ?><br>
                                            <?php $location = $datetime->getLocation(); ?>
                                            <?php if ($location !== false) { echo $location->name; } ?>
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
            <?php $page->addScriptDeclaration("WDN.initializePlugin('pagination');"); ?>
            <div class="dcf-mt-2 dcf-txt-center">
                <div style="display: inline-block;">
                    <nav class="dcf-pagination">
                        <ol class="dcf-list-bare dcf-list-inline">
                        <?php if($context->page != 1): ?>
                            <li><a class="dcf-pagination-prev" href="?event_type_id=<?php echo $context->event_type_id ?>&search_term=<?php echo $context->search_term?>&amp;page=<?php echo $context->page - 1 ?>">Prev</a></li>
                        <?php endif; ?>
                        <?php $before_ellipsis_shown = FALSE; $after_ellipsis_shown = FALSE; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == $context->page): ?>
                                    <li><span class="dcf-pagination-selected"><?php echo $i; ?></span></li>
                                <?php elseif ($i <= 3 || $i >= $total_pages - 2 || $i == $context->page - 1 ||
                                    $i == $context->page - 2 || $i == $context->page + 1 || $i == $context->page + 2): ?>
                                    <li><a href="?event_type_id=<?php echo $context->event_type_id ?>&search_term=<?php echo $context->search_term?>&amp;page=<?php echo $i ?>"><?php echo $i; ?></a></li>
                                <?php elseif ($i < $context->page && !$before_ellipsis_shown): ?>
                                    <li><span class="dcf-pagination-ellipsis">...</span></li>
                                    <?php $before_ellipsis_shown = TRUE; ?>
                                <?php elseif ($i > $context->page && !$after_ellipsis_shown): ?>
                                    <li><span class="dcf-pagination-ellipsis">...</span></li>
                                    <?php $after_ellipsis_shown = TRUE; ?>
                                <?php endif; ?>
                        <?php endfor; ?>
                        <?php if($context->page != $total_pages): ?>
                            <li><a class="dcf-pagination-next" href="?event_type_id=<?php echo $context->event_type_id ?>&search_term=<?php echo $context->search_term?>&amp;page=<?php echo $context->page + 1 ?>">Next</a></li>
                        <?php endif; ?>
                        </ol>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
