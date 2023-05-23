<div id="results">
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <span class="dcf-bold">
            Displaying upcoming from the calendar "<?php echo $context->calendar->name; ?>"
        </span>
        <?php if (!empty($context->event_type_filter)): ?>
            <span class='dcf-d-block dcf-ml-2'>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" class="dcf-h-4 dcf-w-4 dcf-fill-current">
                    <path d="M29.9,15.7c0,0,0-0.1-0.1-0.2c-0.1-0.2-0.2-0.4-0.4-0.6l-7.2-7.2c-0.7-0.7-1.7-0.7-2.4,0c-0.7,0.7-0.7,1.7,0,2.4l4.4,4.3
                    H3.4V1.7C3.4,0.8,2.6,0,1.7,0S0,0.8,0,1.7v14.4c0,0.9,0.8,1.7,1.7,1.7h22.5l-4.4,4.3c-0.3,0.3-0.5,0.7-0.5,1.2s0.2,0.9,0.5,1.2
                    c0.3,0.3,0.7,0.5,1.2,0.5s0.9-0.2,1.2-0.5l7.2-7.2c0.1-0.1,0.3-0.3,0.4-0.5c0-0.1,0.1-0.2,0.1-0.3c0-0.1,0-0.2,0-0.4
                    C30,16,30,15.8,29.9,15.7z"/>
                    <g>
                        <path fill="none" d="M0,0h30v30H0V0z"/>
                    </g>
                </svg>
                Event Type: <?php echo $context->getFormattedEventTypes(); ?>
            </span>
        <?php endif; ?>
        <?php if (!empty($context->audience_filter)): ?>
            <span class='dcf-d-block dcf-ml-2'>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" class="dcf-h-4 dcf-w-4 dcf-fill-current">
                    <path d="M29.9,15.7c0,0,0-0.1-0.1-0.2c-0.1-0.2-0.2-0.4-0.4-0.6l-7.2-7.2c-0.7-0.7-1.7-0.7-2.4,0c-0.7,0.7-0.7,1.7,0,2.4l4.4,4.3
                    H3.4V1.7C3.4,0.8,2.6,0,1.7,0S0,0.8,0,1.7v14.4c0,0.9,0.8,1.7,1.7,1.7h22.5l-4.4,4.3c-0.3,0.3-0.5,0.7-0.5,1.2s0.2,0.9,0.5,1.2
                    c0.3,0.3,0.7,0.5,1.2,0.5s0.9-0.2,1.2-0.5l7.2-7.2c0.1-0.1,0.3-0.3,0.4-0.5c0-0.1,0.1-0.2,0.1-0.3c0-0.1,0-0.2,0-0.4
                    C30,16,30,15.8,29.9,15.7z"/>
                    <g>
                        <path fill="none" d="M0,0h30v30H0V0z"/>
                    </g>
                </svg>
                Target Audience: <?php echo $context->getFormattedAudiences(); ?>
            </span>
        <?php endif; ?>
    </p>
    <h1 class="dcf-txt-h3 dcf-mt-0 dcf-d-flex dcf-flex-no-wrap dcf-ai-base dcf-jc-between">
        Upcoming Events
        <a
            class="dcf-txt-decor-hover"
            href="<?php echo $frontend->getCalendarURL(); ?>upcoming/.ics"
            aria-label="I C S for Upcoming Events"
        >
            <svg class="dcf-h-4 dcf-w-4 dcf-fill-current" focusable="false" width="24" height="24" viewBox="0 0 24 24">
                <path d="M23.5 2H20V.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V2
                    H8V.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V2H.5a.5.5
                    0 00-.5.5V7h24V2.5a.5.5 0 00-.5-.5zM7 4H5V1h2v3zm12
                    0h-2V1h2v3zM0 23.5a.5.5 0 00.5.5h23a.5.5 0 00.5-.5V8
                    H0v15.5zM7 15h4v-4a1 1 0 012 0v4h4a1 1 0 010 2h-4v4a1
                    1 0 01-2 0v-4H7a1 1 0 010-2z"></path>
            </svg>
        </a>
    </h1>
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <span>
            <?php if ($context->count() == 0): ?>
                No Events Coming Up
            <?php elseif ($context->count() != 1): ?>
                Next <?php echo $context->count(); ?> events
            <?php else: ?>
                Next event
            <?php endif; ?>
        </span>
    </p>
</div>

<?php echo $savvy->render($context, 'EventListing.tpl.php');
