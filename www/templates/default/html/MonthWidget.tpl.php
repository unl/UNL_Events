<?php
$prev = $context->getDateTime()->modify('-1 month');
$next = $context->getDateTime()->modify('+1 month');
?>
<div class="monthwidget">
    <table class="wp-calendar" data-datetime="<?php echo $context->getDateTime()->format('c') ?>">
        <caption>
            <span class="prev"><a href="<?php echo $context->getPreviousMonthURL(); ?>"><span class="eventicon-angle-circled-left" aria-hidden="true"></span><span class="dcf-sr-only">View events for <?php echo $prev->format('F'); ?></span></a></span>
            <span class="monthvalue">
                <a href="<?php echo $context->getURL(); ?>"><?php echo $context->getDateTime()->format('F'); ?></a>
            </span>
            <span class="yearvalue">
                <a href="<?php echo $context->getYearURL(); ?>"><?php echo $context->getDateTime()->format('Y'); ?></a>
            </span>
            <span class="next"><a href="<?php echo $context->getNextMonthURL(); ?>"><span class="eventicon-angle-circled-right" aria-hidden="true"></span><span class="dcf-sr-only">View events for <?php echo $next->format('F'); ?></span></a></span>
    
        </caption>
        <thead>
        <?php
        $weekdays = array(
            'Sunday'    => 'Sun',
            'Monday'    => 'Mon',
            'Tuesday'   => 'Tue',
            'Wednesday' => 'Wed',
            'Thursday'  => 'Thu',
            'Friday'    => 'Fri',
            'Saturday'  => 'Sat',
        );
        ?>
        <tr>
            <?php foreach ($weekdays as $full=>$short): ?>
                <th scope="col" title="<?php echo $full; ?>"><?php echo $short; ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php
        $first = true;
        $month = $context->getRawObject();
        foreach ($month as $datetime) {
            if (UNL\UCBCN\Frontend\Month::$weekday_start == $datetime->format('l')) {
                // Start of a new week, so start a new table row
                if (!$first) {
                    echo '</tr>';
                    $first = false;
                }
                echo '<tr>';
            }
            
            //Get the class.
            $datetime_cone = clone $datetime;  //We need to clone so that the $datetime object is not modified.
            $day_timestamp = $datetime_cone->modify('first day of this month')->format('U');
            $current_timestamp = $context->getDateTime()->modify('first day of this month')->format('U');
            $class = 'selected';
            if ($day_timestamp < $current_timestamp) {
                $class = 'prev';
            } elseif ($day_timestamp  > $current_timestamp) {
                $class = 'next';
            }
            echo '<td class="'.$class.'">';

            $d = $datetime->format('j');
            if (isset($context->data[$datetime->format('Y-m-d')])) {
                echo '<a href="' . $context->getDayURL($datetime) . '" aria-label="'.$datetime->format('F j').'">' . $d . '</a>';
            } else {
                echo '<span>' . $d . '</span>';
            }
    
            echo '</td>';
        }
        echo '</tr>';
        ?>
        </tbody>
    </table>
</div>