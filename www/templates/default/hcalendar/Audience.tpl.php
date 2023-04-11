<div id="results">
    <?php if ($context->countQuery() > 1): ?>
        <p class="dcf-txt-xs unl-font-sans unl-dark-gray">Displaying Audiences:
    <?php else: ?>
        <p class="dcf-txt-xs unl-font-sans unl-dark-gray">Displaying Audience:
    <?php endif; ?>
        <span><?php echo $context->getFormattedAudiences(); ?></span>
    </p>
    <h1 class="dcf-txt-h3 dcf-mt-0">Audience Results</h1>
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <?php echo $context->count(); ?>

        <?php if ($context->count() != 1 && $context->countQuery() > 1): ?>
            results from all calendars matching the selected audiences
        <?php elseif ($context->count() != 1 && $context->countQuery() <= 1): ?>
            results from all calendars matching the selected audience
        <?php elseif ($context->count() == 1 && $context->countQuery() > 1): ?>
            result from all calendars matching the selected audiences
        <?php else: ?>
            result from all calendars matching the selected audience
        <?php endif; ?>
    </p>
</div>

<?php echo $savvy->render($context, 'EventListing.tpl.php');
