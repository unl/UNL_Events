<?php if (isset($context->event->subtitle)): ?><header><?php endif; ?>
    <h2>
        <a class="url summary dcf-txt-decor-none" href="<?php echo $frontend->getEventURL($context->getRawObject()) ?>"><?php echo $savvy->dbStringtoHtml($context->event->title) ?></a>
    </h2>
    <?php if (isset($context->event->subtitle)): ?><p class="dcf-subhead"><?php echo $savvy->dbStringtoHtml($context->event->subtitle) ?></p><?php endif; ?>
<?php if (isset($context->event->subtitle)): ?></header><?php endif; ?>
