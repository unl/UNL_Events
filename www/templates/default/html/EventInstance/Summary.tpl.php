<?php
    // Gets the event and audience
    $event = $context->eventdatetime->getEvent();
    $event_event_type = $event->getFirstType();
?>

<?php if (isset($context->event->subtitle)): ?><header><?php endif; ?>
    <h2>
        <span class="dcf-relative">
            <a 
                class="url summary dcf-txt-decor-none"
                href="<?php echo $frontend->getEventURL($context->getRawObject()) ?>">
                <?php echo $savvy->dbStringtoHtml($context->event->displayTitle($context)) ?>
            </a>
            <?php if (isset($event_event_type) && !empty($event_event_type)): ?>
                <small class="dcf-badge dcf-badge-roundrect dcf-absolute dcf-top-0 dcf-left-100% dcf-ml-3">
                    <a href="<?php echo $frontend->getEventTypeURL() . '?q=' . $event_event_type->name; ?>" style="color: inherit;">
                        <?php echo $event_event_type->name; ?>
                    </a>
                </small>
            <?php endif; ?>
        </span>
    </h2>
    <?php if (isset($context->event->subtitle)): ?><p class="dcf-subhead"><?php echo $savvy->dbStringtoHtml($context->event->subtitle) ?></p><?php endif; ?>
<?php if (isset($context->event->subtitle)): ?></header><?php endif; ?>
