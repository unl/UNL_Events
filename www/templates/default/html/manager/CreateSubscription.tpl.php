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
<form id="add-subscription" action="<?php echo $context->subscription->id == NULL ? $context->subscription->getNewURL($context->calendar) : $context->subscription->getEditURL($context->calendar) ?>" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <div class="dcf-grid dcf-col-gap-vw">
        <div class="dcf-col-100% dcf-col-67%-start@lg">
            <label class="dcf-label" for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo $context->subscription->name ?>" />

            <label class="dcf-label" for="calendars">Events Posted to Calendar(s):</label>
            <select id="calendars" name="calendars[]" multiple="multiple" class="use-select2" style="width: 100%;">
            <?php foreach($context->getAvailableCalendars() as $calendar): ?>
                <option <?php if (in_array($calendar->id, $subbed_calendar_ids)) echo 'selected="selected"'; ?> value="<?php echo $calendar->id ?>"><?php echo $calendar->name ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <div class="dcf-col-100% dcf-col-33%-end@lg">
            <br>
            <div class="visual-island">
                <div class="details top-border">
                    <fieldset>
                        <legend class="dcf-legend">Automatically approve events?</legend>
                        <ul>
                            <li>
                                <?php
                                $checked = '';
                                if (!$context->subscription->id || 0 == $context->subscription->automaticapproval) {
                                    $checked = 'checked="checked"';
                                }
                                ?>
                                <input class="dcf-input-control" type="radio" value="no" name="auto_approve" id="auto-approve-no" <?php echo $checked ?>>
                                <label class="dcf-label" for="auto-approve-no">No (send to pending)</label>
                            </li>
                            <li>
                                <?php
                                $checked = '';
                                if ($context->subscription->id && 1 == $context->subscription->automaticapproval) {
                                    $checked = 'checked="checked"';
                                }
                                ?>
                                <input class="dcf-input-control" type="radio" value="yes" name="auto_approve" id="auto-approve-yes" <?php echo $checked ?>>
                                <label class="dcf-label" for="auto-approve-yes">Yes (send to upcoming)</label>
                            </li>
                        </ul>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <br>
    <button class="dcf-btn dcf-btn-primary" form="add-subscription" type="submit">
        <?php echo $context->subscription->id == NULL ? 'Add Subscription' : 'Save Subscription' ?>
    </button>
</form>

<br>