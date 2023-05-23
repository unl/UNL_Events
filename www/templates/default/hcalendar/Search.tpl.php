<div id="results">
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <span class="dcf-bold">
            <?php if ($context->isDateRange()): ?>
                Displaying Search for Date Range:
                <?php echo date('F jS, Y', $context->getStartDate()); ?>
                <?php echo "-"; ?>
                <?php echo date('F jS, Y', $context->getEndDate()); ?>
            <?php elseif ($context->isSingleDate()): ?>
                    Displaying Search for Date:
                    <?php echo date('F jS, Y', $context->getStartDate()); ?>
            <?php elseif (empty($context->search_query)): ?>
                    Displaying Search:
                    Any Event
            <?php else: ?>
                    Displaying Search:
                    <?php echo $context->search_query; ?>
            <?php endif; ?>
        </span>

        <?php if (!empty($context->event_type_filter)): ?>
            <span class='dcf-d-block dcf-ml-2'>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" class="dcf-h-4 dcf-w-4 dcf-fill-current">
                    <path d="M29.9,15.7c0,0,0-0.1-0.1-0.2c-0.1-0.2-0.2-0.4-0.4-0.6
                        l-7.2-7.2c-0.7-0.7-1.7-0.7-2.4,0c-0.7,0.7-0.7,1.7,0,2.4l4.4,4.3H3.4V1.7C3.4,0.8,2.6,0,1.7,0
                        S0,0.8,0,1.7v14.4c0,0.9,0.8,1.7,1.7,1.7h22.5l-4.4,4.3c-0.3,0.3-0.5,0.7-0.5,1.2
                        s0.2,0.9,0.5,1.2c0.3,0.3,0.7,0.5,1.2,0.5s0.9-0.2,1.2-0.5l7.2-7.2c0.1-0.1,0.3-0.3,0.4-0.5
                        c0-0.1,0.1-0.2,0.1-0.3c0-0.1,0-0.2,0-0.4C30,16,30,15.8,29.9,15.7z"/>
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
                    <path d="M29.9,15.7c0,0,0-0.1-0.1-0.2c-0.1-0.2-0.2-0.4-0.4-0.6
                        l-7.2-7.2c-0.7-0.7-1.7-0.7-2.4,0c-0.7,0.7-0.7,1.7,0,2.4l4.4,4.3H3.4V1.7C3.4,0.8,2.6,0,1.7,0
                        S0,0.8,0,1.7v14.4c0,0.9,0.8,1.7,1.7,1.7h22.5l-4.4,4.3c-0.3,0.3-0.5,0.7-0.5,1.2
                        s0.2,0.9,0.5,1.2c0.3,0.3,0.7,0.5,1.2,0.5s0.9-0.2,1.2-0.5l7.2-7.2c0.1-0.1,0.3-0.3,0.4-0.5
                        c0-0.1,0.1-0.2,0.1-0.3c0-0.1,0-0.2,0-0.4C30,16,30,15.8,29.9,15.7z"/>
                    <g>
                        <path fill="none" d="M0,0h30v30H0V0z"/>
                    </g>
                </svg>
                Target Audience: <?php echo $context->getFormattedAudiences(); ?>
            </span>
        <?php endif; ?>
    </p>
    <?php if ($context->isDateRange() || $context->isSingleDate()): ?>
        <h1
            class="dcf-txt-h3 dcf-mt-0"
            id="heading-date"
            data-datetime="<?php echo date(DATE_ATOM, $context->getStartDate()); ?>"
        >
            Search Results
        </h1>
    <?php else: ?>
        <h1 class="dcf-txt-h3 dcf-mt-0" id="heading-date">Search Results</h1>
    <?php endif; ?>

    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <?php if ($context->count() == 0): ?>
            No results from the calendar "<?php echo $context->calendar->name; ?>" matching search query
        <?php elseif ($context->count() != 1): ?>
            <?php echo $context->count(); ?>
            results from the calendar "<?php echo $context->calendar->name; ?>" matching search query
        <?php else: ?>
            <?php echo $context->count(); ?>
            results from the calendar "<?php echo $context->calendar->name; ?>" matching search query
        <?php endif; ?>
    </p>
</div>


<?php echo $savvy->render($context, 'EventListing.tpl.php');
