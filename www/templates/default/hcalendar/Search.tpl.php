<div id="results">
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
            <?php if ($context->isDateRange()): ?>
                Displaying Search for Date Range:
                    <span class="dcf-bold">
                        <?php echo date('F jS, Y', $context->getStartDate()); ?>
                        <?php echo "-"; ?>
                        <?php echo date('F jS, Y', $context->getEndDate()); ?>
                    </span>
            <?php elseif ($context->isSingleDate()): ?>
                Displaying Search for Date:
                    <span class="dcf-bold">
                        <?php echo date('F jS, Y', $context->getStartDate()); ?>
                    </span>
            <?php elseif (empty($context->search_query)): ?>
                Displaying Search:
                    <span class="dcf-bold">
                        Any Event
                    </span>
            <?php else: ?>
                Displaying Search:
                    <span class="dcf-bold">
                        <?php echo htmlentities($context->search_query); ?>
                    </span>
            <?php endif; ?>
        <?php if (!empty($context->search_event_type)): ?>
            <span class='dcf-d-block'>
                Filter Type: <?php echo htmlentities($context->search_event_type); ?>
            </span>
        <?php endif; ?>
        <?php if (!empty($context->search_event_audience)): ?>
            <span class='dcf-d-block'>
                Filter Audience: <?php echo htmlentities($context->search_event_audience); ?>
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
