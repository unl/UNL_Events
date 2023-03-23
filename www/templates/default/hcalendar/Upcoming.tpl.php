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
    <h2 class="dcf-mt-0">Upcoming Events</h2>
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <?php if ($context->count() != 1): ?>
            Next <?php echo $context->count(); ?> events that are coming up
        <?php else: ?>
            Next event that is coming up
        <?php endif; ?>
    </p>
</div>

<?php echo $savvy->render($context, 'EventListing.tpl.php');
