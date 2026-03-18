<?php if (isset($currentUser) && $context->event->userCanEdit()): ?>
<a class="dcf-mt-4 dcf-btn dcf-btn-primary" href="<?php echo $context->event->getEditURL(); ?>">Edit Event</a>
<?php endif; ?>
