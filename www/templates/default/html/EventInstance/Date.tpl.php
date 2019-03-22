<?php
$classes = array('date-wrapper');
if ($context->isAllDay()) {
    $classes[] = 'all-day';
}

$starttime = $context->getStartTime();
$endtime = $context->getEndTime();
$timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($context->eventdatetime->timezone);
?>

<span class="date-wrapper">
    <span class="eventicon-calendar-empty" aria-hidden="true"></span><span class="dcf-sr-only">Date:</span>
    <?php if (!empty($starttime)): ?>
        <time class="dtstart" datetime="<?php echo $timezoneDateTime->format($starttime, 'c') ?>"><?php echo $timezoneDateTime->format($starttime,'M. j, Y') ?></time>
    <?php endif; ?>
    <?php if (!empty($endtime) && $context->isOngoing()): ?>&ndash; <time class="dtend" datetime="<?php echo $timezoneDateTime->format($endtime,'c') ?>"><?php echo $timezoneDateTime->format($endtime,'M. j, Y')?></time>
    <?php endif; ?>
</span>
<span class="time-wrapper">
    <span class="eventicon-clock" aria-hidden="true"></span><span class="dcf-sr-only">Time:</span>
    <?php if ($context->isAllDay()): ?>
    All Day
    <?php else: ?>
        <?php echo $timezoneDateTime->format($starttime,'g:i a')?><?php if (!empty($endtime) && $endtime != $starttime): ?>&ndash;<?php echo $timezoneDateTime->format($endtime,'g:i a')?><?php endif; ?>
        <?php if ($context->eventdatetime->timezone != $context->calendar->defaulttimezone) { echo $timezoneDateTime->format($starttime,' T'); } ?>
    <?php endif; ?>
</span>
