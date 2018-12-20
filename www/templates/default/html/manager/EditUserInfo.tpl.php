<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "Edit User Info" => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h2 class="wdn-brand">Edit User Info</h2>
<form action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <label class="dcf-label" for="api_token">Current API Token</label>
    <input class="dcf-input-text" disabled="disabled" type="text" id="token" name="token" value="<?php echo $context->user->token ?>" />
    <input class="hidden" type="text" name="generate_api_token" value="true">

    <br><br>
    <button class="dcf-btn dcf-btn-primary" type="submit">
        Generate new API Token
    </button>
</form>
