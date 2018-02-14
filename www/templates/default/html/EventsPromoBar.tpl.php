<?php
use UNL\UCBCN\Event\Occurrence;

$events = array(
    'withImage' => array(),
    'withOutImage' => array()
);

foreach($context->getRawObject() as $eventInstance) {
    if($eventInstance->getImageURL()) {
        $events['withImage'][] = $eventInstance;
    }
    else {
        $events['withOutImage'][] = $eventInstance;
    }
}

function sortEventsByRecurranceFlag($a, $b) {
    $a_rtype = $a->eventdatetime->recurringtype;
    $b_rtype = $b->eventdatetime->recurringtype;

    if(Occurrence::RECURRING_TYPE_NONE == $a_rtype && Occurrence::RECURRING_TYPE_NONE != $b_rtype) {
        return +1;
    }
    else if(Occurrence::RECURRING_TYPE_NONE == $b_rtype && Occurrence::RECURRING_TYPE_NONE != $a_rtype) {
        return -1;
    }
    else {
        return 0;
    }
}

usort($events['withImage'], "sortEventsByRecurranceFlag");
usort($events['withOutImage'], "sortEventsByRecurranceFlag");

$promoEvents = array();
$promoEvents = array_merge($events['withImage'], $events['withOutImage']);
?>
<?php if(sizeof($events) > 0): ?>
    <div class="wdn-band">
        <div class="bp640-wdn-grid-set-thirds">
            <?php
            for($i = 0; $i < sizeof($promoEvents) && $i < 3 ; $i++){
                ?>
                <div class="wdn-col">
                    <div class="wdn-grid-set">
                        <div class="wdn-col-one-third bp480-wdn-col-one-half bp640-wdn-col-full">
                            <img class="event_description_img" src="<?php if ($imageURL = $promoEvents[$i]->getImageURL()) echo $imageURL; else echo "//events.unl.edu/images/"?>" alt="image for event <?php echo $promoEvents[$i]->event->id; ?>" width="262" height="148"/>
                        </div>
                        <div class="wdn-col-two-thirds bp480-wdn-col-one-half bp640-wdn-col-full">
                            <h3 class="event-heading">
                                <a class="url summary" href="<?php echo $frontend->getEventURL($promoEvents[$i]) ?>">
                                    <?php echo $savvy->dbStringtoHtml($promoEvents[$i]->event->title) ?></a>
                            </h3>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
<?php endif; ?>
