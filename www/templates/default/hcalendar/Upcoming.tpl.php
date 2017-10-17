<h1 class="upcoming-heading">
    Upcoming Events
    <a href="<?php echo $frontend->getCalendarURL(); ?>upcoming/.ics"><span class="wdn-icon-calendar" aria-hidden="true"></span><span class="wdn-text-hidden">ics format for upcoming events</span></a>
</h1>

<?php echo $savvy->render($context, 'EventListing.tpl.php');?>
