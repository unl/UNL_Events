<?php
$crumbs = new stdClass;
$crumbs->crumbs = array(
    "Events Manager" => "/manager",
    $context->calendar->name => $context->calendar->getManageURL(),
    "Event Clean Up" => $context->calendar->getCleanupURL(),
);
echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>

<h2 class="wdn-brand">Event Clean Up</h2>

<p>Use this form to permanently remove past events from your calendar. <strong>Note: This may take awhile if you are removing alot of events.</strong></p>

<form action="<?php echo $context->calendar->getCleanupURL(); ?>" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">

    <div class="dcf-form-group">
        <label class="dcf-label" for="past-duration">Remove Events Older Than</label>
        <select class="dcf-input-select" id="past-duration" name="past_duration">
            <option value="<?php echo \UNL\UCBCN\Calendar::CLEANUP_YEARS_10; ?>">Ten Years</option>
            <option value="<?php echo \UNL\UCBCN\Calendar::CLEANUP_YEARS_5; ?>">Five Years</option>
            <option value="<?php echo \UNL\UCBCN\Calendar::CLEANUP_YEARS_4; ?>">Four Years</option>
            <option value="<?php echo \UNL\UCBCN\Calendar::CLEANUP_YEARS_3; ?>">Three Years</option>
            <option value="<?php echo \UNL\UCBCN\Calendar::CLEANUP_YEARS_2; ?>">Two Years</option>
            <option value="<?php echo \UNL\UCBCN\Calendar::CLEANUP_YEARS_1; ?>">One Year</option>
        </select>
    </div>

    <button class="dcf-btn dcf-btn-primary" type="submit" onclick=" return confirm('Are you sure you want to permanently remove these events from your calendar?');">Clean Up Events</button>

</form>
