<?php
  if (empty($timezoneDisplay) || empty($timezoneDisplay->getTimezone())) {
      // set with default calendar timezone
      $timezoneDisplay = new \UNL\UCBCN\TimezoneDisplay($context->calendar->defaulttimezone, FALSE);
  }
  $formattedDate = $context->getDateTime($timezoneDisplay)->format('l, F j');
?>
<div class="section-heading">
    <h2 data-datetime="<?php echo $context->getDateTime()->format('c') ?>">
      <?php echo $formattedDate ?>
    </h2>
    <div class="links">
        <a class="permalink" href="<?php echo $context->getURL(); ?>"><span class="wdn-icon-link" aria-hidden="true"></span><span class="dcf-sr-only">permalink</span></a>
        <a href="<?php echo $context->getURL() ?>.ics"><span class="wdn-icon-calendar" aria-hidden="true"></span><span class="dcf-sr-only">ics format for events on <?php echo $formattedDate ?></span></a>
    </div>
</div>

<p class="day-nav">
    <a class="url prev" href="<?php echo $context->getPreviousDay()->getURL(); ?>">Previous Day</a>
    <a class="url next" href="<?php echo $context->getNextDay()->getURL(); ?>">Next Day</a>
</p>
<?php
$events = array(
    'ongoing' => array(),
    'today' => array(),
);
foreach ($context->getRawObject() as $event) {
    if ($event->isOngoing()) {
        $events['ongoing'][] = $event;
    } else {
        $events['today'][] = $event;
    }
}
?>
<?php echo $savvy->render(new ArrayIterator($events['today']), 'EventListing.tpl.php'); ?>
<?php if (!empty($events['ongoing'])): ?>
    <div class="section-heading">
        <h2>Ongoing Events</h2>
    </div>

    <?php echo $savvy->render(new ArrayIterator($events['ongoing']), 'EventListing.tpl.php'); ?>
<?php endif; ?>
