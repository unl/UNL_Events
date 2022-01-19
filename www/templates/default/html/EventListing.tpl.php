<div class="vcalendar">
    <?php
    foreach ($context as $eventinstance) {
        //Start building an array of row classes
        $row_classes = array('vevent');

        if ($eventinstance->isAllDay()) {
            $row_classes[] = 'all-day';
        }

        if ($eventinstance->isInProgress()) {
            $row_classes[] = 'in-progress';
        }

        if ($eventinstance->isOnGoing()) {
            $row_classes[] = 'ongoing';
        }

        ?>
        <div class="dcf-card dcf-card-as-link <?php echo implode(' ', $row_classes) ?>">
            <?php echo $savvy->render($eventinstance, 'EventInstance/Summary.tpl.php') ?>
            <a class="dcf-card-link dcf-d-none" href="<?php echo $eventinstance->getURL(); ?>">Go to Home Page</a>
            <div class="dcf-d-flex">
                <?php if ($eventinstance->getImageURL()): ?>
                <div class="dcf-flex-shrink-0 dcf-pr-4 dcf-pb-4">
                    <?php echo $savvy->render($eventinstance, 'EventInstance/Thumbnail.tpl.php') ?>
                </div>
                <?php endif; ?>
                <div class="dcf-flex-auto">
                    <?php echo $savvy->render($eventinstance, 'EventInstance/Date.tpl.php') ?>
                    <?php echo $savvy->render($eventinstance, 'EventInstance/Location.tpl.php') ?>
                    <?php echo $savvy->render($eventinstance, 'EventInstance/Description.tpl.php') ?>
                    <?php echo $savvy->render($eventinstance, 'EventInstance/OriginCalendar.tpl.php') ?>
                    <?php echo $savvy->render($eventinstance, 'EventInstance/EditButton.tpl.php') ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<?php
 if (isset($page)) {
    $page->addScriptDeclaration(" WDN.initializePlugin('card-as-link');");
 }
?>
