<?php
$prev = $context->getDateTime()->modify('-1 week');
$next = $context->getDateTime()->modify('+1 week');
?>
<div class="week_cal dcf-pb-8" id="month_viewcal">
    <table class="wp-calendar wdn_responsive_table">
        <caption>
            <span><a href="<?php echo $context->getPreviousURL(); ?>" id="prev_week"><span class="eventicon-angle-circled-left" aria-hidden="true"></span><span class="dcf-sr-only">View events for week <?php echo $prev->format('W'); ?></span></a></span>
            <span class="monthvalue">
                <a href="<?php echo $context->getURL(); ?>"><span class="week-number">Week <?php echo $context->getDateTime()->format('W'); ?>:</span> <?php echo $context->getDateTime()->format('F'); ?></a>
            </span>
            <span class="yearvalue">
                <a href="<?php echo $context->getYearURL(); ?>"><?php echo $context->getDateTime()->format('Y'); ?></a>
            </span>
            <span><a href="<?php echo $context->getNextURL(); ?>" id="next_week"><span class="eventicon-angle-circled-right" aria-hidden="true"></span><span class="dcf-sr-only">View events for week <?php echo $next->format('W'); ?></span></a></span>
        </caption>
        <thead>
        <?php
        $weekdays = array(
            'Sunday' => 'Sun',
            'Monday' => 'Mon',
            'Tuesday' => 'Tue',
            'Wednesday' => 'Wed',
            'Thursday' => 'Thu',
            'Friday' => 'Fri',
            'Saturday' => 'Sat',
        );
        $weekdaysCount = count($weekdays);
        $weekdayKeys = array_keys($weekdays);
        ?>
        <tr>
            <?php foreach ($weekdays as $full => $short): ?>
                <th scope="col" title="<?php echo $full; ?>"><?php echo $short; ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
        <?php
        $week = $context->getRawObject();
        foreach ($week as $i => $day): 
        ?>
            <td data-header="<?php echo $weekdayKeys[$i % $weekdaysCount] ?>">
                <?php echo $savvy->render($day, 'EventListing/Month.tpl.php'); ?>
            </td>
        <?php endforeach; ?>
        </tr>
        </tbody>
    </table>
</div>

