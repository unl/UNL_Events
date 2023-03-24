<div id="results">
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <?php if (!empty($context->upcoming_event_type)): ?>
            <span class='dcf-d-block'>
                Filter Type: <?php echo $context->upcoming_event_type; ?>
            </span>
        <?php endif; ?>
        <?php if (!empty($context->upcoming_event_audience)): ?>
            <span class='dcf-d-block'>
                Filter Audience: <?php echo $context->upcoming_event_audience; ?>
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
            <?php if ($context->count() != 1): ?>
                Next <?php echo $context->count(); ?> events
            <?php else: ?>
                Next event
            <?php endif; ?>
        </span>
    </p>
</div>

<?php echo $savvy->render($context, 'EventListing.tpl.php');
