<?php $url = $context->getURL(); ?>
<?php $microdata_check = $context->microdata_check(); ?>
<div class="event_cal">
    <div class='vcalendar'>
        <div class='vevent'>

            <?php if($microdata_check): ?>
                <script type="application/ld+json">
                    <?php $event_microdata = $context->getFormattedMicrodata(); ?>
                    <?php $raw_microdata = $savvy->getRawObject($event_microdata); ?>
                    <?php echo json_encode($raw_microdata, JSON_UNESCAPED_SLASHES); ?>
                </script>
            <?php endif; ?>

            <?php if (isset($context->event->subtitle)): ?><header><?php endif; ?>
                <h2 id="heading-date" class='summary' data-datetime="<?php echo (new DateTime($context->getStartTime()))->format('c') ?>">
                    <a class="dcf-txt-decor-none" href="<?php echo $url; ?>">
                        <?php echo $savvy->dbStringtoHtml($context->event->displayTitle($context)); ?>
                    </a>
                </h2>
                <?php if (isset($context->event->subtitle)): ?><p class="dcf-subhead"><?php echo $savvy->dbStringtoHtml($context->event->subtitle) ?></p><?php endif; ?>
            <?php if (isset($context->event->subtitle)): ?></header><?php endif; ?>
            <?php echo $savvy->render($context, 'EventInstance/Date.tpl.php') ?>
            <?php echo $savvy->render($context, 'EventInstance/FullLocation.tpl.php') ?>
            <?php echo $savvy->render($context, 'EventInstance/Contact.tpl.php') ?>
            <div class="description">
                <?php
                $description = $savvy->dbStringtoHtml($context->event->description);
                $description = $savvy->linkify($description);
                echo $description;
                ?>
            </div>
            <?php if (!empty($context->eventdatetime->additionalpublicinfo)): ?>
            <p class="public-info">
              Additional Public Info:<br>
                <?php
                $publicInfo = $savvy->dbStringtoHtml($context->eventdatetime->additionalpublicinfo);
                $publicInfo = $savvy->linkify($publicInfo);
                echo $publicInfo;
                ?>
            </p>
            <?php endif; ?>
            <?php if (isset($context->event->webpageurl)): ?>
            <p class="website">
                <a class="url external" href="<?php echo $context->event->webpageurl ?>"><?php echo $context->event->webpageurl ?></a>
            </p>
            <?php endif; ?>
            <?php if ($imageURL = $context->getImageURL()): ?>
            <img class="event_description_img" src="<?php echo $imageURL ?>" aria-hidden="true" alt="">
            <?php endif; ?>
            <p class="download">
                <a class="dcf-btn dcf-btn-primary" href="<?php echo $url ?>.ics">Download this event to my calendar</a>
            </p>

            <?php echo $savvy->render($context, 'EventInstance/OriginCalendar.tpl.php') ?>

            <?php echo $savvy->render($context, 'EventInstance/EditButton.tpl.php') ?>
        </div>
    </div>
</div>
