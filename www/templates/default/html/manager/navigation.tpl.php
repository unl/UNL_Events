<?php foreach ($context->getCalendars() as $calendar): ?>
    <?php if ($context->getCalendar() && ($calendar->shortname == $context->getCalendar()->shortname)): ?>
        <div style="background: #CCCCCC;"><?php echo $calendar->name ?></div>
        <ul>
            <li><small><a href="<?php echo $calendar->getFrontendURL() ?>">Live Calendar</a></small></li>
            <li><small><a href="<?php echo $calendar->getEditURL() ?>">Edit Calendar Info</a></small></li>
            <li><small><a href="<?php echo $calendar->getSubscriptionsURL() ?>">Subscriptions</a></small></li>
            <li><small><a href="<?php echo $calendar->getUsersURL() ?>">Users &amp; Permissions</a></small></li>
        </ul>
    <?php else: ?>
        <a href="<?php echo $calendar->getManageURL() ?>"><?php echo $calendar->name ?></a><br>
    <?php endif; ?>
<?php endforeach; ?>
<br>
<a href="<?php echo $base_manager_url ?>calendar/new" class="wdn-button wdn-button-brand">+ New Calendar</a><br>
<br>
<a href="<?php echo $calendar->getFrontendURL() ?>">Account Info</a><br>
<a href="<?php echo $calendar->getFrontendURL() ?>">InDesign Tags Export</a><br>