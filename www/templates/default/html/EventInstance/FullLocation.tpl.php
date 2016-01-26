<?php 
$location = $context->eventdatetime->getLocation();
$room = !empty($context->eventdatetime->room) ? $context->eventdatetime->room : $location->room;
$directions = !empty($context->eventdatetime->directions) ? $context->eventdatetime->directions : $location->directions;
?>
<?php if (isset($location->name) || $room || $directions || isset($location->streetaddress1)): ?>
<div class="location eventicon-location">
<?php if (isset($location->mapurl)): ?>
    <a class="mapurl" href="<?php echo $location->mapurl ?>"><?php echo $location->name; ?></a>
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
