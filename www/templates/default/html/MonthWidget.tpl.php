<?php
$prev = $context->getDateTime()->modify('-1 month');
$next = $context->getDateTime()->modify('+1 month');
?>
<div class="monthwidget">
    <table class="wp-calendar dcf-table dcf-table-fixed dcf-table-bordered dcf-txt-sm dcf-w-100% unl-font-sans" data-datetime="<?php echo $context->getDateTime()->format('c') ?>">
        <caption class="dcf-pb-0 dcf-txt-sm dcf-regular unl-ls-0 unl-bg-darker-gray">
            <span class="dcf-d-flex dcf-flex-nowrap dcf-ai-center">
                <a class="dcf-p-4 unl-cream prev" href="<?php echo $context->getPreviousMonthURL(); ?>" aria-label="View events for <?php echo $prev->format('F'); ?>"><svg class="dcf-d-block dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M12 .004c-6.617 0-12 5.383-12 12s5.383 12 12 12 12-5.383 12-12-5.384-12-12-12zm3 8.5a.5.5 0 01-.193.395l-3.993 3.105 3.993 3.106c.122.094.193.24.193.394v4a.5.5 0 01-.82.384l-9-7.5a.499.499 0 010-.768l9-7.5a.5.5 0 01.82.384v4z"></path></svg></a>
                <span class="dcf-flex-grow-1 dcf-d-flex dcf-ai-center dcf-jc-flex-end monthvalue">
                    <a class="dcf-txt-right dcf-pt-4 dcf-pr-1 dcf-pb-4 dcf-pl-4 dcf-uppercase dcf-txt-decor-hover unl-cream" href="<?php echo $context->getURL(); ?>"><?php echo $context->getDateTime()->format('F'); ?></a>
                </span>
                <span class="dcf-flex-grow-1 dcf-d-flex dcf-ai-center yearvalue">
                    <a class="dcf-txt-left dcf-pt-4 dcf-pr-4 dcf-pb-4 dcf-txt-decor-hover unl-cream" href="<?php echo $context->getYearURL(); ?>"><?php echo $context->getDateTime()->format('Y'); ?></a>
                </span>
                <a class="dcf-p-4 unl-cream next" href="<?php echo $context->getNextMonthURL(); ?>" aria-label="View events for <?php echo $next->format('F'); ?>"><svg class="dcf-d-block dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M12 .004c-6.617 0-12 5.383-12 12s5.383 12 12 12 12-5.383 12-12-5.384-12-12-12zm6.82 12.384l-8.999 7.5a.498.498 0 01-.532.069.5.5 0 01-.289-.453v-4c0-.154.071-.3.193-.394l3.992-3.106-3.992-3.106A.497.497 0 019 8.504v-4a.5.5 0 01.82-.384l8.999 7.5a.499.499 0 01.001.768z"></path></svg></a>
            </span>
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
                <th class="dcf-pt-3 dcf-pr-0 dcf-pb-3 dcf-pl-0 dcf-txt-center dcf-txt-sm dcf-regular dcf-uppercase dcf-b-1 dcf-b-solid unl-b-light-gray unl-bg-lightest-gray" scope="col" title="<?php echo $full; ?>"><?php echo $short; ?></th>
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
                $class = 'dcf-txt-xs unl-bg-lightest-gray prev';
            } elseif ($day_timestamp > $current_timestamp) {
                $class = 'dcf-txt-xs unl-bg-lightest-gray next';
            }
            echo '<td class="dcf-p-0 dcf-txt-center dcf-txt-middle dcf-b-1 dcf-b-solid unl-b-light-gray '.$class.'">';

            $d = $datetime->format('j');
            if (isset($context->data[$datetime->format('Y-m-d')])) {
                echo '<a class="dcf-txt-decor-none" href="' . $context->getDayURL($datetime) . '" aria-label="'.$datetime->format('F j').'">' . $d . '</a>';
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