<?php echo $context->calendar->name ?> &gt; <?php echo $context->user == NULL ? 'Add a User' : 'Edit User Permissions' ?>

<div class="wdn-grid-set">
    <form action="" method="POST">
        <fieldset>
            <?php if ($context->user == NULL) { ?>
                <label for="user">User</label>
                <select id="user" name="user">
                <?php foreach($context->getAvailableUsers() as $user) { ?>
                    <option value="<?php echo $user->uid ?>"><?php echo $user->uid ?></option>
                <?php } ?>
                </select>
            <?php } else { ?>
                <h4><?php echo $context->user->uid ?></h4>
            <?php } ?>

            <?php foreach ($context->getAllPermissions() as $permission) { ?>
                <input
                <?php if ($context->user != NULL && $context->user->hasPermission($permission->id, $context->calendar->id)) echo 'checked="checked"'; ?>
                 type="checkbox" name="permission_<?php echo $permission->id ?>" id="permission-<?php echo $permission->id ?>"> 
                <label for="permission-<?php echo $permission->id ?>"><?php echo $permission->description ?></label>
                <br>
            <?php } ?>

            <button class="wdn-button wdn-button-brand" type="submit"><?php echo $context->user == NULL ? 'Add User' : 'Update User Permissions' ?></button>
        </fieldset>
    </form>
</div>