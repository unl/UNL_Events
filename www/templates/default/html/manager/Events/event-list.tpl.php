<select id="bulk-action" class="bulk-upcoming-event-tools">
    <option value="">Bulk Actions</option>
    <option value="move-to-pending">Move to Pending</option>
    <option value="delete">Delete</option>
</select>

<?php $arrays = array_chunk($context->getIDs()->getRawObject()->getArrayCopy(), 10); ?>
<?php foreach ($arrays as $page => $events): ?>
<div class="event-page" data-page-num="<?php echo $page+1 ?>" <?php if ($page+1 > 1) echo 'style="display: none;"' ?>>
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
            <?php foreach($events as $event): ?>
                <?php $event = \UNL\UCBCN\Event::getByID($event); ?>
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
                                <?php else : ?>
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
                                <option value="move-to-pending">Move to Pending</option>
                                <option value="recommend">Recommend</option>
                                <option value="delete">Delete</option>
                        </select>
                        <form id="delete-<?php echo $event->id; ?>" method="POST" action="<?php echo $event->getDeleteURL($controller->getCalendar()) ?>" class="delete-form hidden"></form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>

<script type="text/javascript">
WDN.loadCSS(WDN.getTemplateFilePath('css/modules/pagination.css'));
</script>
<div style="text-align: center;">
    <div style="display: inline-block;">
        <ul id="pending-pagination" class="wdn_pagination" data-tab="pending" style="padding-left: 0;">
            <li class="arrow prev"><a href="#" title="Go to the previous page">← prev</a></li>
            <?php for ($i = 1; $i <= count($arrays); $i++): ?>
                <li class="<?php if ($i==$context->page) echo 'selected';?>" data-page="<?php echo $i ?>">
                    <?php if ($i <= 3 || $i >= count($arrays) - 2 || $i == $context->page - 1 || 
                            $i == $context->page - 2 || $i == $context->page + 1 || $i == $context->page + 2): ?>
                    <a style="display: none;" data-page="<?php echo $i ?>" class="link" href="#" title="Go to page <?php echo $i; ?>"><?php echo $i; ?></a>
                    <span style="display: none;" class="number-text"><?php echo $i; ?></span>
                    <span class="ellipsis" style="display: none;">...</span>
                </li>
            <?php endfor; ?>
            <li class="arrow next"><a href="#" title="Go to the next page">next →</a></li>
        </ul>
    </div>
</div>