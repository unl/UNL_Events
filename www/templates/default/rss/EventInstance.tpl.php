<?php
$timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($context->eventdatetime->timezone);
?>
<item>
    <title><?php echo $context->event->displayTitle(); ?></title>
    <link><?php echo $context->getURL(); ?></link>
    <description>
        <?php
        echo '&lt;div&gt;'.$context->event->description.'&lt;/div&gt;';
        if (isset($context->event->subtitle)) echo '&lt;div&gt;'.$context->event->subtitle.'&lt;/div&gt;';
        echo '&lt;div&gt;Status: ' . $context->event->icalStatus() . '&lt;/div&gt;';
        echo '&lt;small&gt;' . $timezoneDateTime->format($context->getStartTime(),'l, F jS') . '&lt;/small&gt;';

        if (isset($context->eventdatetime->starttime)) {
            if (strpos($context->eventdatetime->starttime,'00:00:00')) {
        echo ' | &lt;small&gt;&lt;abbr class="dtstart" title="' . $timezoneDateTime->format($context->getStartTime(),'c') . '"&gt;All day&lt;/abbr&gt;&lt;/small&gt;';
            } else {
        echo ' | &lt;small&gt;&lt;abbr class="dtstart" title="' . $timezoneDateTime->format($context->getStartTime(),'c') . '"&gt;' . $timezoneDateTime->format($context->getStartTime(),'g:i: a') . '&lt;/abbr&gt;&lt;/small&gt;';
            }
        } else {
            echo 'Unknown';
        }
        if (isset($context->eventdatetime->endtime) &&
            ($context->eventdatetime->endtime != $context->eventdatetime->starttime) &&
            ($context->eventdatetime->endtime > $context->eventdatetime->starttime)) {
        echo '-&lt;small&gt;&lt;abbr class="dtend" title="' . $timezoneDateTime->format($context->getEndTime(),'c') . '"&gt;' . $timezoneDateTime->format($context->getEndTime(),'g:i: a') . '&lt;/abbr&gt;&lt;/small&gt;';
        }
        if ($context->eventdatetime->location_id) {
            $loc = $context->eventdatetime->getLocation();
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
