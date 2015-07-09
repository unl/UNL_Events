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
        ($context->subscription->id == NULL ? 'Add a Subscription' : 'Edit Subscription') => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>

<div class="wdn-grid-set">
    <form action="<?php echo $context->subscription->id == NULL ? $context->subscription->getNewURL($context->calendar) : $context->subscription->getEditURL($context->calendar) ?>" method="POST">
        <div class="wdn-col-two-thirds">
            <h3><?php echo $context->subscription->id == NULL ? 'Add a Subscription' : 'Edit Subscription' ?></h3>
            <fieldset>
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?php echo $context->subscription->name ?>" />

                <label for="calendars">Events Posted to Calendar(s):</label>
                <select id="calendars" name="calendars[]" multiple="multiple" class="use-select2">
                <?php foreach($context->getAvailableCalendars() as $calendar): ?>
                    <option <?php if (in_array($calendar->id, $subbed_calendar_ids)) echo 'selected="selected"'; ?> value="<?php echo $calendar->id ?>"><?php echo $calendar->name ?></option>
                <?php endforeach; ?>
                </select>
            </fieldset>

            <button class="wdn-button wdn-button-brand" type="submit">
                <?php echo $context->subscription->id == NULL ? 'Add Subscription' : 'Save Subscription' ?>
            </button>
        </div>
        <div class="wdn-col-one-third">
            <br>
            <div class="visual-island">
                <p>
                    <label>Automatically approve events?</label>
                    <br>
                    <br>
                    <input type="radio" value="no" name="auto_approve" id="auto-approve-no" checked="checked"> 
                    <label for="auto-approve-no">No (send to pending)</label> 
                    <br>
                    <input type="radio" value="yes" name="auto_approve" id="auto-approve-yes"> 
                    <label for="auto-approve-yes">Yes (send to upcoming)</label> 
                </p>
            </div>
        </div>
    </form>
</div>