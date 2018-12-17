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
        <div class="<?php echo implode(' ', $row_classes) ?>">
          <?php echo $savvy->render($eventinstance, 'EventInstance/Summary.tpl.php') ?>
          <div class="dcf-grid dcf-col-gap-3">
            <div class="dcf-col-100% dcf-col-25%-start@sm dcf-pb-3">
                <?php echo $savvy->render($eventinstance, 'EventInstance/Thumbnail.tpl.php') ?>
            </div>
            <div class="dcf-col-100% dcf-col-75%-end@sm">
                <?php echo $savvy->render($eventinstance, 'EventInstance/Date.tpl.php') ?>
                <?php echo $savvy->render($eventinstance, 'EventInstance/Location.tpl.php') ?>
                <?php echo $savvy->render($eventinstance, 'EventInstance/Description.tpl.php') ?>
            </div>
          </div>
        </div>
        <?php
    }
    ?>
</div>

