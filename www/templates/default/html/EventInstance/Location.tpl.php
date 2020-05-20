<?php if (isset($context->eventdatetime->location_id) && $context->eventdatetime->location_id): ?>
<?php $l = $context->eventdatetime->getLocation(); ?>
<?php if (isset($l->mapurl) || !empty($l->name)): ?>
    <span class="location">
        <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
            <path d="M12 0C7.589 0 4 3.589 4 8c0 4.245 7.273 15.307 7.583 15.775a.497.497 0 00.834 0C12.727 23.307 20 12.245 20 8c0-4.411-3.589-8-8-8zm0 22.58C10.434 20.132 5 11.396 5 8c0-3.86 3.14-7 7-7s7 3.14 7 7c0 3.395-5.434 12.132-7 14.58z"></path>
            <path d="M12 4.5c-1.93 0-3.5 1.57-3.5 3.5s1.57 3.5 3.5 3.5 3.5-1.57 3.5-3.5-1.57-3.5-3.5-3.5zm0 6c-1.378 0-2.5-1.122-2.5-2.5s1.122-2.5 2.5-2.5 2.5 1.122 2.5 2.5-1.122 2.5-2.5 2.5z"></path>
        </svg>
        <span class="dcf-sr-only">Location:</span>
<?php if (isset($l->mapurl)): ?>
        <a class="mapurl" href="<?php echo $l->mapurl ?>"><?php echo $l->name; ?></a>
<?php elseif (isset($l->webpageurl)): ?>
        <a class="webpageurl" href="<?php echo $l->webpageurl ?>"><?php echo $l->name; ?></a>
<?php else: ?>
        <?php echo $l->name; ?>
<?php endif; ?>
    </span>
<?php endif; ?>
<?php endif; ?>
