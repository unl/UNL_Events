<?php
  if (empty($timezoneDisplay) || empty($timezoneDisplay->getTimezone())) {
      // set with default calendar timezone
      $timezoneDisplay = new \UNL\UCBCN\TimezoneDisplay($context->calendar->defaulttimezone);
  }
  $formattedDate = $context->getDateTime($timezoneDisplay)->format('l, F j');
?>
<div class="section-heading">
    <h2 data-datetime="<?php echo $context->getDateTime()->format('c') ?>">
      <?php echo $formattedDate ?>
    </h2>
    <div class="links">
        <a href="<?php echo $context->getURL(); ?>" aria-label="permalink">
            <svg class="dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M14.474 10.232l-.706-.706a4.004 4.004 0 00-5.658-.001l-4.597 4.597a4.004 4.004 0 000 5.657l.707.706a3.97 3.97 0 002.829 1.173 3.973 3.973 0 002.827-1.172l2.173-2.171a.999.999 0 10-1.414-1.414l-2.173 2.17c-.755.756-2.071.757-2.828 0l-.707-.706a2.004 2.004 0 010-2.829l4.597-4.596c.756-.756 2.073-.756 2.828 0l.707.707a1.001 1.001 0 001.415-1.415z"></path><path d="M20.486 4.221l-.707-.706a3.97 3.97 0 00-2.829-1.173 3.977 3.977 0 00-2.827 1.172L12.135 5.5a.999.999 0 101.414 1.414l1.988-1.984c.755-.756 2.071-.757 2.828 0l.707.706c.779.78.779 2.049 0 2.829l-4.597 4.596c-.756.756-2.073.756-2.828 0a.999.999 0 00-1.414 0 .999.999 0 00-.001 1.414 4.001 4.001 0 005.657.001l4.597-4.597a4.005 4.005 0 000-5.658z"></path></svg>
        </a>
        <a href="<?php echo $context->getURL() ?>.ics" aria-label="I C S format for events on <?php echo $formattedDate ?>">
            <svg class="dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24"><path d="M23.5 2H20V.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V2H8V.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V2H.5a.5.5 0 00-.5.5V7h24V2.5a.5.5 0 00-.5-.5zM7 4H5V1h2v3zm12 0h-2V1h2v3zM0 23.5a.5.5 0 00.5.5h23a.5.5 0 00.5-.5V8H0v15.5zM7 15h4v-4a1 1 0 012 0v4h4a1 1 0 010 2h-4v4a1 1 0 01-2 0v-4H7a1 1 0 010-2z"></path></svg>
        </a>
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
    <div class="dcf-m-0 dcf-pt-4 dcf-pb-4 dcf-pr-6 dcf-pl-6 dcf-txt-xs dcf-regular dcf-uppercase unl-ls-0 unl-cream">
        <h2>Ongoing Events</h2>
    </div>

    <?php echo $savvy->render(new ArrayIterator($events['ongoing']), 'EventListing.tpl.php'); ?>
<?php endif; ?>
