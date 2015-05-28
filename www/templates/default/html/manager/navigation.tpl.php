<ul>
    <?php foreach ($context->getCalendars() as $calendar): ?>
        <li>
        <?php if ($context->getCalendar() && ($calendar->shortname == $context->getCalendar()->shortname)): ?>
            <a href="<?php echo $calendar->getManageURL() ?>"><?php echo $calendar->name ?></a>
            <ul>
                <li><a href="<?php echo $calendar->getFrontendURL() ?>">Live Calendar</a></li>
                <li><a href="<?php echo $base_manager_url . $calendar->shortname ?>/create/">New Event</a></li>
                <li><a href="<?php echo $calendar->getEditURL() ?>">Edit Calendar Info</a></li>
                <li><a href="<?php echo $calendar->getSubscriptionsURL() ?>">Subscriptions</a></li>
                <li><a href="<?php echo $calendar->getUsersURL() ?>">Users &amp; Permissions</a></li>
            </ul>
        <?php else: ?>
            <a href="<?php echo $calendar->getManageURL() ?>"><?php echo $calendar->name ?></a>
        <?php endif; ?>
         </li>
    <?php endforeach; ?>
   
    <li>
        <a href="<?php echo $base_manager_url ?>calendar/new" class="wdn-button wdn-button-brand">+ New Calendar</a>
    </li>
    <li>
        <a href="<?php echo $context->getEditAccountURL() ?>">Account Info</a>
    </li>
    <li>
        <a href="<?php echo $calendar->getFrontendURL() ?>">InDesign Tags Export</a>
    </li>
</ul>
