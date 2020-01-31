<?php 
    $subbed_calendars = $context->subscription->getSubscribedCalendars();
    $subbed_calendar_ids = array();
    foreach ($subbed_calendars as $subbed) {
        $subbed_calendar_ids[] = $subbed->id;
    }
?>

<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        "Subscriptions" => $context->calendar->getSubscriptionsURL(),
        ($context->subscription->id == NULL ? 'Add a Subscription' : 'Edit Subscription') => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h2 class="wdn-brand"><?php echo $context->subscription->id == NULL ? 'Add a Subscription' : 'Edit Subscription' ?></h2>
<form class="dcf-form" id="add-subscription" action="<?php echo $context->subscription->id == NULL ? $context->subscription->getNewURL($context->calendar) : $context->subscription->getEditURL($context->calendar) ?>" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">

    <div class="dcf-form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?php echo $context->subscription->name ?>" />
    </div>

    <div class="dcf-form-group">
        <label for="calendars">Events Posted to Calendar(s):</label>
        <p class="dcf-txt-xs dcf-mb-1">Please select at least one calendar from list below. Click to view list or click and type to filter list.</p>
        <select id="calendars" name="calendars[]" multiple="multiple" class="use-select2">
        <?php foreach($context->getAvailableCalendars() as $calendar): ?>
            <?php
                // skip the calendar adding the subscription as option, since can not subscribe to itself
                if ($context->calendar->id == $calendar->id) {
                    continue;
                }
            ?>
            <option <?php if (in_array($calendar->id, $subbed_calendar_ids)) echo 'selected="selected"'; ?> value="<?php echo $calendar->id ?>">
            <?php
                echo trim($calendar->name) . ' (' . trim($calendar->shortname);
                if ($calendar->id == UNL\UCBCN::$main_calendar_id) {
                    echo ' - Main Calendar';
                }
                echo ')';
            ?>
            </option>
        <?php endforeach; ?>
        </select>
    </div>

    <div class="dcf-form-group">
        <fieldset>
            <legend>Automatically approve events?</legend>
            <div class="dcf-input-radio">
            <?php
                $checked = '';
                if (!$context->subscription->id || 0 == $context->subscription->automaticapproval) {
                    $checked = 'checked="checked"';
                }
            ?>
            <input type="radio" value="no" name="auto_approve" id="auto-approve-no" <?php echo $checked ?>>
            <label for="auto-approve-no">No (send to pending)</label>
            </div>
            <div class="dcf-input-radio">
            <?php
                $checked = '';
                if ($context->subscription->id && 1 == $context->subscription->automaticapproval) {
                    $checked = 'checked="checked"';
                }
            ?>
            <input type="radio" value="yes" name="auto_approve" id="auto-approve-yes" <?php echo $checked ?>>
            <label for="auto-approve-yes">Yes (send to upcoming)</label>
            </div>
        </fieldset>
    </div>

    <button class="dcf-mt-6 dcf-btn dcf-btn-primary" form="add-subscription" type="submit">
        <?php echo $context->subscription->id == NULL ? 'Add Subscription' : 'Save Subscription' ?>
    </button>
</form>
