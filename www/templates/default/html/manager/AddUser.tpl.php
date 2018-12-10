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

<h2 class="wdn-brand"><?php echo $context->user == NULL ? 'Add a User' : 'Edit User Permissions' ?></h2>
<form action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <?php if ($context->user == NULL) { ?>
        <label class="dcf-label" for="user">User</label>
        <div class="dcf-input-select">
          <select id="user" name="user">
          <?php foreach($context->getAvailableUsers() as $user) { ?>
              <option value="<?php echo $user->uid ?>"><?php echo $user->uid ?></option>
          <?php } ?>
          </select>
        </div>
        <div class="visual-island dcf-mt-4">
            <div class="details">
                Note: This is a list of users who have previously logged into the system.  
                If you do not see someone in this list, please have him or her navigate to 
                <a href="http://events.unl.edu/manager">http://events.unl.edu/manager</a> and login. His or her
                username will then be present in this list.
            </div>
        </div>
    <?php } else { ?>
        <label class="dcf-label">Editing for User:</label><br><strong><?php echo $context->user->uid ?></strong>
    <?php } ?>

    <fieldset>
      <label class="dcf-label">Permissions</label><br>
      <?php foreach ($context->getAllPermissions() as $permission) { ?>
          <div class="dcf-form-group">
            <input class=""dcf-input-control"
            <?php if (($context->user != NULL && $context->user->hasPermission($permission->id, $context->calendar->id)) ||
                ($context->user == NULL && $permission->standard)) echo 'checked="checked"'; ?>
             type="checkbox" name="permission_<?php echo $permission->id ?>" id="permission-<?php echo $permission->id ?>">
            <label class="dcf-label" for="permission-<?php echo $permission->id ?>"><?php echo $permission->description ?></label>
          </div>
      <?php } ?>
    </fieldset>
    <button class="dcf-btn wdn-button-brand" type="submit"><?php echo $context->user == NULL ? 'Add User' : 'Update User Permissions' ?></button>
</form>
