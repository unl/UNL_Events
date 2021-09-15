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

<h1>Delete <?php echo $context->calendar->name ?></h1>
<p><strong>Are you sure?</strong></p>
<p>Deleting this calendar will <strong>permanently</strong> delete all of the events associated with it.</p>
<form method="POST" action="<?php echo $context->calendar->getDeleteFinalURL() ?>" class="delete-form">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <button class="dcf-btn dcf-btn-primary" type="submit">Yes, delete</button>
    <a class="dcf-btn dcf-btn-secondary" href="<?php echo $context->calendar->getEditURL() ?>">No, do not delete</a>
</form>
