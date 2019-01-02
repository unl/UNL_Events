<?php if ($imageURL = $context->getImageURL()): ?>
    <img class="event_description_img" src="<?php echo $imageURL ?>" alt="image for event <?php echo $context->event->id; ?>" />
<?php endif; ?>
