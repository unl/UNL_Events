<?php
	$crumbs = new stdClass;
	$crumbs->crumbs = array(
		"Events Manager" => "/manager",
		$context->calendar->name => $context->calendar->getManageURL(),
		"Subscriptions" => NULL
	);
	echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>

<?php if (count($context->getSubscriptions()) > 0): ?>
<h3>
	Current Subscriptions
</h3>
<div>
	<table>
		<thead>
			<tr>
				<th>Title</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
	    <?php foreach($context->getSubscriptions() as $subscription): ?>
			<tr>
				<td>
		        	<?php echo $subscription->name; ?>
				</td>
				<td>
			        <a class="wdn-button wdn-button-brand" href="<?php echo $subscription->getEditURL() ?>">Edit</a>
			        |
			        <form method="POST" action="<?php echo $subscription->getDeleteURL() ?>" class="delete-form">
		                <input type="hidden" name="subscription_id" value="<?php echo $subscription->id ?>" />
		                <button type="submit">Delete</button>
		            </form>
				</td>
			</tr>
	    <?php endforeach; ?>
		</tbody>
	</table>
</div>
<br>
<?php endif; ?>

<a href="<?php echo $base_manager_url . $context->calendar->shortname ?>/subscriptions/new/" 
	class="wdn-button wdn-button-brand">+ Add a Subscription
</a>