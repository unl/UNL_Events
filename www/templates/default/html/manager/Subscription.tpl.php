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
<?php foreach($context->getSubscriptions() as $subscription): ?>
<form id="delete-subscription-<?php echo $subscription->id ?>" method="POST" action="<?php echo $subscription->getDeleteURL() ?>" class="delete-form dcf-d-none">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <input type="hidden" name="subscription_id" value="<?php echo $subscription->id ?>" />
    <button type="submit">Submit</button>
</form>
<?php endforeach; ?>
<h1>Current Subscriptions</h1>
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
                <td class="small-center table-actions">
                    <a class="dcf-btn dcf-btn-primary" href="<?php echo $subscription->getEditURL() ?>">Edit</a>
                    <span class="small-hidden">|</span><br class="dcf-d-none small-block"><br class="dcf-d-none small-block">
                    <button class="dcf-btn dcf-btn-secondary" form="delete-subscription-<?php echo $subscription->id ?>" type="submit">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<br>
<?php else: ?>
    <p>There are currently no subscriptions on this calendar.</p>
<?php endif; ?>
<a class="dcf-btn dcf-btn-primary" href="<?php echo $base_manager_url . $context->calendar->shortname ?>/subscriptions/new/">Add Subscription</a>
