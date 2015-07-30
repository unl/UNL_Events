<?php 
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
<form id="search-form" action="/manager/<?php echo $context->calendar->shortname; ?>/search" style="display: none;">
    <label for="events-search">Search</label>
    <div>
        <div style="float: right; padding-top: 3px;">
            <button type="submit" class="wdn-button wdn-button-triad">Search</button>
        </div>
        <div style="margin-right: 100px;">
            <input type="text" name="search_term" id="events-search"  />
        </div>
    </div>
</form>
<br>
<ul class="wdn_tabs">
    <li class="<?php if ($context->tab == 'pending') echo 'selected'; ?>"><a href="<?php echo ($context->tab == 'pending' ? '#pending' : '?tab=pending'); ?>">Pending (<?php echo count($categorized_events['pending']); ?>)</a></li>
    <li class="<?php if ($context->tab == 'upcoming') echo 'selected'; ?>"><a href="<?php echo ($context->tab == 'upcoming' ? '#upcoming' : '?tab=upcoming'); ?>">Upcoming (<?php echo count($categorized_events['posted']); ?>)</a></li>
    <li class="<?php if ($context->tab == 'past') echo 'selected'; ?>"><a href="<?php echo ($context->tab == 'past' ? '#past' : '?tab=past'); ?>">Past (<?php echo count($categorized_events['archived']); ?>)</a></li>
</ul>
<div class="wdn_tabs_content">
    <div id="<?php echo $context->tab; ?>">
        <?php if (count($events) == 0): ?>
            There are no <?php echo $context->tab ?> events.
        <?php else: ?>
            <div class="medium-hidden">
                <select id="bulk-action" title="Bulk Action" class="bulk-<?php echo $context->tab; ?>-event-tools">
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
                <form id="bulk-action-form" method="POST" action="<?php echo $context->calendar->getBulkActionURL() ?>" class="delete-form hidden">
                <input type="text" title="Bulk Action IDs" id="bulk-action-ids" name="ids">
                <input type="text" title="Bulk Action Action" id="bulk-action-action" name="action">
                <button type="submit">Submit</button>
                </form>
            </div><br class="medium-hidden">
            <div class="medium-hidden" style="margin-bottom: 5px;">
                <a href='#' class="wdn-button small-button" id="check-all">Check All</a>
                <a href='#' class="wdn-button small-button" id="uncheck-all">Uncheck All</a>
            </div>
            <div class="event-page">
                <table class="event-list">
                    <thead class="small-hidden">
                        <tr>
                            <th class="center medium-hidden">Select</th>
                            <th>Title</th>
                            <th>Date/Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($events as $event): ?>
                            <tr>
                                <td class="center medium-hidden">
                                    <input type="checkbox" id="select-event-<?php echo $event->id ?>" title="Select Event" class="select-event" data-id="<?php echo $event->id; ?>">
                                </td>
                                <td class="small-hidden">
                                    <?php if ($event->userCanEdit()): ?>
                                        <a href="<?php echo $event->getEditURL($controller->getCalendar()) ?>">
                                        <?php echo $event->title; ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo $event->title; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small-block hidden calendar-event-title">
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
                                        class="<?php echo $context->tab ?>-event-tools small-block hidden"
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

                                            <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_RECOMMEND_ID, $context->calendar->id)): ?>
                                                <option value="recommend">Recommend</option>
                                            <?php endif; ?>

                                            <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_DELETE_ID, $context->calendar->id)): ?>
                                                <option value="delete">Delete</option>
                                            <?php endif; ?>
                                    </select>
                                    <br class="small-block hidden">
                                </td>
                                <td class="small-hidden">
                                    <select 
                                        id="event-action-<?php echo $event->id ?>"
                                        class="<?php echo $context->tab ?>-event-tools"
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

                                            <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_RECOMMEND_ID, $context->calendar->id)): ?>
                                                <option value="recommend">Recommend</option>
                                            <?php endif; ?>

                                            <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_DELETE_ID, $context->calendar->id)): ?>
                                                <option value="delete">Delete</option>
                                            <?php endif; ?>
                                    </select>
                                    <form id="move-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getMoveURL($controller->getCalendar()) ?>" class="delete-form hidden">
                                    <input type="text" title="New Status" name="new_status" id="move-target-<?php echo $event->id; ?>">
                                    <input type="text" title="Event ID" name="event_id" value="<?php echo $event->id ?>">
                                    <button type="submit">Submit</button>
                                    </form>
                                    <form id="delete-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getDeleteURL($controller->getCalendar()) ?>" class="delete-form hidden">
                                    <button type="submit">Submit</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <script type="text/javascript">
                WDN.loadCSS(WDN.getTemplateFilePath('css/modules/pagination.css'));
                </script>
                <div style="text-align: center;">
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
