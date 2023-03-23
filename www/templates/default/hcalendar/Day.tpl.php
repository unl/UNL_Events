<?php
  if (empty($timezoneDisplay) || empty($timezoneDisplay->getTimezone())) {
      // set with default calendar timezone
      $timezoneDisplay = new \UNL\UCBCN\TimezoneDisplay($context->calendar->defaulttimezone);
  }
  $formattedDate = $context->getDateTime($timezoneDisplay)->format('l, F j');
?>
<div>
    <h2 class="dcf-mt-0" id="heading-date" data-datetime="<?php echo $context->getDateTime()->format('c') ?>"><?php echo $formattedDate ?></h2>
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
    <div>
        <h2 class="dcf-mt-0">Ongoing Events</h2>
    </div>

    <?php echo $savvy->render(new ArrayIterator($events['ongoing']), 'EventListing.tpl.php'); ?>
<?php endif; ?>
