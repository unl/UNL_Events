<?php $url = $context->getURL(); ?>
<div class="event_cal">
<div class='vcalendar'>
	<div class='vevent'>
	    <a class="dcf-txt-decor-none" href="<?php echo $url; ?>">
		<h2 class='summary' data-datetime="<?php echo (new DateTime($context->getStartTime()))->format('c') ?>">
		    <?php echo $savvy->dbStringtoHtml($context->event->title); ?>
		    <?php if (isset($context->event->subtitle)): ?><span class="dcf-subhead"><?php echo $savvy->dbStringtoHtml($context->event->subtitle) ?></span><?php endif; ?>
	    </h2>
	    </a>
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
	    <?php if (isset($context->eventdatetime->additionalpublicinfo)): ?>
	    <p class="public-info">
	        Additional Public Info:<br />
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
			<figure><img class="event_description_img" src="<?php echo $imageURL ?>" alt="image for event <?php echo $context->event->id; ?>" /></figure>
		<?php endif; ?>
        <p class="download">
			<a class="dcf-btn dcf-btn-primary" href="<?php echo $url ?>.ics">Download this event to my calendar</a>
		</p>
	</div>
</div>
</div>
