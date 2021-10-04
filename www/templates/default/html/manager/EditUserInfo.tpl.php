<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "Edit User Info" => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1>Edit User Info</h1>
<form class="dcf-form" action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <div class="dcf-form-group">
        <label for="token">Current API Token</label>
        <input disabled="disabled" type="text" id="token" name="token" value="<?php echo $context->user->token ?>" />
    </div>
    <input class="dcf-d-none" type="text" name="generate_api_token" value="true">
    <button class="dcf-btn dcf-btn-primary" type="submit">Generate new API Token</button>
</form>
