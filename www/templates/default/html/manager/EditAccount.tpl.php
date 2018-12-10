<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "Edit Account Info" => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h2 class="wdn-brand">Edit Account Info</h2>

<form action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <label class="dcf-label" for="name">Account Name</label>
    <input class="dcf-input-text" type="text" id="name" name="name" value="<?php echo $context->account->name ?>" />

    <label class="dcf-label" for="address-1">Address</label>
    <input class="dcf-input-text" type="text" id="address-1" name="streetaddress1" value="<?php echo $context->account->streetaddress1 ?>" />

    <label class="dcf-label" for="address-2">Address 2</label>
    <input class="dcf-input-text" type="text" id="address-2" name="streetaddress2" value="<?php echo $context->account->streetaddress2 ?>" />

    <label class="dcf-label" for="city">City</label>
    <input class="dcf-input-text" class="dcf-label" type="text" id="city" name="city" value="<?php echo $context->account->city ?>" />

    <label class="dcf-label" for="state">State</label>
    <input class="dcf-input-text" type="text" id="state" name="state" value="<?php echo $context->account->state ?>" />

    <label class="dcf-label" for="zip">Zip</label>
    <input class="dcf-input-text" type="text" id="zip" name="zip" value="<?php echo $context->account->zip ?>" />

    <label class="dcf-label" for="phone">Phone</label>
    <input class="dcf-input-text" type="text" id="phone" name="phone" value="<?php echo $context->account->phone ?>" />

    <label class="dcf-label" for="fax">Fax</label>
    <input class="dcf-input-text" type="text" id="fax" name="fax" value="<?php echo $context->account->fax ?>" />

    <label class="dcf-label" for="email">Email</label>
    <input class="dcf-input-text" type="text" id="email" name="email" value="<?php echo $context->account->email ?>" />

    <label class="dcf-label" for="website">Website</label>
    <input class="dcf-input-text" type="text" id="website" name="website" value="<?php echo $context->account->website ?>" />
    <br>
    <br>

    <button class="dcf-btn wdn-button-brand" type="submit">
        Update Account
    </button>
</form>
