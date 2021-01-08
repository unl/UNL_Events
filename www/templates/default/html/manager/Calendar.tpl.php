<?php
    const ARIA_SELECTED = 'aria-selected="true"';
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
                    <form id="bulk-action-form" method="POST" action="<?php echo $context->calendar->getBulkMoveActionURL() ?>" class="delete-form dcf-d-none">
                      <input type="text" title="Bulk Action IDs" id="bulk-action-ids" name="ids">
                      <input type="text" title="Bulk Action Action" id="bulk-action-action" name="action">
                      <input type="text" name="status" value="<?php echo $context->tab ?>">
                      <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                      <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                      <button type="submit">Submit</button>
                    </form>
                </div>
                <div class="event-page">
                    <table class="event-list">
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
                                                    <?php if (!empty($location)) echo $location->name; ?>
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

                                            <?php if ($context->calendar->id == UNL\UCBCN::$main_calendar_id && $context->tab != 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_ID, $context->calendar->id)): ?>
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

                                          <?php if ($context->calendar->id == UNL\UCBCN::$main_calendar_id && $context->tab != 'pending' && $user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_ID, $context->calendar->id)): ?>
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
                                        <form id="move-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getMoveURL($controller->getCalendar()) ?>" class="delete-form dcf-d-none">
                                          <input type="text" title="New Status" name="new_status" id="move-target-<?php echo $event->id; ?>">
                                          <input type="text" title="Event ID" name="event_id" value="<?php echo $event->id ?>">
                                          <input type="text" name="status" value="<?php echo $context->tab ?>">
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                                          <button type="submit">Submit</button>
                                        </form>
                                        <form id="delete-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getDeleteURL($controller->getCalendar()) ?>" class="delete-form dcf-d-none">
                                          <input type="text" name="status" value="<?php echo $context->tab ?>">
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                                          <button type="submit">Submit</button>
                                        </form>
                                        <form id="promote-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getPromoteURL($controller->getCalendar()) ?>" class="delete-form dcf-d-none">
                                          <input type="text" id="promote-target-<?php echo $event->id; ?>" name="status" value="promote">
                                          <input type="text" title="Event ID" name="event_id" value="<?php echo $event->id ?>">
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                                          <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
                                          <button type="submit">Submit</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php } catch (\OutOfBoundsException $e) {} ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                    <?php
                    $page->addScriptDeclaration("WDN.loadCSS('https://unlcms.unl.edu/wdn/templates_4.1/css/modules/pagination.css');");
                    ?>
                    <div class="dcf-mt-2" style="text-align: center;">
                        <div style="display: inline-block;">
                            <ul id="pending-pagination" class="wdn_pagination" data-tab="pending" style="padding-left: 0;">
                                <?php if($context->page != 1): ?>
                                    <li class="arrow prev"><a href="?tab=<?php echo $context->tab?>&amp;page=<?php echo $context->page - 1 ?>" title="Go to the previous page">← prev</a></li>
                                <?php endif; ?>
                                <?php $before_ellipsis_shown = FALSE; $after_ellipsis_shown = FALSE; ?>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <?php if ($i == $context->page): ?>
                                            <li class="selected"><span><?php echo $i; ?></span></li>
                                        <?php elseif ($i <= 3 || $i >= $total_pages - 2 || $i == $context->page - 1 ||
                                                    $i == $context->page - 2 || $i == $context->page + 1 || $i == $context->page + 2): ?>
                                            <li><a href="?tab=<?php echo $context->tab?>&amp;page=<?php echo $i ?>" title="Go to page <?php echo $i; ?>"><?php echo $i; ?></a></li>
                                        <?php elseif ($i < $context->page && !$before_ellipsis_shown): ?>
                                            <li><span class="ellipsis">...</span></li>
                                            <?php $before_ellipsis_shown = TRUE; ?>
                                        <?php elseif ($i > $context->page && !$after_ellipsis_shown): ?>
                                            <li><span class="ellipsis">...</span></li>
                                            <?php $after_ellipsis_shown = TRUE; ?>
                                        <?php endif; ?>
                                <?php endfor; ?>
                                <?php if($context->page != $total_pages): ?>
                                    <li class="arrow next"><a href="?tab=<?php echo $context->tab?>&amp;page=<?php echo $context->page + 1 ?>" title="Go to the next page">next →</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$page->addScriptDeclaration("WDN.loadCSS('https://wdn.unl.edu/wdn/templates_5.2/js/js-css/tabs.css');");
?>
