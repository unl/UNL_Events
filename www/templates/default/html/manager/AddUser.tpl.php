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

<h1><?php echo $context->user == NULL ? 'Add a User' : 'Edit User Permissions' ?></h1>
<form class="dcf-form" action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <?php if ($context->user == NULL) { ?>
        <div class="dcf-form-group">
            <label for="user">User</label>
            <select id="user" name="user">
                <?php foreach($context->getAvailableUsers() as $user) { ?>
                  <option value="<?php echo $user->uid ?>"><?php echo $user->uid ?></option>
              <?php } ?>
            </select>
        </div>
        <p class="dcf-form-help">Note: This is a list of users who have previously logged into the system. If you do not see someone in this list, please have him or her navigate to <a href="http://events.unl.edu/manager">http://events.unl.edu/manager</a> and login. His or her username will then be present in this list.</p>
    <?php } else { ?>
        <p>Editing for User:</label><br>
        <strong><?php echo $context->user->uid ?></strong></p>
    <?php } ?>

    <fieldset>
      <legend>Permissions</legend>
      <?php foreach ($context->getAllPermissions() as $permission) { ?>
          <div class="dcf-input-checkbox">
            <input id="permission-<?php echo $permission->id ?>" name="permission_<?php echo $permission->id ?>" type="checkbox"
            <?php if (($context->user != NULL && $context->user->hasPermission($permission->id, $context->calendar->id)) ||
                ($context->user == NULL && $permission->standard)) echo 'checked="checked"'; ?>>
            <label for="permission-<?php echo $permission->id ?>"><?php echo $permission->description ?></label>
          </div>
      <?php } ?>
    </fieldset>
    <button class="dcf-btn dcf-btn-primary" type="submit"><?php echo $context->user == NULL ? 'Add User' : 'Update User Permissions' ?></button>
</form>
