<?php
$classes = array('date-wrapper');
if ($context->isAllDay()) {
    $classes[] = 'all-day';
}

$starttime = $context->getStartTime();
$endtime = $context->getEndTime();
if (empty($timezoneDisplay) || empty($timezoneDisplay->getTimezone())) {
  // set with default calendar timezone
  $timezoneDisplay = new \UNL\UCBCN\TimezoneDisplay($context->calendar->defaulttimezone);
}
?>
<span class="date-wrapper">
    <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
        <path d="M23.5 2H20V.5c0-.3-.2-.5-.5-.5h-3c-.3 0-.5.2-.5.5V2H8V.5c0-.3-.2-.5-.5-.5h-3c-.3 0-.5.2-.5.5V2H.5c-.3 0-.5.2-.5.5v21c0 .3.2.5.5.5h23c.3 0 .5-.2.5-.5v-21c0-.3-.2-.5-.5-.5zM17 1h2v3h-2V1zM5 1h2v3H5V1zM4 3v1.5c0 .3.2.5.5.5h3c.3 0 .5-.2.5-.5V3h8v1.5c0 .3.2.5.5.5h3c.3 0 .5-.2.5-.5V3h3v4H1V3h3zM1 23V8h22v15H1z"></path>
    </svg>
    <span class="dcf-sr-only">Date:</span>
    <div>
        <?php if (!empty($starttime)): ?>
            <time 
                class="dtstart" 
                datetime="<?php echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone, 'c') ?>"
            >
                <?php if ($timezoneDisplay->format($starttime, $context->eventdatetime->timezone,'M') === "May"): ?>
                    <?php echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone,'M j, Y') ?>
                <?php else: ?>
                    <?php echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone,'M. j, Y') ?>
                <?php endif; ?>
            </time>
        <?php endif; ?>
        <?php if (!empty($endtime) && $context->isOngoing()): ?>
            &ndash; <time
                class="dtend" 
                datetime="<?php echo $timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'c') ?>"
            >
                <?php if ($timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'M') === "May"): ?>
                    <?php echo $timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'M j, Y') ?>
                <?php else: ?>
                    <?php echo $timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'M. j, Y') ?>
                <?php endif; ?>
            </time>
        <?php endif; ?>
    </div>
</span>
<span class="time-wrapper">
    <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
        <path d="M12 0C5.383 0 0 5.383 0 12s5.383 12 12 12 12-5.383 12-12S18.617 0 12 0zm0 23C5.935 23 1 18.066 1 12 1 5.935 5.935 1 12 1s11 4.935 11 11c0 6.066-4.935 11-11 11z"></path>
        <path d="M13.716 13.011A1.98 1.98 0 0014 12c0-1.103-.897-2-2-2V6.5a.5.5 0 00-1 0v3.778c-.595.347-1 .984-1 1.722 0 1.103.897 2 2 2a1.97 1.97 0 001.008-.283l4.638 4.636a.5.5 0 00.707-.707l-4.637-4.635zM11 12c0-.551.449-1 1-1s1 .449 1 1a.996.996 0 01-.288.699l-.009.006-.005.007A.993.993 0 0112 13c-.551 0-1-.449-1-1z"></path>
    </svg>
    <span class="dcf-sr-only">Time:</span>
    <div>
        <?php if ($context->isAllDay()): ?>
        All Day
        <?php else: ?>
            <?php echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone, 'g:i a')?><?php if (!empty($endtime) && $endtime != $starttime): ?>&ndash;<?php echo $timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'g:i a')?><?php endif; ?>
            <?php if ($context->eventdatetime->timezone != $context->calendar->defaulttimezone) { echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone, ' T'); } ?>
        <?php endif; ?>
    </div>
</span>
