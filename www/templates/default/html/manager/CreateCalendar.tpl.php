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

<h2 class="wdn-brand"><?php echo ($context->calendar->id == NULL ? 'Create Calendar' : 'Edit ' . $context->calendar->name); ?></h2>

<form id="create-calendar-form" action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
  
    <label class="dcf-label" for="name"><span class="dcf-required">*</span> Name</label>
    <input class="dcf-input-text" type="text" id="name" name="name" value="<?php echo $context->calendar->name ?>" />

    <label class="dcf-label" for="shortname"><span class="dcf-required">*</span> Shortname</label>
    <input class="dcf-input-text" type="text" id="shortname" name="shortname" value="<?php echo $context->calendar->shortname ?>" />

    <label class="dcf-label dcf-mt-2" for="timezone"><span class="dcf-required">*</span> Default Timezone</label>
    <select class="dcf-input-select" id="defaulttimezone"" name="defaulttimezone" aria-label="Default Timezone">
      <?php
      $timezone = UNL\UCBCN::$defaultTimezone;
      if (!empty($context->calendar->defaulttimezone)) {
          $timezone = $context->calendar->defaulttimezone;
      }
      foreach (UNL\UCBCN::getTimezoneOptions() as $tzName => $tzValue) { ?>
        <option <?php if ($timezone == $tzValue) echo 'selected="selected"'; ?> value="<?php echo $tzValue ?>"><?php echo $tzName ?></option>
      <?php } ?>
    </select>

    <label class="dcf-label" for="website">Website</label>
    <input class="dcf-input-text" type="text" id="website" name="website" value="<?php echo $context->calendar->website ?>" />

    <div class="dcf-form-group">
      <label class="dcf-label" for="event-release-preference">Event Release Preference</label>
      <select class="dcf-input-select" id="event-release-preference" name="event_release_preference">
        <option value="" <?php if ($context->calendar->getRawObject()->eventreleasepreference === \UNL\UCBCN\Calendar::EVENT_RELEASE_PREFERENCE_DEFAULT) echo 'selected="selected"' ?>></option>
        <option value="immediate" <?php if ($context->calendar->getRawObject()->eventreleasepreference == \UNL\UCBCN\Calendar::EVENT_RELEASE_PREFERENCE_IMMEDIATE) echo 'selected="selected"' ?>>Immediate</option>
        <option value="pending" <?php if ($context->calendar->getRawObject()->eventreleasepreference === (string)\UNL\UCBCN\Calendar::EVENT_RELEASE_PREFERENCE_PENDING) echo 'selected="selected"' ?>>Pending</option>
      </select>
    </div>

    <label class="dcf-label" for="email-lists">Email Lists (separated by commas)</label>
    <textarea class="dcf-input-text" id="email-lists" name="email_lists"><?php echo $context->calendar->emaillists ?></textarea>

    <input class="dcf-input-control" type="checkbox" name="recommend_within_account" id="recommend-within-account" <?php if ($context->calendar->recommendationswithinaccount) echo 'checked="checked"' ?>>
    <label class="dcf-label" for="recommend-within-account">Allow event recommendations within this account</label>
    <br>
    <br>
    <button class="dcf-btn dcf-btn-primary" type="submit">
        <?php echo $context->calendar->id == NULL ? 'Create Calendar' : 'Save Calendar' ?>
    </button>
    <br><br>

    <?php if ($context->calendar->id != NULL): ?>
    <a class="dcf-btn dcf-btn-secondary" href="<?php echo $context->calendar->getDeleteURL() ?>">Delete Calendar</a><br><br>
    <a class="dcf-btn dcf-btn-secondary" href="<?php echo $context->calendar->getCleanupURL() ?>">Clean Calendar Events</a>
    <?php endif; ?>
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
