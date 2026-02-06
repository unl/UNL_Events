<?php if ($imageURL = $context->getImageURL()): ?>
    <img class="event_description_img" src="<?php echo $imageURL ?>" loading="lazy" aria-hidden="true" alt="">
<?php endif; ?>
