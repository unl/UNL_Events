<div id="results">
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
            <?php if ($dt = $context->getSearchTimestamp()): ?>
                Displaying Search for Date:
                    <span>
                        <?php echo date('F jS', $dt); ?>
                    </span>
            <?php elseif (empty($context->search_query)): ?>
                Displaying Search:
                    <span>
                        Any Event
                    </span>
            <?php else: ?>
                Displaying Search:
                    <span>
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
    <?php if ($dt = $context->getSearchTimestamp()): ?>
        <h1
            class="dcf-txt-h3 dcf-mt-0"
            id="heading-date"
            data-datetime="<?php echo date(DATE_ATOM, $dt); ?>"
        >
            Search Results
        </h1>
    <?php else: ?>
        <h1 class="dcf-txt-h3 dcf-mt-0" id="heading-date">Search Results</h1>
    <?php endif; ?>

    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <?php echo $context->count(); ?>

        <?php if ($context->count() != 1): ?>
            results from the calendar "<?php echo htmlentities($context->calendar->name); ?>" matching search query
        <?php else: ?>
            results from the calendar "<?php echo htmlentities($context->calendar->name); ?>" matching search query
        <?php endif; ?>
    </p>
</div>


<?php echo $savvy->render($context, 'EventListing.tpl.php');
