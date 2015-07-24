<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        "Edit Account Info" => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1 class="wdn-brand">Edit Account Info</h1>

<form action="" method="POST">
    <label for="name">Account Name</label>
    <input type="text" id="name" name="name" value="<?php echo $context->account->name ?>" />

    <label for="address-1">Address</label>
    <input type="text" id="address-1" name="streetaddress1" value="<?php echo $context->account->streetaddress1 ?>" />

    <label for="address-2">Address 2</label>
    <input type="text" id="address-2" name="streetaddress2" value="<?php echo $context->account->streetaddress2 ?>" />

    <label for="city">City</label>
    <input type="text" id="city" name="city" value="<?php echo $context->account->city ?>" />

    <label for="state">State</label>
    <input type="text" id="state" name="state" value="<?php echo $context->account->state ?>" />

    <label for="zip">Zip</label>
    <input type="text" id="zip" name="zip" value="<?php echo $context->account->zip ?>" />

    <label for="phone">Phone</label>
    <input type="text" id="phone" name="phone" value="<?php echo $context->account->phone ?>" />

    <label for="fax">Fax</label>
    <input type="text" id="fax" name="fax" value="<?php echo $context->account->fax ?>" />

    <label for="email">Email</label>
    <input type="text" id="email" name="email" value="<?php echo $context->account->email ?>" />

    <label for="website">Website</label>
    <input type="text" id="website" name="website" value="<?php echo $context->account->website ?>" />
    <br>
    <br>

    <button class="wdn-button wdn-button-brand" type="submit">
        Update Account
    </button>
</form>
