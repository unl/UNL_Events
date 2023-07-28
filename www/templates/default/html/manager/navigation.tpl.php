<?php
    $user = \UNL\UCBCN\Manager\Auth::getCurrentUser();
    $calendar = $context->getCalendar();
?>

<?php if ($calendar != NULL): ?>
<div class="toolbox">
    <h3><?php echo $calendar->name; ?></h3>
    <div class="tools">
        <div class="dcf-mb-3 dcf-txt-center">
        <?php if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_CREATE_ID, $calendar->id)): ?>
            <a
                class="dcf-btn dcf-btn-primary"
                href="<?php echo $base_manager_url . $calendar->shortname ?>/create/"
            >+ New Event</a>
        <?php endif; ?>
        </div>
        <ul class="dcf-list-bare dcf-txt-sm">
            <li><a class="dcf-txt-decor-hover" href="<?php echo $calendar->getManageURL() ?>">Manage Calendar</a></li>
            <li><a class="dcf-txt-decor-hover" href="<?php echo $calendar->getFrontendURL() ?>">Live Calendar</a></li>

            <?php if ($user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_ID, $calendar->id)): ?>
                <li>
                    <a class="dcf-txt-decor-hover" href="<?php echo $calendar->getEditURL() ?>">Edit Calendar Info</a>
                </li>
            <?php endif; ?>

            <?php if ($user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_SUBSCRIPTIONS_ID, $calendar->id)): ?>
                <li>
                    <a
                        class="dcf-txt-decor-hover"
                        href="<?php echo $calendar->getSubscriptionsURL() ?>"
                    >Subscriptions</a>
                </li>
            <?php endif; ?>

            <?php if ($user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_PERMISSIONS_ID, $calendar->id)): ?>
                <li>
                    <a
                        class="dcf-txt-decor-hover"
                        href="<?php echo $calendar->getUsersURL() ?>"
                    >Users &amp; Permissions</a>
                </li>
            <?php endif; ?>

            <?php
                if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_CREATE_ID, $calendar->id) &&
                    $user->hasPermission(\UNL\UCBCN\Permission::EVENT_EDIT_ID, $calendar->id)
                ):
            ?>
                <li>
                    <a
                        class="dcf-txt-decor-hover"
                        href="<?php echo $calendar->getLocationURL(); ?>"
                    >Calendar Saved Locations</a>
                </li>
            <?php endif; ?>

            <?php
                if ($user->hasPermission(\UNL\UCBCN\Permission::EVENT_CREATE_ID, $calendar->id) &&
                    $user->hasPermission(\UNL\UCBCN\Permission::EVENT_EDIT_ID, $calendar->id)
                ):
            ?>
                <li>
                    <a
                        class="dcf-txt-decor-hover"
                        href="<?php echo $calendar->getVirtualLocationURL(); ?>"
                    >Calendar Saved Virtual Locations</a>
                </li>
            <?php endif; ?>

            <?php if ($user->hasPermission(\UNL\UCBCN\Permission::CALENDAR_EDIT_PERMISSIONS_ID, $calendar->id)): ?>
                <li><a class="dcf-txt-decor-hover" href="<?php echo $base_manager_url ?>logout">Logout</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<div class="toolbox">
    <h3>Tools</h3>
    <div class="tools">
        <div class="dcf-mb-3 dcf-txt-center">
            <a class="dcf-btn dcf-btn-secondary" href="<?php echo $base_manager_url ?>calendar/new">+ New Calendar</a>
        </div>
        <ul class="dcf-list-bare dcf-txt-sm">
            <li>
                <a class="dcf-txt-decor-hover" href="<?php echo $context->getWelcomeURL() ?>">Welcome</a>
            </li>
            <li>
                <a class="dcf-txt-decor-hover" href="<?php echo $context->getEditMeURL() ?>">User Info</a>
            </li>
            <li>
                <a class="dcf-txt-decor-hover" href="<?php echo $context->getUserLocationURL(); ?>">
                    Your Saved Locations
                </a>
            </li>
            <li>
                <a
                    class="dcf-txt-decor-hover"
                    href="<?php echo $context->getUserVirtualLocationURL(); ?>"
                >Your Saved Virtual Locations</a>
            </li>
            <li>
                <a
                    class="dcf-txt-decor-hover"
                    href="<?php echo $context->getCalendarLookupURL(); ?>"
                >Calendar Lookup</a>
            </li>
            <li>
                <a class="dcf-txt-decor-hover" href="https://www.github.com/unl/UNL_UCBCN_System/wiki">Get Help!</a>
            </li>
        </ul>
    </div>
</div>

<div class="toolbox">
    <h3>Your Calendars</h3>
    <div class="tools">
        <ul class="dcf-list-bare dcf-txt-sm">
            <?php foreach ($context->getCalendars() as $calendar): ?>
                <li>
                    <a
                        class="dcf-txt-decor-hover"
                        href="<?php echo $calendar->getManageURL() ?>"
                    ><?php echo $calendar->name ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
