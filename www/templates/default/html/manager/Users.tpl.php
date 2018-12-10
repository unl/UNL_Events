<?php
	$crumbs = new stdClass;
	$crumbs->crumbs = array(
		"Events Manager" => "/manager",
		$context->calendar->name => $context->calendar->getManageURL(),
		"Users & Permissions" => NULL
	);
	echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
	//$calendar = Calendar::getByShortname($this->options['calendar_shortname']);
?>

<h2 class="wdn-brand">Users on this Calendar</h2>
<div>
	<table>
		<thead>
			<tr>
				<th>User</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
	    <?php foreach($context->getUsers() as $user): ?>
	    <tr>
			<td>
	        	<?php echo $user->uid; ?>
	    	</td>
	    	<td class="small-center table-actions">
		        <a class="dcf-btn wdn-button-brand" href="<?php echo $user->getEditPermissionsURL($context->calendar) ?>">Edit Permissions</a>
		        <span class="small-hidden">|</span><br class="hidden small-block" /><br class="dcf-d-none small-block" />
		        <form method="post" action="<?php echo $user->getDeletePermissionsURL($context->calendar) ?>" class="delete-form">
                  <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
                  <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
	                <input type="hidden" name="user_uid" value="<?php echo $user->uid ?>" />
	                <button class="dcf-btn" type="submit">Remove</button>
	            </form>
            </td>
	    </tr>
	    </tbody>
	    <?php endforeach; ?>
	</table>
</div>
<br>

<a href="<?php echo $base_manager_url . $context->calendar->shortname ?>/users/new/" 
	class="dcf-btn wdn-button-brand">Add User
</a><br>