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

<h1 class="wdn-brand">Delete <?php echo $context->calendar->name ?></h1>
<h2 class="wdn-brand">Are you sure?</h2>
<p>Deleting this calendar will delete all of the events associated with it. Permanently.</p>
<form method="POST" action="<?php echo $context->calendar->getDeleteFinalURL() ?>" class="delete-form">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <button class="wdn-button wdn-button-brand" type="submit">Yes, Delete</button>
    <a href="<?php echo $context->calendar->getEditURL() ?>" class="wdn-button" style="vertical-align: middle;">No, let's not</a>
</form>
