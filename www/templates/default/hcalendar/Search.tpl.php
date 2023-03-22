<div id="results">
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <?php if ($dt = $context->getSearchTimestamp()): ?>
            Displaying Search for Date:
        <?php else: ?>
            Displaying Search:
        <?php endif; ?>
        <span>
            <?php
                if ($dt = $context->getSearchTimestamp()) {
                    echo date('F jS', $dt);
                } elseif (empty($context->search_query)) {
                    echo '\'Any\'';
                } else {
                    echo htmlentities($context->search_query);
                }
            ?>
        </span>
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
        <h2 class="dcf-mt-0" id="heading-date" data-datetime="<?php echo date(DATE_ATOM, $dt); ?>">
    <?php else: ?>
        <h2 class="dcf-mt-0" id="heading-date">Search Results</h2>
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
