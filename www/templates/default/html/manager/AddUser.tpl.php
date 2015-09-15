<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        "Users & Permissions" => $context->calendar->getUsersURL(),
        $context->user == NULL ? 'Add a User' : 'Edit User Permissions' => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>

<h1 class="wdn-brand"><?php echo $context->user == NULL ? 'Add a User' : 'Edit User Permissions' ?></h1>
<form action="" method="POST">
    <?php if ($context->user == NULL) { ?>
        <label for="user">User</label>
        <select id="user" name="user">
        <?php foreach($context->getAvailableUsers() as $user) { ?>
            <option value="<?php echo $user->uid ?>"><?php echo $user->uid ?></option>
        <?php } ?>
        </select>
        <div class="visual-island">
            <div class="details">
                Note: This is a list of users who have previously logged into the system.  If you do not see someone in this list, please have them log into the system so that their account is created.
            </div>
        </div>
    <?php } else { ?>
        <label>Editing for User:</label><br><strong><?php echo $context->user->uid ?></strong>
    <?php } ?>
    <br><br>
    <label>Permissions</label><br>
    <?php foreach ($context->getAllPermissions() as $permission) { ?>
        <input
        <?php if (($context->user != NULL && $context->user->hasPermission($permission->id, $context->calendar->id)) ||
            ($context->user == NULL && $permission->standard)) echo 'checked="checked"'; ?>
         type="checkbox" name="permission_<?php echo $permission->id ?>" id="permission-<?php echo $permission->id ?>"> 
        <label for="permission-<?php echo $permission->id ?>"><?php echo $permission->description ?></label>
        <br>
    <?php } ?>
    <br>
    <button class="wdn-button wdn-button-brand" type="submit"><?php echo $context->user == NULL ? 'Add User' : 'Update User Permissions' ?></button>
</form>
