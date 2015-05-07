<?php echo $context->calendar->name ?> &gt; Add a User

<div class="wdn-grid-set">
    <form action="" method="POST">
        <fieldset>
            <label for="user">User</label>
            <select id="user" name="user">
            <?php foreach($context->getAvailableUsers() as $user) { ?>
                <option value="<?php echo $user->uid ?>"><?php echo $user->uid ?></option>
            <?php } ?>
            </select>

            <?php foreach ($context->getAllPermissions() as $permission) { ?>
                <input type="checkbox" name="permission_<?php echo $permission->id ?>" id="permission-<?php echo $permission->id ?>"> 
                <label for="permission-<?php echo $permission->id ?>"><?php echo $permission->description ?></label>
                <br>
            <?php } ?>

            <button class="wdn-button wdn-button-brand" type="submit">Add User</button>
        </fieldset>
    </form>
</div>