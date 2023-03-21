<div>
    <p class="summary dcf-txt-xs unl-font-sans" aria-live="polite" aria-atomic="true">Displaying Audiences: 
        <span><?php $context->getURL().'">'.$context->getFormattedAudiences(); ?></span>
    </p>
    <h2>Audience Results</h2>
    <p><?php echo $context->count().' from all calendars matching the selected audiences'; ?></p>
</div>

<?php echo $savvy->render($context, 'EventListing.tpl.php');
