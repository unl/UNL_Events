<?php
    use UNL\UCBCN\Event\Occurrence;

    $classes = array('date-wrapper');
    if ($context->isAllDay()) {
        $classes[] = 'all-day';
    }

    $starttime = $context->getStartTime();
    $endtime = $context->getEndTime();
    $timezone = $context->eventdatetime->timezone;
    if (empty($timezoneDisplay) || empty($timezoneDisplay->getTimezone())) {
        // set with default calendar timezone
        $timezoneDisplay = new \UNL\UCBCN\TimezoneDisplay($context->calendar->defaulttimezone);
    }

    // Define recurring details
    $recurring_details = "";
    if ($context->eventdatetime->recurringtype == 'daily' ||
        $context->eventdatetime->recurringtype == 'weekly' ||
        $context->eventdatetime->recurringtype == 'biweekly' ||
        $context->eventdatetime->recurringtype == 'annually'
    ) {
        $recurring_details = ucwords($context->eventdatetime->recurringtype) . ':';
    } elseif ($context->eventdatetime->recurringtype == 'monthly') {
        if ($context->eventdatetime->rectypemonth == 'lastday') {
            $recurring_details = 'Last day of every month:';
        } elseif ($context->eventdatetime->rectypemonth == 'date') {
            $recurring_details = date('jS', strtotime($context->eventdatetime->starttime)) . ' of every month:';
        } else {
            $recurring_details = ucwords($context->eventdatetime->rectypemonth)
            . date(' l', strtotime($context->eventdatetime->starttime))
            . ' of every month:';
        }
    }
?>
<?php if ($context->eventdatetime->recurringtype !== "none"): ?>
    <span class="date-wrapper">
        <svg
            class="dcf-h-4 dcf-w-4 dcf-fill-current"
            aria-hidden="true"
            focusable="false"
            height="24"
            width="24"
            viewBox="0 0 24 24"
        >
            <path d="M23.5,2H20V0.5C20,0.224,19.776,0,19.5,0h-3C16.224,0,16,0.224,16,0.5V2
                H8V0.5C8,0.224,7.776,0,7.5,0h-3 C4.224,0,4,0.224,4,0.5V2
                H0.5C0.224,2,0,2.224,0,2.5v21C0,23.776,0.224,24,0.5,24h23
                c0.276,0,0.5-0.224,0.5-0.5v-21 C24,2.224,23.776,2,23.5,2z
                M17,1h2v3h-2V1z M5,1h2v3H5V1z M4,3v1.5C4,4.776,4.224,5,4.5,5h3
                C7.776,5,8,4.776,8,4.5V3h8v1.5 C16,4.776,16.224,5,16.5,5h3
                C19.776,5,20,4.776,20,4.5V3h3v4H1V3H4z M1,23V8h22v15H1z"></path>
            <path d="M12.5,10c-2.808,0-5.127,2.116-5.456,4.837l-1.19-1.19
                c-0.195-0.195-0.512-0.195-0.707,0s-0.195,0.512,0,0.707l2,2
                c0.196,0.197,0.485,0.185,0.667,0.038l2.5-2c0.215-0.173,0.25-0.487,0.078-0.703
                c-0.173-0.214-0.487-0.251-0.703-0.078 L8.06,14.912
                C8.351,12.71,10.22,11,12.5,11c2.481,0,4.5,2.019,4.5,4.5
                c0,2.482-2.019,4.5-4.5,4.5 c-1.589,0-3.078-0.853-3.883-2.225
                c-0.14-0.238-0.446-0.319-0.685-0.178c-0.238,0.14-0.317,0.447-0.178,0.684
                C8.739,19.958,10.558,21,12.5,21c3.033,0,5.5-2.467,5.5-5.5S15.533,10,12.5,10z"></path>
            <g>
                <path fill="none" d="M0 0H24V24H0z"></path>
            </g>
        </svg>
        <span class="dcf-sr-only">Recurring Date Info:</span>
        <div>
            <?php echo $recurring_details; ?>
            <time
                datetime="<?php echo $timezoneDisplay->format($context->eventdatetime->starttime, $timezone, 'c') ?>"
            >
                <?php if ($timezoneDisplay->format($context->eventdatetime->starttime, $timezone, 'M') === "May"): ?>
                    <?php echo $timezoneDisplay->format($context->eventdatetime->starttime, $timezone, 'M j, Y') ?>
                <?php else: ?>
                    <?php echo $timezoneDisplay->format($context->eventdatetime->starttime, $timezone, 'M. j, Y') ?>
                <?php endif; ?>
            </time>
            &ndash;
            <time
                datetime="<?php echo $timezoneDisplay->format($context->eventdatetime->recurs_until, $timezone, 'c') ?>"
            >
                <?php if ($timezoneDisplay->format($context->eventdatetime->recurs_until, $timezone, 'M') === "May"): ?>
                    <?php echo $timezoneDisplay->format($context->eventdatetime->recurs_until, $timezone, 'M j, Y') ?>
                <?php else: ?>
                    <?php echo $timezoneDisplay->format($context->eventdatetime->recurs_until, $timezone, 'M. j, Y') ?>
                <?php endif; ?>
            </time>
        </div>
    </span>
