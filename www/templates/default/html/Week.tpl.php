<?php
$prev = $context->getDateTime()->modify('-1 week');
$next = $context->getDateTime()->modify('+1 week');
?>
<div class="week_cal dcf-pb-8" id="month_viewcal">
    <table class="wp-calendar dcf-table dcf-table-bordered dcf-table-responsive dcf-lh-3 dcf-w-100%">
        <caption class="dcf-pb-0 dcf-txt-sm dcf-regular unl-ls-0 unl-bg-darker-gray">
            <span class="dcf-d-flex dcf-flex-nowrap dcf-ai-center dcf-jc-between">
                <a class="unl-prerender dcf-flex-grow-1 dcf-txt-left dcf-p-4 unl-cream" href="<?php echo $context->getPreviousURL(); ?>" id="prev_week" aria-label="View events for week <?php echo $prev->format('W'); ?>"><svg class="dcf-d-block dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M12 .004c-6.617 0-12 5.383-12 12s5.383 12 12 12 12-5.383 12-12-5.384-12-12-12zm3 8.5a.5.5 0 01-.193.395l-3.993 3.105 3.993 3.106c.122.094.193.24.193.394v4a.5.5 0 01-.82.384l-9-7.5a.499.499 0 010-.768l9-7.5a.5.5 0 01.82.384v4z"></path></svg></a>
                <a class="unl-prerender dcf-pt-4 dcf-pr-1 dcf-pb-4 dcf-pl-4 dcf-uppercase dcf-txt-decor-hover unl-cream" href="<?php echo $context->getURL(); ?>"><span class="week-number dcf-case-reset unl-font-serif unl-lighter-gray">Week <?php echo $context->getDateTime()->format('W'); ?>:</span> <?php echo $context->getDateTime()->format('F'); ?></a>
                <a class="unl-prerender dcf-pt-4 dcf-pr-4 dcf-pb-4 dcf-txt-decor-hover unl-cream" href="<?php echo $context->getYearURL(); ?>"><?php echo $context->getDateTime()->format('Y'); ?></a>
                <a class="unl-prerender dcf-flex-grow-1 dcf-d-flex dcf-jc-flex-end dcf-p-4 unl-cream" href="<?php echo $context->getNextURL(); ?>" id="next_week" aria-label="View events for week <?php echo $next->format('W'); ?>"><svg class="dcf-d-block dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M12 .004c-6.617 0-12 5.383-12 12s5.383 12 12 12 12-5.383 12-12-5.384-12-12-12zm6.82 12.384l-8.999 7.5a.498.498 0 01-.532.069.5.5 0 01-.289-.453v-4c0-.154.071-.3.193-.394l3.992-3.106-3.992-3.106A.497.497 0 019 8.504v-4a.5.5 0 01.82-.384l8.999 7.5a.499.499 0 01.001.768z"></path></svg></a>
            </span>
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
                <th class="dcf-pt-3 dcf-pr-0 dcf-pb-3 dcf-pl-0 dcf-txt-center dcf-txt-sm dcf-regular dcf-uppercase dcf-b-1 dcf-b-solid unl-b-light-gray unl-bg-lightest-gray" scope="col" title="<?php echo $full; ?>"><?php echo $short; ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
        <?php
        $week = $context->getRawObject();
        foreach ($week as $i => $day):
        ?>
            <td class="unl-bg-cream" data-header="<?php echo $weekdayKeys[$i % $weekdaysCount] ?>">
                <?php echo $savvy->render($day, 'EventListing/Month.tpl.php'); ?>
            </td>
        <?php endforeach; ?>
        </tr>
        </tbody>
    </table>
</div>
