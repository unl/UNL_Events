<div class="calendar">
    <?php echo $savvy->render($context->getMonthWidget()); ?>
</div>

<div id="subscribe">
    <span>Subscribe to this calendar</span>
    <ul id="droplist">
        <li id="eventrss"><a href="<?php echo $frontend->getUpcomingURL(); ?>?format=rss&amp;limit=100"><span class="eventicon-rss" aria-hidden="true"></span>RSS</a></li>
        <li id="eventical"><a href="<?php echo $frontend->getWebcalUpcomingURL(); ?>?format=ics&amp;limit=-1"><span class="wdn-icon-calendar" aria-hidden="true"></span>ICS</a></li>
    </ul>
</div>