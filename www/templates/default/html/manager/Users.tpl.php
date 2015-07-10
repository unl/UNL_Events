<?php
	$crumbs = new stdClass;
	$crumbs->crumbs = array(
		"Events Manager" => "/manager",
		$context->calendar->name => $context->calendar->getManageURL(),
		"Users & Permissions" => NULL
	);
	echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>

<h3>
	Users on this Calendar
</h3>
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
	    	<td>
		        <a class="wdn-button wdn-button-brand" href="<?php echo $user->getEditPermissionsURL($context->calendar) ?>">Edit Permissions</a>
		        |
		        <form method="post" action="<?php echo $user->getDeletePermissionsURL($context->calendar) ?>" class="delete-form">
	                <input type="hidden" name="user_uid" value="<?php echo $user->uid ?>" />
	                <button type="submit">Remove</button>
	            </form>
            </td>
	    </tr>
	    </tbody>
	    <?php endforeach; ?>
	</table>
</div>
<br>

<a href="<?php echo $base_manager_url . $context->calendar->shortname ?>/users/new/" 
	class="wdn-button wdn-button-brand">Add User
</a>