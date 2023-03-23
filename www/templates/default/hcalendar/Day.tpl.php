<?php
  if (empty($timezoneDisplay) || empty($timezoneDisplay->getTimezone())) {
      // set with default calendar timezone
      $timezoneDisplay = new \UNL\UCBCN\TimezoneDisplay($context->calendar->defaulttimezone);
  }
  $formattedDate = $context->getDateTime($timezoneDisplay)->format('l, F j');
?>
<div>
    <h2
        class="dcf-mt-0"
        id="heading-date"
        data-datetime="<?php echo $context->getDateTime()->format('c') ?>"
    >
        <?php echo $formattedDate ?>
    </h2>

    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <a href="<?php echo $context->getURL() ?>.ics">
            <svg class="dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24">
                <path d="M23.5 2H20V.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V2H8V.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V2H.5a.5.5 0 00-.5.5V7h24V2.5a.5.5 0 00-.5-.5zM7 4H5V1h2v3zm12 0h-2V1h2v3zM0 23.5a.5.5 0 00.5.5h23a.5.5 0 00.5-.5V8H0v15.5zM7 15h4v-4a1 1 0 012 0v4h4a1 1 0 010 2h-4v4a1 1 0 01-2 0v-4H7a1 1 0 010-2z"></path>
            </svg>
            ICS for <?php echo $formattedDate ?>
        </a>
    </p>
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
