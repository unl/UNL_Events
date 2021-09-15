<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "Edit Account Info" => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1>Edit Account Info</h1>
<form class="dcf-form" action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <fieldset class="dcf-mb-6">
        <legend>Account Info</legend>
        <div class="dcf-form-group">
            <label for="name">Account Name</label>
            <input type="text" id="name" name="name" value="<?php echo $context->account->name ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="address-1">Address</label>
            <input type="text" id="address-1" name="streetaddress1" value="<?php echo $context->account->streetaddress1 ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="address-2">Address 2</label>
            <input type="text" id="address-2" name="streetaddress2" value="<?php echo $context->account->streetaddress2 ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" value="<?php echo $context->account->city ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="state">State</label>
            <input type="text" id="state" name="state" value="<?php echo $context->account->state ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="zip"><abbr title="Zone Improvement Plan">ZIP</abbr> Code</label>
            <input type="text" id="zip" name="zip" value="<?php echo $context->account->zip ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo $context->account->phone ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="fax">Fax</label>
            <input type="text" id="fax" name="fax" value="<?php echo $context->account->fax ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="<?php echo $context->account->email ?>" />
        </div>
        <div class="dcf-form-group">
            <label for="website">Website</label>
            <input type="text" id="website" name="website" value="<?php echo $context->account->website ?>" />
        </div>
    </fieldset>
    <button class="dcf-btn dcf-btn-primary" type="submit">Update Account</button>
</form>
