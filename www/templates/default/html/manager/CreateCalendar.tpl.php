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

<h1><?php echo ($context->calendar->id == NULL ? 'Create Calendar' : 'Edit ' . $context->calendar->name); ?></h1>

<form class="dcf-form" id="create-calendar-form" action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <div class="dcf-form-group">
        <label for="name">Name <small class="dcf-required">Required</small></label>
        <input type="text" id="name" name="name" value="<?php echo $context->calendar->name ?>" />
    </div>
    <div class="dcf-form-group">
        <label for="shortname">Shortname <small class="dcf-required">Required</small></label>
        <input type="text" id="shortname" name="shortname" value="<?php echo $context->calendar->shortname ?>" />
    </div>
    <div class="dcf-form-group">
        <label for="defaulttimezone">Default Time Zone <small class="dcf-required">Required</small></label>
        <select id="defaulttimezone"" name="defaulttimezone" aria-label="Default Timezone">
          <?php
          $timezone = UNL\UCBCN::$defaultTimezone;
          if (!empty($context->calendar->defaulttimezone)) {
              $timezone = $context->calendar->defaulttimezone;
          }
          foreach (UNL\UCBCN::getTimezoneOptions() as $tzName => $tzValue) { ?>
            <option <?php if ($timezone == $tzValue) echo 'selected="selected"'; ?> value="<?php echo $tzValue ?>"><?php echo $tzName ?></option>
          <?php } ?>
        </select>
    </div>
    <div class="dcf-form-group">
        <label for="website">Website</label>
        <input type="text" id="website" name="website" value="<?php echo $context->calendar->website ?>" />
    </div>
    <div class="dcf-form-group">
        <label for="event-release-preference">Event Release Preference</label>
        <select id="event-release-preference" name="event_release_preference">
            <option value="" <?php if ($context->calendar->getRawObject()->eventreleasepreference === \UNL\UCBCN\Calendar::EVENT_RELEASE_PREFERENCE_DEFAULT) echo 'selected="selected"' ?>></option>
            <option value="immediate" <?php if ($context->calendar->getRawObject()->eventreleasepreference == \UNL\UCBCN\Calendar::EVENT_RELEASE_PREFERENCE_IMMEDIATE) echo 'selected="selected"' ?>>Immediate</option>
            <option value="pending" <?php if ($context->calendar->getRawObject()->eventreleasepreference === (string)\UNL\UCBCN\Calendar::EVENT_RELEASE_PREFERENCE_PENDING) echo 'selected="selected"' ?>>Pending</option>
        </select>
    </div>
    <div class="dcf-form-group">
        <label for="email-lists">Email Lists (separated by commas)</label>
        <textarea id="email-lists" name="email_lists"><?php echo $context->calendar->emaillists ?></textarea>
    </div>
    <div class="dcf-input-checkbox">
        <input type="checkbox" name="recommend_within_account" id="recommend-within-account" <?php if ($context->calendar->recommendationswithinaccount) { echo 'checked="checked"'; } ?>>
        <label for="recommend-within-account">Allow event recommendations within this account</label>
    </div>
    <div class="dcf-mt-6">
        <button class="dcf-btn dcf-btn-primary" type="submit">
            <?php echo $context->calendar->id == NULL ? 'Create Calendar' : 'Save Calendar' ?>
        </button>
        <?php if ($context->calendar->id != NULL): ?>
        <a class="dcf-btn dcf-btn-secondary" href="<?php echo $context->calendar->getDeleteURL() ?>">Delete Calendar</a><br><br>
        <a class="dcf-btn dcf-btn-secondary" href="<?php echo $context->calendar->getCleanupURL() ?>">Clean Up Calendar Events</a>
        <?php endif; ?>
    </div>
</form>

<?php
$page->addScriptDeclaration("
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
});");
?>
