<?php 
    $user = \UNL\UCBCN\Manager\Auth::getCurrentUser();
    $calendar = $context->getCalendar();
?>

<?php if ($calendar != NULL): ?>
<div class="toolbox">
<h3 class="wdn-brand"><?php echo $calendar->name; ?></h3>
<ul>
    <li><a href="<?php echo $calendar->getManageURL() ?>">Manage Calendar</a></li>
    <li><a href="<?php echo $calendar->getFrontendURL() ?>">Live Calendar</a></li>
    
    <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_CREATE_ID, $calendar->id)): ?>
        <li><a href="<?php echo $base_manager_url . $calendar->shortname ?>/create/">New Event</a></li>
    <?php endif; ?>

    <?php if ($user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_ID, $calendar->id)): ?>
        <li><a href="<?php echo $calendar->getEditURL() ?>">Edit Calendar Info</a></li>
    <?php endif; ?>

    <?php if ($user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_SUBSCRIPTIONS_ID, $calendar->id)): ?>
        <li><a href="<?php echo $calendar->getSubscriptionsURL() ?>">Subscriptions</a></li>
    <?php endif; ?>

    <?php if ($user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_PERMISSIONS_ID, $calendar->id)): ?>
        <li><a href="<?php echo $calendar->getUsersURL() ?>">Users &amp; Permissions</a></li>
    <?php endif; ?>
</ul>
</div>
<?php endif; ?>

<div class="toolbox">
<h3 class="wdn-brand">Tools</h3>
<ul>
    <li>
        <a href="<?php echo $base_manager_url ?>calendar/new">New Calendar</a>
    </li>
    <li>
        <a href="<?php echo $context->getEditAccountURL() ?>">Account Info</a>
    </li>
    <li>
        <a href="/help">Get Help!</a>
    </li>
</ul>
</div>

<div class="toolbox">
<h3 class="wdn-brand">My Calendars</h3>
<ul>
    <?php foreach ($context->getCalendars() as $calendar): ?>
        <li>
            <a href="<?php echo $calendar->getManageURL() ?>"><?php echo $calendar->name ?></a>
        </li>
    <?php endforeach; ?>
</ul>
</div>
