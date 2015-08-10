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
<form>
    <label for="events-search">Search</label>
    <div>
        <div style="float: right; padding-top: 3px;">
            <button type="submit" class="wdn-button wdn-button-triad">Search</button>
        </div>
        <div style="margin-right: 100px;">
            <input type="text" name="search_term" id="events-search" value="<?php echo $context->search_term ?>" />
        </div>
    </div>
</form>

<div>
    <?php if (count($context->events) == 0): ?>
        There are no results.
    <?php else: ?>
        <div class="event-page">
            <table class="event-list">
                <thead class="small-hidden">
                    <tr>
                        <th>Title</th>
                        <th>Original Calendar</th>
                        <th>Date/Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($context->events as $event): ?>
                        <tr>
                            <td class="small-hidden">
                                <?php if ($event->userCanEdit()): ?>
                                    <a href="<?php echo $event->getEditURL() ?>"><?php echo $event->title; ?></a>
                                <?php else: ?>
                                    <?php echo $event->title; ?>
                                <?php endif; ?>
                            </td>
                            <td class="small-hidden">
                                <?php $calendar = $event->getOriginCalendar() ?>
                                <?php if ($calendar): ?>
                                    <a href="<?php echo $calendar->getFrontendURL() ?>"><?php echo $calendar->name ?></a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small-block hidden calendar-event-title">
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
                                            <?php if (!empty($location)) echo $location->name; ?>
                                            <?php else: ?>
                                                ...and <?php echo (count($datetimes) - 3); ?> more
                                            <?php break; ?>
                                            <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                                <div class="small-block hidden">
                                    <?php if ($status = $event->getStatusWithCalendar($context->calendar->getRawObject())): ?>
                                        <strong><?php echo ucwords($status); ?></strong> on <?php echo $context->calendar->name ?>
                                    <?php else: ?>
                                        <select 
                                            id="event-action-<?php echo $event->id ?>"
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
                                        <form id="move-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getMoveURL($context->calendar) ?>" class="delete-form hidden">
                                        <input type="text" name="new_status" id="move-target-<?php echo $event->id; ?>">
                                        <input type="text" name="event_id" value="<?php echo $event->id ?>">
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="small-hidden">
                                <?php if ($status = $event->getStatusWithCalendar($context->calendar->getRawObject())): ?>
                                    <strong><?php echo ucwords($status); ?></strong> on <?php echo $context->calendar->name ?>
                                <?php else: ?>
                                    <select 
                                        id="event-action-<?php echo $event->id ?>"
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
                                    <form id="move-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getMoveURL($context->calendar) ?>" class="delete-form hidden">
                                    <input type="text" name="new_status" id="move-target-<?php echo $event->id; ?>">
                                    <input type="text" name="event_id" value="<?php echo $event->id ?>">
                                    </form>
                                <?php endif; ?>
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
                            <li class="arrow prev"><a href="?search_term=<?php echo $context->search_term?>&amp;page=<?php echo $context->page - 1 ?>" title="Go to the previous page">← prev</a></li>
                        <?php endif; ?>
                        <?php $before_ellipsis_shown = FALSE; $after_ellipsis_shown = FALSE; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == $context->page): ?>
                                    <li class="selected"><span><?php echo $i; ?></span></li>
                                <?php elseif ($i <= 3 || $i >= $total_pages - 2 || $i == $context->page - 1 || 
                                            $i == $context->page - 2 || $i == $context->page + 1 || $i == $context->page + 2): ?>
                                    <li><a href="?search_term=<?php echo $context->search_term?>&amp;page=<?php echo $i ?>" title="Go to page <?php echo $i; ?>"><?php echo $i; ?></a></li>
                                <?php elseif ($i < $context->page && !$before_ellipsis_shown): ?>
                                    <li><span class="ellipsis">...</span></li>
                                    <?php $before_ellipsis_shown = TRUE; ?>
                                <?php elseif ($i > $context->page && !$after_ellipsis_shown): ?>
                                    <li><span class="ellipsis">...</span></li>
                                    <?php $after_ellipsis_shown = TRUE; ?>
                                <?php endif; ?>
                        <?php endfor; ?>
                        <?php if($context->page != $total_pages): ?>
                            <li class="arrow next"><a href="?search_term=<?php echo $context->search_term?>&amp;page=<?php echo $context->page + 1 ?>" title="Go to the next page">next →</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
