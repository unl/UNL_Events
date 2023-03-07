<?php 
$location = $context->eventdatetime->getLocation();

if (isset($location)) :

$locationName = isset($location->name) ? $location->name : '';
$locationRoom = isset($location->room) ? $location->room : '';
$locationDirections = isset($location->directions) ? $location->directions : '';
$room = !empty($context->eventdatetime->room) ? $context->eventdatetime->room : $locationRoom;
$directions = !empty($context->eventdatetime->directions) ? $context->eventdatetime->directions : $locationDirections;
?>
<?php if ($locationName || $room || $directions || isset($location->streetaddress1)): ?>
<div class="location">
    <svg class="dcf-mr-1 dcf-h-4 dcf-w-4 dcf-fill-current" aria-hidden="true" focusable="false" height="24" width="24" viewBox="0 0 24 24">
        <path d="M12 0C7.589 0 4 3.589 4 8c0 4.245 7.273 15.307 7.583 15.775a.497.497 0 00.834 0C12.727 23.307 20 12.245 20 8c0-4.411-3.589-8-8-8zm0 22.58C10.434 20.132 5 11.396 5 8c0-3.86 3.14-7 7-7s7 3.14 7 7c0 3.395-5.434 12.132-7 14.58z"></path>
        <path d="M12 4.5c-1.93 0-3.5 1.57-3.5 3.5s1.57 3.5 3.5 3.5 3.5-1.57 3.5-3.5-1.57-3.5-3.5-3.5zm0 6c-1.378 0-2.5-1.122-2.5-2.5s1.122-2.5 2.5-2.5 2.5 1.122 2.5 2.5-1.122 2.5-2.5 2.5z"></path>
    </svg>
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
<?php if (isset($location->additionalpublicinfo)): ?>
    <div class="additionalinfo">Additional Info: <?php echo $savvy->dbStringtoHtml($location->additionalpublicinfo) ?></div>
<?php endif ?>
</div>
<?php endif; ?>
<?php endif; ?>
