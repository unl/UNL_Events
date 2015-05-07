<?php echo $context->calendar->name ?>
 <a href="<?php echo $base_manager_url . $context->calendar->shortname ?>/users/new/" 
	class="wdn-button wdn-button-brand">+ Add a User
</a>

<h3>
Current Users on this Calendar
</h3>
<div>
	<div class="wdn-grid-set">
	    <div class="wdn-col-one-third">
	        <h6>User</h6>
	    </div>
    </div>
    <?php foreach($context->getUsers() as $user) { ?>
    <div class="wdn-grid-set">
	    <div class="wdn-col-one-third">
	        <?php echo $user->uid; ?>
	    </div>
	    <div class="wdn-col-two-thirds" style="text-align: right;">
	        <a class="wdn-button wdn-button-brand" href="<?php echo $user->getEditPermissionsURL($context->calendar) ?>">Edit Permissions</a>
	        |
	        <a class="wdn-button wdn-button-brand" href="#">Remove From Calendar</a>
	    </div>
    </div>
    <?php } ?>
</div>