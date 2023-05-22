<div id="results">
    <?php if(empty($context->event_type_filter)): ?>
        <p class="dcf-txt-xs unl-font-sans unl-dark-gray">Displaying All Event Types
    <?php elseif ($context->countQuery() > 1): ?>
        <p class="dcf-txt-xs unl-font-sans unl-dark-gray">Displaying Event Types:
    <?php else: ?>
        <p class="dcf-txt-xs unl-font-sans unl-dark-gray">Displaying Event Type:
    <?php endif; ?>
        <span><?php echo $context->getFormattedEventTypes(); ?></span>
    </p>
    <h1 class="dcf-txt-h3 dcf-mt-0">Event Type Results</h1>
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <?php echo $context->count(); ?>

        <?php if ($context->count() != 1 && $context->countQuery() > 1): ?>
            results from all calendars matching the selected event types
        <?php elseif ($context->count() != 1 && $context->countQuery() <= 1): ?>
            results from all calendars matching the selected event type
        <?php elseif ($context->count() == 1 && $context->countQuery() > 1): ?>
            result from all calendars matching the selected event types
        <?php else: ?>
            result from all calendars matching the selected event type
        <?php endif; ?>
    </p>
</div>

<?php echo $savvy->render($context, 'EventListing.tpl.php');
