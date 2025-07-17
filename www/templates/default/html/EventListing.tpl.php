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
          <?php if ($eventinstance->getImageURL()) { ?>
            <div class="dcf-d-grid dcf-grid-cols-12 dcf-row-gap-4">
              <div class="dcf-col-span-12 dcf-col-span-9@sm">
                  <?php echo $savvy->render($eventinstance, 'EventInstance/Date.tpl.php') ?>
                  <?php echo $savvy->render($eventinstance, 'EventInstance/Location.tpl.php') ?>
                  <?php echo $savvy->render($eventinstance, 'EventInstance/TargetAudience.tpl.php') ?>
                  <?php echo $savvy->render($eventinstance, 'EventInstance/Description.tpl.php') ?>
                  <?php echo $savvy->render($eventinstance, 'EventInstance/OriginCalendar.tpl.php') ?>
                  <?php echo $savvy->render($eventinstance, 'EventInstance/EditButton.tpl.php') ?>
              </div>
              <div class="dcf-col-span-12 dcf-col-span-3@sm">
                  <?php echo $savvy->render($eventinstance, 'EventInstance/Thumbnail.tpl.php') ?>
              </div>
            </div>
          <?php } else { ?>
              <?php echo $savvy->render($eventinstance, 'EventInstance/Date.tpl.php') ?>
              <?php echo $savvy->render($eventinstance, 'EventInstance/Location.tpl.php') ?>
              <?php echo $savvy->render($eventinstance, 'EventInstance/TargetAudience.tpl.php') ?>
              <?php echo $savvy->render($eventinstance, 'EventInstance/Description.tpl.php') ?>
              <?php echo $savvy->render($eventinstance, 'EventInstance/OriginCalendar.tpl.php') ?>
	          <?php echo $savvy->render($eventinstance, 'EventInstance/EditButton.tpl.php') ?>
          <?php } // end else ?>
        </div>
        <?php
    }
    ?>
</div>
