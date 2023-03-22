<div id="results">
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">Displaying Search:
        <span>
            <?php
                if ($dt = $context->getSearchTimestamp()) {
                    echo 'Date:' . date('F jS', $dt);
                } elseif (empty($context->search_query)) {
                    echo '\'Any\'';
                } else {
                    echo htmlentities($context->search_query);
                }
            ?>
        </span>
        <?php if (!empty($context->search_event_type)): ?>
            <span class='dcf-d-block'>
                Filter Type: <?php echo $context->search_event_type ?>
            </span>
        <?php endif; ?>
        <?php if (!empty($context->search_event_audience)): ?>
            <span class='dcf-d-block'>
                Filter Audience: <?php echo $context->search_event_audience ?>
            </span>
        <?php endif; ?>
    </p>
    <h2 class="dcf-mt-0">Search Results</h2>
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <?php echo $context->count(); ?>

        <?php if ($context->count() != 1: ?>
            results from "<?php echo $context->calendar->name; ?>" matching search query
        <?php else: ?>
            results from "<?php echo $context->calendar->name; ?>" matching search query
        <?php endif; ?>
    </p>
</div>


<?php echo $savvy->render($context, 'EventListing.tpl.php');
