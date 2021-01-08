<?php

use UNL\UCBCN\Frontend\Controller;

$originCalendar = $context->event->getOriginCalendar();
if (!empty($context->calendar->id) && !empty($originCalendar->id) && $context->calendar->id != $originCalendar->id): ?>
    <?php
        $name = $savvy->dbStringtoHtml($originCalendar->name);
        $name = $savvy->linkify($name);
    ?>
    <div><small class="dcf-txt-2xs">This event originated in <a href="<?php echo Controller::$url . urlencode($originCalendar->shortname); ?>"><?php echo $name; ?></a>.</small></div>
<?php endif; ?>
