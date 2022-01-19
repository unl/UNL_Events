<?php if ($imageURL = $context->getImageURL()): ?>
    <div class="thumbnail-container">
        <img class="event-thumbnail" src="<?php echo $imageURL ?>" aria-hidden="true" alt="">
    </div>
<?php endif; ?>
