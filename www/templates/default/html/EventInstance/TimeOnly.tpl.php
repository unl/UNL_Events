<?php
$starttime = $context->getStartTime();
$endtime = $context->getEndTime();
$timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($context->eventdatetime->timezone);
?>
<span class="time-wrapper">
<?php
    if (!$context->isAllDay()) {
        if (intval($timezoneDateTime->format($starttime,'i')) == 0) {
            echo $timezoneDateTime->format($starttime,'g a');
        } else {
            echo $timezoneDateTime->format($starttime,'g:i a');
        }

        if (!empty($endtime) && $endtime != $starttime) {
            echo '&ndash;';
            if (intval($timezoneDateTime->format($endtime,'i')) == 0) {
                echo $timezoneDateTime->format($endtime,'g a');
            } else {
                echo $timezoneDateTime->format($endtime,'g:i a');
            }
        }

        if ($context->eventdatetime->timezone != $context->calendar->defaulttimezone) {
          echo $timezoneDateTime->format($starttime,' T');
        }
    }
?>
</span>
