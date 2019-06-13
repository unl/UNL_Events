<h2>
  <a class="url summary" href="<?php echo $frontend->getEventURL($context->getRawObject()) ?>"><?php echo $savvy->dbStringtoHtml($context->event->title) ?></a>
  <?php if (isset($context->event->subtitle)): ?><span class="dcf-subhead"><?php echo $savvy->dbStringtoHtml($context->event->subtitle) ?></span><?php endif; ?>
</h2>