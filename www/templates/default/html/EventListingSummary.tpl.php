<div class="vcalendar">
    <ol class="dcf-mt-4 dcf-mb-0 dcf-list-bare dcf-grid-full dcf-grid-halves@sm dcf-grid-thirds@lg dcf-grid-fourths@xl dcf-col-gap-vw dcf-row-gap-4">
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
                $location = '<div class="unl-event-location dcf-d-flex dcf-ai-center dcf-lh-3"><svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-flex-shrink-0 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24"><path d="M12 23.5c-.3 0-.6-.2-.8-.4-.7-1.1-7-10.7-7-14.7C4.2 4 7.7.5 12 .5s7.8 3.5 7.8 7.8c0 4-6.3 13.6-7 14.7-.2.3-.5.5-.8.5zm0-21c-3.2 0-5.8 2.6-5.8 5.8 0 2.5 3.7 8.9 5.8 12.3 2.2-3.4 5.8-9.8 5.8-12.3 0-3.2-2.6-5.8-5.8-5.8z"></path><path d="M12 12.1c-2.1 0-3.7-1.7-3.7-3.7 0-2.1 1.7-3.7 3.7-3.7 2.1 0 3.7 1.7 3.7 3.7s-1.6 3.7-3.7 3.7zm0-5.5c-1 0-1.7.8-1.7 1.7S11.1 10 12 10s1.7-.8 1.7-1.7S13 6.6 12 6.6z"></path><path fill="none" d="M0 0h24v24H0z"></path></svg>';
                if (isset($l->mapurl) && filter_var($l->mapurl, FILTER_VALIDATE_URL)) {
                    $location .= '<a class="mapurl" href="' . $l->mapurl .'">' . $l->name . '</a>';
                } elseif (isset($l->webpageurl) && filter_var($l->webpageurl, FILTER_VALIDATE_URL)) {
                    $location .= '<a class="webpageurl" href="' . $l->webpageurl .'">' . $l->name . '</a>';
                } else {
                    $location .= '<span>' . $l->name . '</span>';
                }
                $location .= '</div>';
            }
        }
        $datetimedate = $timezoneDisplay->format($starttime, $eventinstance->eventdatetime->timezone,'Y-m-d');
        $month = $timezoneDisplay->format($starttime, $eventinstance->eventdatetime->timezone,'M');
        $day = $timezoneDisplay->format($starttime, $eventinstance->eventdatetime->timezone,'j');
        $time = '';
        if (!$eventinstance->isAllDay()) {
            $time = '<time class="unl-event-time dcf-d-flex dcf-ai-center dcf-uppercase" datetime="' . $timezoneDisplay->format($starttime, $eventinstance->eventdatetime->timezone, 'H:i') . '"><svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-flex-shrink-0 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24"><path d="M12 23C5.9 23 1 18.1 1 12S5.9 1 12 1s11 4.9 11 11-4.9 11-11 11zm0-20c-5 0-9 4-9 9s4 9 9 9 9-4 9-9-4-9-9-9z"></path><path d="M16.8 17.8c-.2 0-.5-.1-.7-.3l-5.2-4.8c-.2-.2-.3-.5-.3-.7V7.2c0-.6.4-1 1-1s1 .4 1 1v4.3l4.9 4.5c.4.4.4 1 .1 1.4-.3.3-.5.4-.8.4z"></path><path fill="none" d="M0 0h24v24H0z"></path></svg>' . $timezoneDisplay->format($starttime, $eventinstance->eventdatetime->timezone, 'g:i a') . '</time>';
        } else {
            $time = '<div class="unl-event-time dcf-d-flex dcf-ai-center dcf-uppercase"><svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-flex-shrink-0 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24"><path d="M12 23C5.9 23 1 18.1 1 12S5.9 1 12 1s11 4.9 11 11-4.9 11-11 11zm0-20c-5 0-9 4-9 9s4 9 9 9 9-4 9-9-4-9-9-9z"></path><path d="M16.8 17.8c-.2 0-.5-.1-.7-.3l-5.2-4.8c-.2-.2-.3-.5-.3-.7V7.2c0-.6.4-1 1-1s1 .4 1 1v4.3l4.9 4.5c.4.4.4 1 .1 1.4-.3.3-.5.4-.8.4z"></path><path fill="none" d="M0 0h24v24H0z"></path></svg>All day</div>';
        }
        ?>
            <li class="unl-event-teaser-li dcf-mb-0">
                <article class="unl-event-teaser dcf-col-gap-4 dcf-card-as-link">
                    <header class="unl-event-title">
                        <h3 class="dcf-mb-0 dcf-lh-3 dcf-bold dcf-txt-h6 unl-lh-crop"><a class="dcf-txt-decor-hover dcf-card-link unl-darker-gray" href="<?php echo $url; ?>"><?php echo $event->displayTitle($eventinstance); ?></a></h3><?php echo $subTitle; ?>
                    </header>
                    <div class="unl-event-date dcf-flex-shrink-0 dcf-w-8 dcf-txt-center" datetime="<?php echo $datetimedate; ?>">
                        <span class="dcf-d-block dcf-txt-3xs dcf-pt-2 dcf-pb-1 dcf-uppercase dcf-bold unl-ls-2 unl-cream unl-bg-scarlet"><?php echo $month; ?></span>
                        <span class="dcf-d-block dcf-txt-h5 dcf-bold dcf-br-1 dcf-bb-1 dcf-bl-1 dcf-br-solid dcf-bb-solid dcf-bl-solid unl-br-light-gray unl-bb-light-gray unl-bl-light-gray unl-darker-gray dcf-bg-white"><?php echo $day; ?></span>
                    </div>
                    <div class="unl-event-details dcf-txt-xs unl-dark-gray">
                        <?php echo $time; ?>
                        <?php echo $location; ?>
                    </div>
                </article>
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
