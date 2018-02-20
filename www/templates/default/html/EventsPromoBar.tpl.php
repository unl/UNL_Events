<?php
use UNL\UCBCN\Event\Occurrence;

$events = array(
    'with_image' => array(),
    'without_image' => array()
);

foreach($context->getRawObject() as $eventInstance) {
    if($eventInstance->getImageURL()) {
        $events['with_image'][] = $eventInstance;
    }
    else {
        $events['without_image'][] = $eventInstance;
    }
}

function sortEventsByRecurrenceFlag($a, $b) {
    $a_rtype = $a->eventdatetime->recurringtype;
    $b_rtype = $b->eventdatetime->recurringtype;

    if(Occurrence::RECURRING_TYPE_NONE == $a_rtype && Occurrence::RECURRING_TYPE_NONE != $b_rtype) {
        return 1;
    }
    else if(Occurrence::RECURRING_TYPE_NONE == $b_rtype && Occurrence::RECURRING_TYPE_NONE != $a_rtype) {
        return -1;
    }
    else {
        return 0;
    }
}

usort($events['with_image'], "sortEventsByRecurrenceFlag");
usort($events['without_image'], "sortEventsByRecurrenceFlag");
$grid_class = '';
switch (count($events['with_image'])) {
    case 1:
        $grid_class = 'wdn-col-one-half';
    break;
    case 2:
        $grid_class = 'wdn-col-one-third';
    break;
    default:
        $grid_class = 'wdn-col-one-fourth';
    break;
}
?>
<div class="band-results">
    <div class="section-heading">
        <h1>Today @ UNL</h1>
    </div>
</div>
<div class="wdn-grid-set" style="word-break: break-word;">
    <?php for ($i = 0; $i < count($events['with_image']) && $i < 3 ; $i++): ?>
        <div class="bp768-<?php echo $grid_class; ?> promo-box">
            <div class="wdn-grid-set">
                <div class="wdn-col-one-half bp768-wdn-col-full">
                    <a href="<?php echo $frontend->getEventURL($events['with_image'][$i]) ?>">
                        <img class="event_description_img" src="<?php if ($imageURL = $events['with_image'][$i]->getImageURL()) echo $imageURL; else echo "//events.unl.edu/images/"?>" alt="image for event <?php echo $events['with_image'][$i]->event->id; ?>" />
                    </a>
                </div>
                <div class="wdn-col-one-half bp768-wdn-col-full">
                    <h3 class="event-heading">
                        <a href="<?php echo $frontend->getEventURL($events['with_image'][$i]) ?>">
                            <?php echo $savvy->dbStringtoHtml($events['with_image'][$i]->event->title) ?>
                        </a>
                    </h3>
                </div>
            </div>
        </div>
    <?php endfor; ?>
    <div class="bp768-<?php echo $grid_class; ?> promo-non-image-link">
        <?php for ($i = 0; $i < count($events['without_image']) && $i < 3 ; $i++): ?>
        <h3 class="event-heading" style="border-bottom: 1px solid #CCC; padding-bottom: .354em;">
            <a href="<?php echo $frontend->getEventURL($events['without_image'][$i]) ?>">
                <?php echo $savvy->dbStringtoHtml($events['without_image'][$i]->event->title) ?>
            </a>
        </h3>
        <?php endfor; ?>
        <div style="margin-bottom: .5em;">
            <a style="font-family: 'Gotham SSm A','Gotham SSm B',Verdana,sans-serif;" href="<?php echo $frontend->getUpcomingURL(); ?>">...and more</a>
        </div>
    </div>
</div>
