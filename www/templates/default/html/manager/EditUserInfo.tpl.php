<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "Edit User Info" => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1 class="wdn-brand">Edit User Info</h1>

<form action="" method="POST">
    <label for="api_token">Current API Token</label>
    <input disabled="disabled" type="text" id="token" name="token" value="<?php echo $context->user->token ?>" />
    <input class="hidden" type="text" name="generate_api_token" value="true">

    <br><br>
    <button class="wdn-button wdn-button-brand" type="submit">
        Generate new API Token
    </button>
</form>
