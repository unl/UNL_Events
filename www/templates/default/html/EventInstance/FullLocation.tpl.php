<?php
if (isset($context->eventdatetime->location_id)) :
$location = $context->eventdatetime->getLocation();
$locationName = isset($location->name) ? $location->name : '';
$locationRoom = isset($location->room) ? $location->room : '';
$locationDirections = isset($location->directions) ? $location->directions : '';
$locationAdditional = isset($location->additionalpublicinfo) ? $location->additionalpublicinfo : '';

$room = !empty($context->eventdatetime->room) ? $context->eventdatetime->room : $locationRoom;
$directions = !empty($context->eventdatetime->directions) ? $context->eventdatetime->directions : $locationDirections;
$location_additional_info = !empty($context->eventdatetime->location_additionalpublicinfo) ? $context->eventdatetime->location_additionalpublicinfo : $locationAdditional;
?>
    <?php if ($locationName || $room || $directions || isset($location->streetaddress1)): ?>
        <div class="location">
            <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
                <path d="M12 0C7.589 0 4 3.589 4 8c0 4.245 7.273 15.307 7.583 15.775a.497.497 0 00.834 0C12.727 23.307 20 12.245 20 8c0-4.411-3.589-8-8-8zm0 22.58C10.434 20.132 5 11.396 5 8c0-3.86 3.14-7 7-7s7 3.14 7 7c0 3.395-5.434 12.132-7 14.58z"></path>
                <path d="M12 4.5c-1.93 0-3.5 1.57-3.5 3.5s1.57 3.5 3.5 3.5 3.5-1.57 3.5-3.5-1.57-3.5-3.5-3.5zm0 6c-1.378 0-2.5-1.122-2.5-2.5s1.122-2.5 2.5-2.5 2.5 1.122 2.5 2.5-1.122 2.5-2.5 2.5z"></path>
            </svg>
            <span>
                <?php if (isset($location->mapurl) && filter_var($location->mapurl, FILTER_VALIDATE_URL)): ?>
                    <a class="mapurl" href="<?php echo $location->mapurl ?>"><?php echo $location->name; ?></a>
                <?php elseif (isset($location->webpageurl) && filter_var($location->webpageurl, FILTER_VALIDATE_URL)): ?>
                    <a class="webpageurl" href="<?php echo $location->webpageurl ?>"><?php echo $location->name; ?></a>
                <?php else: ?>
                    <?php echo $location->name; ?>
                <?php endif; ?>
                <?php if (!empty($room)): ?>
                    <span class="room">Room: <?php echo $room ?></span>
                <?php endif; ?>
                <?php if (isset($location->streetaddress1)): ?>
                    <div class="adr">
                        <span class="street-address"><?php echo $savvy->dbStringtoHtml($location->streetaddress1 . "\n" . $location->streetaddress2) ?></span>
                        <?php if (isset($location->city)): ?>
                            <span class="locality"><?php echo $savvy->dbStringtoHtml($location->city) ?></span>
                        <?php endif; ?>
                        <?php if (isset($location->state)): ?>
                            <span class="region"><?php echo $savvy->dbStringtoHtml($location->state) ?></span>
                        <?php endif; ?>
                        <?php if (isset($location->zip)): ?>
                            <span class="postal-code"><?php echo $savvy->dbStringtoHtml($location->zip) ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($directions)): ?>
                    <div class="directions">Directions: <?php echo $savvy->dbStringtoHtml($directions) ?></div>
                <?php endif ?>
                <?php if (!empty($location_additional_info)): ?>
                    <div class="additionalinfo">Additional Info: <?php echo $savvy->dbStringtoHtml($location_additional_info) ?></div>
                <?php endif ?>
            </span>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($context->eventdatetime->webcast_id) && $context->eventdatetime->webcast_id): ?>
    <?php
        $webcast = $context->eventdatetime->getWebcast();
        $webcastAdditional = isset($webcast->additionalinfo) ? $webcast->additionalinfo : '';
        $webcast_additional_info = !empty($context->eventdatetime->webcast_additionalpublicinfo) ? $context->eventdatetime->webcast_additionalpublicinfo : $webcastAdditional;
    ?>
    <?php $webcast = $context->eventdatetime->getWebcast(); ?>
    <div class="location">
        <svg xmlns="http://www.w3.org/2000/svg" class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
            <path d="M22,1H2C0.897,1,0,1.937,0,3.088v14.824C0,19.063,0.897,20,2,20h9.5v1H5c-0.276,0-0.5,0.224-0.5,0.5S4.724,22,5,22h14 c0.276,0,0.5-0.224,0.5-0.5S19.276,21,19,21h-6.5v-1H22c1.103,0,2-0.937,2-2.088V3.088C24,1.937,23.103,1,22,1z M2,2h20 c0.551,0,1,0.488,1,1.088V15H1V3.088C1,2.488,1.449,2,2,2z M22,19H2c-0.551,0-1-0.488-1-1.088V16h22v1.912 C23,18.512,22.551,19,22,19z"></path>
            <path d="M12,16.5c-0.551,0-1,0.448-1,1s0.449,1,1,1s1-0.448,1-1S12.551,16.5,12,16.5z M12,17.5L12,17.5h0.5H12z"></path>
            <g><path fill="none" d="M0 0H24V24H0z"></path></g>
        </svg>
        <span>
            <span class="dcf-sr-only">Virtual Location:</span>
            <a href="<?php echo $webcast->url; ?>"><?php echo $webcast->title; ?></a>
            <?php if (isset($webcast_additional_info) && !empty($webcast_additional_info)): ?>
                <div class="additionalinfo">Additional Info: <?php echo $savvy->dbStringtoHtml($webcast_additional_info) ?></div>
            <?php endif ?>
        </span>
    </div>
<?php endif; ?>
