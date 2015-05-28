<?php
    $events = $context->getCategorizedEvents();
?>
<ul class="wdn_tabs">
    <li><a href="#pending">Pending (<?php echo count($events['pending']); ?>)</a></li>
    <li><a href="#upcoming">Upcoming (<?php echo count($events['posted']); ?>)</a></li>
    <li><a href="#past">Past (<?php echo count($events['archived']); ?>)</a></li>
</ul>
<div class="wdn_tabs_content">
    <div id="pending">
        <?php if (count($events['pending']) == 0): ?>
            There are no pending events.
        <?php else: ?>
            <?php echo $savvy->render($events['pending'], 'Events/event-list.tpl.php') ?>
        <?php endif; ?>
    </div>
    <div id="upcoming">
        <?php if (count($events['posted']) == 0): ?>
            There are no upcoming events.
        <?php else: ?>
            <?php echo $savvy->render($events['posted'], 'Events/event-list.tpl.php') ?>
        <?php endif; ?>
    </div>
    <div id="past">
        <?php if (count($events['archived']) == 0): ?>
            There are no past events.
        <?php else: ?>
            <?php echo $savvy->render($events['archived'], 'Events/event-list.tpl.php') ?>
        <?php endif; ?>
    </div>
</div>
