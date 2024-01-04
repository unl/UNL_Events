<?php

use UNL\UCBCN\Event\Occurrence;

$timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($context->eventdatetime->timezone);
?>
<item>
    <title><?php echo $context->event->displayTitle($context); ?></title>
    <link><?php echo $context->getURL(); ?></link>
    <description>
        <?php
        echo '&lt;div&gt;'.$context->event->description.'&lt;/div&gt;';
        if (isset($context->event->subtitle)) echo '&lt;div&gt;'.$context->event->subtitle.'&lt;/div&gt;';
        echo '&lt;div&gt;Status: ' . $context->event->icalStatus($context) . '&lt;/div&gt;';
        echo '&lt;small&gt;' . $timezoneDateTime->format($context->getStartTime(),'l, F jS') . '&lt;/small&gt;';

        if ($context->eventdatetime->isAllDay()) {
            echo ' | &lt;small&gt;&lt;abbr class="dtstart" title="' . $timezoneDateTime->format($context->getStartTime(),'c') . '"&gt;All day&lt;/abbr&gt;&lt;/small&gt;';
        } else if ($context->eventdatetime->timemode === Occurrence::TIME_MODE_TBD) {
            echo ' | &lt;small&gt;&lt;abbr class="dtstart"&gt;TBD&lt;/abbr&gt;&lt;/small&gt;';
        } else {
            if (isset($context->eventdatetime->starttime)) {
                echo ' | &lt;small&gt;&lt;abbr class="dtstart" title="' . $timezoneDateTime->format($context->getStartTime(),'c') . '"&gt;' . $timezoneDateTime->format($context->getStartTime(),'g:i: a') . '&lt;/abbr&gt;&lt;/small&gt;';
            } else {
                echo 'Unknown';
            }
            if (isset($context->eventdatetime->endtime) &&
                ($context->eventdatetime->endtime != $context->eventdatetime->starttime) &&
                ($context->eventdatetime->endtime > $context->eventdatetime->starttime)) {
                echo '-&lt;small&gt;&lt;abbr class="dtend" title="' . $timezoneDateTime->format($context->getEndTime(),'c') . '"&gt;' . $timezoneDateTime->format($context->getEndTime(),'g:i: a') . '&lt;/abbr&gt;&lt;/small&gt;';
            }
        }

        $loc = $context->eventdatetime->getLocation();
        if (isset($context->eventdatetime->location_id) && $loc !== false) {
            echo ' | &lt;small&gt;'.$loc->name;
            if (isset($context->eventdatetime->room)) {
                echo ' Room:'.$context->eventdatetime->room;
            }
            echo '&lt;/small&gt;';
        } ?>
    </description>
    <pubDate><?php echo date('r',strtotime($context->event->datecreated)); ?></pubDate>
    <guid><?php echo $context->getURL(); ?></guid>
</item>
