<?php
$classes = array('date-wrapper');
if ($context->isAllDay()) {
    $classes[] = 'all-day';
}

$starttime = $context->getStartTime();
$endtime = $context->getEndTime();
if (empty($timezoneDisplay) || empty($timezoneDisplay->getTimezone())) {
  // set with default calendar timezone
  $timezoneDisplay = new \UNL\UCBCN\TimezoneDisplay($context->calendar->defaulttimezone, FALSE);
}
?>
<span class="date-wrapper">
    <span class="eventicon-calendar-empty" aria-hidden="true"></span><span class="dcf-sr-only">Date:</span>
    <?php if (!empty($starttime)): ?>
        <time class="dtstart" datetime="<?php echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone, 'c') ?>"><?php echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone,'M. j, Y') ?></time>
    <?php endif; ?>
    <?php if (!empty($endtime) && $context->isOngoing()): ?>&ndash; <time class="dtend" datetime="<?php echo $timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'c') ?>"><?php echo $timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'M. j, Y')?></time>
    <?php endif; ?>
</span>
<span class="time-wrapper">
    <span class="eventicon-clock" aria-hidden="true"></span><span class="dcf-sr-only">Time:</span>
    <?php if ($context->isAllDay()): ?>
    All Day
    <?php else: ?>
        <?php echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone, 'g:i a')?><?php if (!empty($endtime) && $endtime != $starttime): ?>&ndash;<?php echo $timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'g:i a')?><?php endif; ?>
        <?php if ($timezoneDisplay->isClientTime() === FALSE && $context->eventdatetime->timezone != $context->calendar->defaulttimezone) { echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone, ' T'); } ?>
    <?php endif; ?>
</span>