<?php endif; ?>
<span class="date-wrapper">
    <svg
        class="dcf-h-4 dcf-w-4 dcf-fill-current"
        aria-hidden="true"
        focusable="false"
        height="24"
        width="24"
        viewBox="0 0 24 24"
    >
        <path d="M23.5 2H20V.5c0-.3-.2-.5-.5-.5h-3c-.3 0-.5.2-.5.5V2H8
            V.5c0-.3-.2-.5-.5-.5h-3c-.3 0-.5.2-.5.5V2H.5c-.3 0-.5.2-.5.5v21
            c0 .3.2.5.5.5h23c.3 0 .5-.2.5-.5v-21c0-.3-.2-.5-.5-.5zM17
            1h2v3h-2V1zM5 1h2v3H5V1zM4 3v1.5c0 .3.2.5.5.5h3c.3 0
            .5-.2.5-.5V3h8v1.5c0 .3.2.5.5.5h3c.3 0 .5-.2.5-.5V3h3v4H1V3h3zM1
            23V8h22v15H1z"></path>
    </svg>
    <span class="dcf-sr-only">Date:</span>
    <div>
        <?php if (!empty($starttime)): ?>
            <time
                class="dtstart"
                datetime="<?php echo $timezoneDisplay->format($starttime, $timezone, 'c') ?>"
            >
                <?php if ($timezoneDisplay->format($starttime, $timezone, 'M') === "May"): ?>
                    <?php echo $timezoneDisplay->format($starttime, $timezone, 'M j, Y') ?>
                <?php else: ?>
                    <?php echo $timezoneDisplay->format($starttime, $timezone, 'M. j, Y') ?>
                <?php endif; ?>
            </time>
        <?php endif; ?>
        <?php if (!empty($endtime) && $context->isOngoing()): ?>
            &ndash; <time
                class="dtend"
                datetime="<?php echo $timezoneDisplay->format($endtime, $timezone, 'c') ?>"
            >
                <?php if ($timezoneDisplay->format($endtime, $timezone, 'M') === "May"): ?>
                    <?php echo $timezoneDisplay->format($endtime, $timezone, 'M j, Y') ?>
                <?php else: ?>
                    <?php echo $timezoneDisplay->format($endtime, $timezone, 'M. j, Y') ?>
                <?php endif; ?>
            </time>
        <?php endif; ?>
    </div>
</span>
<span class="time-wrapper">
    <svg
        class="dcf-h-4 dcf-w-4 dcf-fill-current"
        aria-hidden="true"
        focusable="false"
        height="24"
        width="24"
        viewBox="0 0 24 24"
    >
        <path d="M12 0C5.383 0 0 5.383 0 12s5.383 12 12 12 12-5.383 12-12
        S18.617 0 12 0zm0 23C5.935 23 1 18.066 1 12 1 5.935 5.935 1 12
        1s11 4.935 11 11c0 6.066-4.935 11-11 11z"></path>
        <path d="M13.716 13.011A1.98 1.98 0 0014 12c0-1.103-.897-2-2-2
        V6.5a.5.5 0 00-1 0v3.778c-.595.347-1 .984-1 1.722 0 1.103.897
        2 2 2a1.97 1.97 0 001.008-.283l4.638 4.636a.5.5 0 00.707-.707
        l-4.637-4.635zM11 12c0-.551.449-1 1-1s1 .449 1 1a.996.996 0
        01-.288.699l-.009.006-.005.007A.993.993 0 0112 13c-.551 0-1-.449-1-1z"></path>
    </svg>
    <span class="dcf-sr-only">Time:</span>
    <div>
        <?php if ($context->isAllDay()): ?>
            All Day
        <?php elseif ($context->eventdatetime->timemode === Occurrence::TIME_MODE_TBD):?>
            <abbr title="To Be Determined">TBD</abbr>
        <?php elseif ($context->eventdatetime->timemode === Occurrence::TIME_MODE_KICKOFF):?>
            Starts at
            <?php echo $timezoneDisplay->format($starttime, $timezone, 'g:i a')?>
            <?php
                if ($timezone != $context->calendar->defaulttimezone) {
                    echo $timezoneDisplay->format($starttime, $timezone, ' T');
                }
            ?>
        <?php elseif ($context->eventdatetime->timemode === Occurrence::TIME_MODE_DEADLINE):?>
            Ends at
            <?php echo $timezoneDisplay->format($endtime, $timezone, 'g:i a')?>
            <?php
                if ($timezone != $context->calendar->defaulttimezone) {
                    echo $timezoneDisplay->format($endtime, $timezone, ' T');
                }
            ?>
        <?php else: ?>
            <?php echo $timezoneDisplay->format($starttime, $timezone, 'g:i a')?>
            <?php if (!empty($endtime) && $endtime != $starttime): ?>
                &ndash;
                <?php echo $timezoneDisplay->format($endtime, $timezone,'g:i a')?>
            <?php endif; ?>
            <?php
                if ($timezone != $context->calendar->defaulttimezone) {
                    echo $timezoneDisplay->format($starttime, $timezone, ' T');
                }
            ?>
        <?php endif; ?>
    </div>
</span>
