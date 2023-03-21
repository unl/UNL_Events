<div>
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">Displaying Audiences:
        <span><?php echo $context->getFormattedAudiences(); ?></span>
    </p>
    <h2>Audience Results</h2>
    <p class="dcf-txt-xs unl-font-sans unl-dark-gray">
        <?php echo $context->count(); ?>
        result(s) from all calendars matching the selected audience(s)
    </p>
</div>

<?php echo $savvy->render($context, 'EventListing.tpl.php');
