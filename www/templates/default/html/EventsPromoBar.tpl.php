<?php
use UNL\UCBCN\Event\Occurrence;

$events = array(
    'with_image' => array(),
    'without_image' => array()
);

foreach($context->getRawObject() as $eventInstance) {
    if ($eventInstance->getImageURL()) {
        $events['with_image'][] = $eventInstance;
    } else {
        $events['without_image'][] = $eventInstance;
    }
}

function sortEventsByRecurrenceFlag($a, $b) { # also sort by ongoing
    $a_rtype = $a->eventdatetime->recurringtype;
    $b_rtype = $b->eventdatetime->recurringtype;

    if (Occurrence::RECURRING_TYPE_NONE == $a_rtype && Occurrence::RECURRING_TYPE_NONE != $b_rtype || !$a->isOngoing() && $b->isOngoing()) {
        return -1;
    } else if (Occurrence::RECURRING_TYPE_NONE == $b_rtype && Occurrence::RECURRING_TYPE_NONE != $a_rtype || $a->isOngoing() && !$b->isOngoing()) {
        return 1;
    } else {
        return 0;
    }
}

function sortEventsByPromotedDate($a, $b) {
    $a = $a->event->promoted;
    $b = $b->event->promoted;

    if ($a > $b) {
        return -1;
    } else if ($b > $a) {
        return 1;
    }
    return 0;
}
#remove events that have promoted = "hide"
$events['with_image'] = array_filter($events['with_image'], function ($event) {
    return $event->event->promoted != 'hide';
});
$events['without_image'] = array_filter($events['without_image'], function ($event) {
    return $event->event->promoted != 'hide';
});
usort($events['with_image'], "sortEventsByRecurrenceFlag");
usort($events['without_image'], "sortEventsByRecurrenceFlag");
usort($events['with_image'], "sortEventsByPromotedDate");
usort($events['without_image'], "sortEventsByPromotedDate");
$grid_class = '';
$grid_count = 0;
switch (count($events['with_image'])) {
    case 4:
    case 2:
        $grid_class = 'wdn-col-one-half';
        $grid_count = 2;
    break;
    case 1:
        $grid_class = 'wdn-col-one-half centered';
        $grid_count = 1;
        break;
    default:
        $grid_class = 'wdn-col-one-third';
        $grid_count = 3;
    break;
}
?>
<div id="events-promo-bar">
    <div class="band-results">
        <div class="section-heading">
            <h2>Today @ UNL</h2>
        </div>
    </div>
    <div class="wdn-grid-set break-word">
        <div class="bp768-wdn-col-three-fourths">
        <div class="wdn-grid-set">
        <?php for ($k = 0; $k < $grid_count; $k++): ?>
            <div class="bp768-<?php echo $grid_class; ?> promo-box">
                <div class="wdn-grid-set">
                    <?php for ($i = $k*(count($events['with_image'])<=3?1:2); $i < count($events['with_image']) && $i < 6 && $i < (count($events['with_image'])<=3?$k+1:$k*2+2); $i++): ?>
                    <div class="clearfix">
                    <div class="wdn-col-one-half bp768-wdn-col-full promo-bar-container">
                        <div class="promo-box-image-barrier">
                            <a href="<?php echo $frontend->getEventURL($events['with_image'][$i]) ?>">
                                <img class="event_description_img" src="<?php if ($imageURL = $events['with_image'][$i]->getImageURL()) echo $imageURL; else echo "//events.unl.edu/images/"?>" alt="image for event <?php echo $events['with_image'][$i]->event->id; ?>" />
                            </a>
                        </div>
                    </div>
                    <div class="wdn-col-one-half bp768-wdn-col-full promo-bar-container">
                        <h3 class="event-heading">
                            <a href="<?php echo $frontend->getEventURL($events['with_image'][$i]) ?>">
                                <?php echo $savvy->dbStringtoHtml($events['with_image'][$i]->event->title) ?>
                            </a>
                        </h3>
                    </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endfor; ?>
        </div>
        </div>
        <div class="bp768-wdn-col-one-fourth promo-non-image-link">
            <?php for ($i = 0; $i < count($events['without_image']) && $i < 6; $i++): ?>
            <h3 class="event-heading promo-box-event-listing">
                <a href="<?php echo $frontend->getEventURL($events['without_image'][$i]) ?>">
                    <?php echo $savvy->dbStringtoHtml($events['without_image'][$i]->event->title) ?>
                </a>
            </h3>
            <?php endfor; ?>
            <div style="margin-bottom: .5em;">
                <a class="sans-serif" href="<?php echo $frontend->getUpcomingURL(); ?>">...and more</a>
            </div>
        </div>
    </div>
</div>