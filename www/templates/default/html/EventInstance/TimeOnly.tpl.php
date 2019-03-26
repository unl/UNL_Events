<?php
$starttime = $context->getStartTime();
$endtime = $context->getEndTime();

if (empty($timezoneDisplay) || empty($timezoneDisplay->getTimezone())) {
    // set with default calendar timezone
    $timezoneDisplay = new \UNL\UCBCN\TimezoneDisplay($context->calendar->defaulttimezone, FALSE);
}
?>
<span class="time-wrapper">
<?php
    if (!$context->isAllDay()) {
        if (intval($timezoneDisplay->format($starttime, $context->eventdatetime->timezone,'i')) == 0) {
            echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone,'g a');
        } else {
            echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone,'g:i a');
        }

        if (!empty($endtime) && $endtime != $starttime) {
            echo '&ndash;';
            if (intval($timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'i')) == 0) {
                echo $timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'g a');
            } else {
                echo $timezoneDisplay->format($endtime, $context->eventdatetime->timezone,'g:i a');
            }
        }

        if ($timezoneDisplay->isClientTime() === FALSE && $context->eventdatetime->timezone != $context->calendar->defaulttimezone) {
          echo $timezoneDisplay->format($starttime, $context->eventdatetime->timezone,' T');
        }
    }
?>
</span>
