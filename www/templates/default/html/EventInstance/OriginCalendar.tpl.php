<?php

use UNL\UCBCN\Frontend\Controller;

$originCalendar = $context->event->getOriginCalendar();
if (!empty($context->calendar->id) && !empty($originCalendar->id) && $context->calendar->id != $originCalendar->id): ?>
    <?php
        $name = $savvy->dbStringtoHtml($originCalendar->name);
        $name = $savvy->linkify($name);
    ?>
    <div class="dcf-mt-6 dcf-txt-2xs">
        This event originated in <a href="<?php echo Controller::$url . urlencode($originCalendar->shortname); ?>"><?php echo $name; ?></a>.
    </div>
<?php endif; ?>
