<?php
	$total_pages = ceil(count($context->events) / 10);
?>

<form>
    <label for="events_search">Search</label>
    <input type="text" name="search_term" id="events_search" value="<?php echo $context->search_term ?>" />
</form>

<div>
    <?php if (count($context->events) == 0): ?>
        There are no results.
    <?php else: ?>
        <div class="event-page">
            <table class="event-list">
                <thead>
                    <tr>
                        <th class="center">Select</th>
                        <th>Title</th>
                        <th>Date/Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($context->events as $event): ?>
                        <tr>
                            <td class="center">
                                <input type="checkbox" id="select-event-<?php echo $event->id ?>" class="select-event" data-id="<?php echo $event->id; ?>">
                            </td>
                            <td>
                                <a href="<?php echo $event->getEditURL($controller->getCalendar()) ?>"><?php echo $event->title; ?></a>
                            </td>
                            <td>
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
                                                } else if ($datetime->recurringtype == 'daily' || $datetime->recurringtype == 'weekly' ||
                                                        $datetime->recurringtype == 'annually') {
                                                    echo ucwords($datetime->recurringtype) . ' @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                                        ': ' . date('n/d/y', strtotime($datetime->starttime)) . 
                                                        ' - ' . date('n/d/y', strtotime($datetime->recurs_until));
                                                } else if ($datetime->recurringtype == 'monthly') {
                                                    if ($datetime->rectypemonth == 'lastday') {
                                                        echo 'Last day of each month @ ' . date('g:ia', strtotime($datetime->starttime)) . 
                                                            ': ' . date('n/d/y', strtotime($datetime->starttime)) . 
                                                            ' - ' . date('n/d/y', strtotime($datetime->recurs_until));
                                                    } else if ($datetime->rectypemonth == 'date') {
                                                        echo ordinal(date('d', strtotime($datetime->starttime))) . 
                                                            ' of each month @ ' . date('g:ia', strtotime($datetime->starttime)) . 
                                                            ': ' . date('n/d/y', strtotime($datetime->starttime)) . 
                                                            ' - ' . date('n/d/y', strtotime($datetime->recurs_until));
                                                    } else {
                                                        echo ucwords($datetime->rectypemonth) . date('f', strtotime($datetime->starttime)) . ' of every month' . 
                                                            ': ' . date('n/d/y', strtotime($datetime->starttime)) . 
                                                            ' - ' . date('n/d/y', strtotime($datetime->recurs_until));
                                                    }
                                                }
                                            }
                                            ?><br>
                                            <?php if (!empty($location = $datetime->getLocation())) echo $location->name; ?>
                                        <?php else: ?>
                                            ...and <?php echo (count($datetimes) - 3); ?> more
                                        <?php break; ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            </td>
                            <td>
                                <select 
                                    id="event-action-<?php echo $event->id ?>"
                                    class="upcoming-event-tools" 
                                    data-id="<?php echo $event->id; ?>"
                                    data-recommend-url="<?php echo $event->getRecommendURL($controller->getCalendar()) ?>"
                                    >
                                        <option value="">Select an Action</option>
                                </select>
                                <form id="delete-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getDeleteURL($controller->getCalendar()) ?>" class="delete-form hidden"></form>
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
