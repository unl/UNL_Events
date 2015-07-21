<?php 
    $user = \UNL\UCBCN\Manager\Auth::getCurrentUser();
    $calendar = $context->getCalendar();
?>

<?php if ($calendar != NULL): ?>
<div class="toolbox">
    <h3><?php echo $calendar->name; ?></h3>
    <div class="tools">
        <div style="text-align: center; margin-bottom: .8em">
        <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_CREATE_ID, $calendar->id)): ?>
            <a class="wdn-button wdn-button-brand" href="<?php echo $base_manager_url . $calendar->shortname ?>/create/">
            <span style="font-size: 2em; vertical-align: middle; font-weight: 600">+</span>
            <span style="vertical-align: middle;">New Event</span>
            </a>
        <?php endif; ?>
        </div>
        <ul>
            <li><a href="<?php echo $calendar->getManageURL() ?>">Manage Calendar</a></li>
            <li><a href="<?php echo $calendar->getFrontendURL() ?>">Live Calendar</a></li>
            
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
</div>
<?php endif; ?>

<div class="toolbox">
    <h3>Tools</h3>
    <div class="tools">
        <div style="text-align: center; margin-bottom: .8em">
            <a class="wdn-button wdn-button-triad" href="<?php echo $base_manager_url ?>calendar/new">
            <span style="vertical-align: middle;">+</span>
            <span style="vertical-align: middle;">New Calendar</span>
            </a>
        </div>
        <ul>
            <li>
                <a href="<?php echo $context->getEditAccountURL() ?>">Account Info</a>
            </li>
            <li>
                <a target="_blank" href="http://www.github.com/unl/UNL_UCBCN_System/wiki">Get Help!</a>
            </li>
        </ul>
    </div>
</div>

<div class="toolbox">
    <h3>Your Calendars</h3>
    <div class="tools">
        <ul>
            <?php foreach ($context->getCalendars() as $calendar): ?>
                <li>
                    <a href="<?php echo $calendar->getManageURL() ?>"><?php echo $calendar->name ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
