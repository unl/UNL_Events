<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        'Delete Calendar' => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<?php
	$user = \UNL\UCBCN\Manager\Auth::getCurrentUser();
?>

<h2 class="wdn-brand">Delete <?php echo $context->calendar->name ?></h2>
<h3 class="wdn-brand">Are you sure?</h3>
<p>Deleting this calendar will delete all of the events associated with it. Permanently.</p>
<form method="POST" action="<?php echo $context->calendar->getDeleteFinalURL() ?>" class="delete-form">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <button class="dcf-btn dcf-btn-primary" type="submit">Yes, Delete</button>
    <a href="<?php echo $context->calendar->getEditURL() ?>" class="dcf-btn" style="vertical-align: middle;">No, let's not</a>
</form>
