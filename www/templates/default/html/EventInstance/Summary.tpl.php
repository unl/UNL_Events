<?php
    // Gets the event and audience
    $event = $context->eventdatetime->getEvent();
    $event_event_type = $event->getFirstType();
?>

<?php if (isset($context->event->subtitle)): ?><header><?php endif; ?>
    <?php if (isset($event_event_type) && !empty($event_event_type)): ?>
        <small class="dcf-badge dcf-badge-roundrect dcf-mb-4">
            <a
                class="dcf-txt-decor-hover"
                href="<?php echo $frontend->getEventTypeURL() . '?q=' . $event_event_type->name; ?>"
                style="color: inherit;"
            >
                <?php echo $event_event_type->name; ?>
            </a>
        </small>
    <?php endif; ?>
    <h2>
        <a
            class="url summary dcf-txt-decor-none"
            href="<?php echo $frontend->getEventURL($context->getRawObject()) ?>">
            <?php echo $savvy->dbStringtoHtml($context->event->displayTitle($context)) ?>
        </a>
    </h2>
    <?php if (isset($context->event->subtitle)): ?><p class="dcf-subhead"><?php echo $savvy->dbStringtoHtml($context->event->subtitle) ?></p><?php endif; ?>
<?php if (isset($context->event->subtitle)): ?></header><?php endif; ?>
