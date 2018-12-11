<?php
$prev = $context->getDateTime()->modify('-1 month');
$next = $context->getDateTime()->modify('+1 month');
?>
<div class="month_cal dcf-pb-8" id="month_viewcal">
    <table class="wp-calendar wdn_responsive_table">
        <caption>
            <span><a href="<?php echo $context->getPreviousMonthURL(); ?>" id="prev_month"><span class="eventicon-angle-circled-left" aria-hidden="true"></span><span class="dcf-sr-only">View events for <?php echo $prev->format('F'); ?></span></a></span>
            <span class="monthvalue">
                <a href="<?php echo $context->getURL(); ?>"><?php echo $context->getDateTime()->format('F'); ?></a>
            </span>
            <span class="yearvalue">
                <a href="<?php echo $context->getYearURL(); ?>"><?php echo $context->getDateTime()->format('Y'); ?></a>
            </span>
            <span><a href="<?php echo $context->getNextMonthURL(); ?>" id="next_month"><span class="eventicon-angle-circled-right" aria-hidden="true"></span><span class="dcf-sr-only">View events for <?php echo $next->format('F'); ?></span></a></span>
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
        <?php
        $first = true;
        $month = $context->getRawObject();
        foreach ($month as $i => $day) {
            if (UNL\UCBCN\Frontend\Month::$weekday_start == $day->getDateTime()->format('l')) {
                // Start of a new week, so start a new table row
                if (!$first) {
                    echo '</tr>';
                    $first = false;
                }
                echo '<tr>';
            }

            $classes = array('total-events-' . count($day));
            $day_timestamp = $day->getDateTime()->modify('first day of this month')->format('U');
            $current_timestamp = $context->getDateTime('first day of this month')->format('U');
            if ($day_timestamp < $current_timestamp) {
                $classes[] = 'prev';
            } else if ($day_timestamp > $current_timestamp) {
                $classes[] = 'next';
            } else {
                $classes[] = 'selected';
            }
            ?>
            <td data-header="<?php echo $weekdayKeys[$i % $weekdaysCount] ?>" class="<?php echo implode(' ', $classes); ?>">
                <?php echo $savvy->render($day, 'EventListing/Month.tpl.php'); ?>
            </td>
            <?php
        }
        echo '</tr>';
        ?>
        </tbody>
    </table>
</div>