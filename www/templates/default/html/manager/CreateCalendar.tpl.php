<?php
    $crumbs = new stdClass;

    if ($context->calendar->id != NULL) {
        $crumbs->crumbs = array(
            "Events Manager" => "/manager",
            $context->calendar->name => $context->calendar->getManageURL(),
            'Edit Calendar Info' => NULL
        );
    } else {
        $crumbs->crumbs = array(
            "Events Manager" => "/manager",
            'Create Calendar' => NULL
        );
    }
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>

<h1 class="wdn-brand"><?php echo ($context->calendar->id == NULL ? 'Create Calendar' : 'Edit ' . $context->calendar->name); ?></h1>

<form id="create-calendar-form" action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
  
    <label for="name"><span class="required">*</span> Name</label>
    <input type="text" id="name" name="name" value="<?php echo $context->calendar->name ?>" />

    <label for="shortname"><span class="required">*</span> Shortname</label>
    <input type="text" id="shortname" name="shortname" value="<?php echo $context->calendar->shortname ?>" />

    <label for="website">Website</label>
    <input type="text" id="website" name="website" value="<?php echo $context->calendar->website ?>" />

    <label for="event-release-preference">Event Release Preference</label>
    <select id="event-release-preference" name="event_release_preference">
        <option value="" <?php if ($context->calendar->getRawObject()->eventreleasepreference === \UNL\UCBCN\Calendar::EVENT_RELEASE_PREFERENCE_DEFAULT) echo 'selected="selected"' ?>></option>
        <option value="immediate" <?php if ($context->calendar->getRawObject()->eventreleasepreference == \UNL\UCBCN\Calendar::EVENT_RELEASE_PREFERENCE_IMMEDIATE) echo 'selected="selected"' ?>>Immediate</option>
        <option value="pending" <?php if ($context->calendar->getRawObject()->eventreleasepreference === (string)\UNL\UCBCN\Calendar::EVENT_RELEASE_PREFERENCE_PENDING) echo 'selected="selected"' ?>>Pending</option>
    </select>

    <label for="email-lists">Email Lists (separated by commas)</label>
    <textarea id="email-lists" name="email_lists"><?php echo $context->calendar->emaillists ?></textarea>

    <input type="checkbox" name="recommend_within_account" id="recommend-within-account" <?php if ($context->calendar->recommendationswithinaccount) echo 'checked="checked"' ?>> 
    <label for="recommend-within-account">Allow event recommendations within this account</label>
    <br>
    <br>
    <button class="wdn-button wdn-button-brand" type="submit">
        <?php echo $context->calendar->id == NULL ? 'Create Calendar' : 'Save Calendar' ?>
    </button>
    <br><br>

    <?php if ($context->calendar->id != NULL): ?>
    <a class="wdn-button" href="<?php echo $context->calendar->getDeleteURL() ?>">
    Delete Calendar
    </a>
    <?php endif; ?>
</form>

<script type="text/javascript">
require(['jquery'], function($) {
    $('#create-calendar-form').submit(function (submit) {
        if ($('#name').val() == '' || $('#shortname').val() == '') {
            if ($('#name').val() == '') {
                notifier.mark_input_invalid($('#name'));
            }
            if ($('#shortname').val() == '') {
                notifier.mark_input_invalid($('#shortname'));
            }
            notifier.alert('Sorry! We couldn\'t create your calendar', 'Name and shortname are required.');
            submit.preventDefault();
        } else if (!($('#shortname').val().match(/^[a-zA-Z-_0-9]+$/))) {
            notifier.mark_input_invalid($('#shortname'));
            notifier.alert('Sorry! We couldn\'t create your calendar', 'Calendar shortnames must contain only letters, numbers, dashes, and underscores.');
            submit.preventDefault();
        }
    });
});
</script>
