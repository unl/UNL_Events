<div class="vcalendar">
    <ol class="dcf-mt-4 dcf-list-bare dcf-grid-full dcf-grid-halves@sm dcf-grid-thirds@lg dcf-grid-fourths@xl dcf-col-gap-vw dcf-row-gap-4">
    <?php
    foreach ($context as $eventinstance) {
        if (empty($eventinstance)) {
            continue;
        }
        $event = $eventinstance->event;
        $starttime = $eventinstance->getStartTime();
        if (empty($timezoneDisplay) || empty($timezoneDisplay->getTimezone())) {
            // set with default calendar timezone
            $timezoneDisplay = new \UNL\UCBCN\TimezoneDisplay($eventinstance->calendar->defaulttimezone);
        }

        $url = $frontend->getEventURL($eventinstance->getRawObject());
        $subTitle = !empty($event->subtitle) ? '<p class="dcf-subhead dcf-mt-2 dcf-txt-3xs unl-dark-gray">' . $event->subtitle . '</p>' : '';
        $location = '';
        if (isset($eventinstance->eventdatetime->location_id) && $eventinstance->eventdatetime->location_id) {
            $l = $eventinstance->eventdatetime->getLocation();
            if (isset($l->mapurl) || !empty($l->name)) {
                $location = '<div class="unl-event-location dcf-txt-xs dcf-pt-1 unl-dark-gray">';
                if (isset($l->mapurl) && filter_var($l->mapurl, FILTER_VALIDATE_URL)) {
                    $location .= '<a class="mapurl" href="' . $l->mapurl .'">' . $l->name . '</a>';
                } elseif (isset($l->webpageurl) && filter_var($l->webpageurl, FILTER_VALIDATE_URL)) {
                    $location .= '<a class="webpageurl" href="' . $l->webpageurl .'">' . $l->name . '</a>';
                } else {
                    $location .= $l->name;
                }
                $location .= '</div>';
            }
        }
        $month = $timezoneDisplay->format($starttime, $eventinstance->eventdatetime->timezone,'M');
        $day = $timezoneDisplay->format($starttime, $eventinstance->eventdatetime->timezone,'j');
        $time = 'All Day';
        if (!$eventinstance->isAllDay()) {
            $time = $timezoneDisplay->format($starttime, $eventinstance->eventdatetime->timezone, 'g:i a');
        }
        ?>
            <li class="unl-event-teaser">
                <header class="unl-event-title"><h3 class="dcf-mb-0 dcf-lh-3 dcf-bold dcf-txt-h6 unl-lh-crop"><a class="dcf-txt-decor-hover unl-darker-gray" href="<?php echo $url; ?>"><?php echo $event->displayTitle($eventinstance); ?></a></h3><?php echo $subTitle; ?><?php echo $location; ?></header>
                <div class="unl-event-datetime dcf-flex-shrink-0 dcf-w-8 dcf-mr-5 dcf-txt-center">
                    <span class="dcf-d-block dcf-txt-3xs dcf-pt-2 dcf-pb-1 dcf-uppercase dcf-bold unl-ls-2 unl-cream unl-bg-scarlet"><?php echo $month; ?></span>
                    <span class="dcf-d-block dcf-txt-h5 dcf-bold dcf-br-1 dcf-bb-1 dcf-bl-1 dcf-br-solid dcf-bb-solid dcf-bl-solid unl-br-light-gray unl-bb-light-gray unl-bl-light-gray unl-darker-gray dcf-bg-white"><?php echo $day; ?></span>
                    <span class="dcf-d-block dcf-pt-2 dcf-txt-3xs dcf-uppercase dcf-bold unl-scarlet"><?php echo $time; ?></span>
                </div>
            </li>
        <?php
    }
    ?>
    </ol>

    <div class="dcf-txt-right">
        <a class="dcf-btn dcf-btn-tertiary" href="<?php echo $context->calendar->getFeaturedURL(); ?>">View All<span class="dcf-sr-only"> Featured Events</span></a>
    </div>
</div>

<script>
  // TODO: Replace this with event css
  window.addEventListener('inlineJSReady', function() {
    WDN.initializePlugin('card-as-link');
    WDN.initializePlugin('events', {limit:0});
  });
</script>
