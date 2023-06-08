<?php
    const CHECKED_INPUT = 'checked="checked"';
    const SELECTED_INPUT = 'selected="selected"';

    $calendar = $context->calendar;
    $event = $context->event;
    $datetime = $context->event_datetime;
    $recurringType = '';

    if ($datetime->starttime == null) {
        $start_time = '';
        $start_date =  '';
        $start_hour = '';
        $start_minute = -1;
        $start_am_pm = 'am';
    } else {
        $start_time = strtotime($datetime->starttime);
        $start_date = date('m/d/Y', $start_time);
        $start_hour = date('h', $start_time);
        $start_minute = date('i', $start_time);
        $start_am_pm = date('a', $start_time);
    }

    if ($datetime->endtime == null) {
        $end_time = '';
        $end_date =  '';
        $end_hour = '';
        $end_minute = -1;
        $end_am_pm = 'am';
    } else {
        $end_time = strtotime($datetime->endtime);
        $end_date = date('m/d/Y', $end_time);
        $end_hour = date('h', $end_time);
        $end_minute = date('i', $end_time);
        $end_am_pm = date('a', $end_time);
    }

    $recurs_until_date = date('m/d/Y', strtotime($datetime->recurs_until));
?>
<?php
    $last_crumb = null;
    if ($context->recurrence_id != null) {
        $last_crumb = 'Edit a Single Occurrence of a Recurring Instance';
    } else {
        $last_crumb = $datetime->id == null ? 'Add a New Instance' : 'Edit Existing Instance';
    }

    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        'Edit "' . $event->title . '"' => $event->getEditURL($context->calendar),
        $last_crumb => null
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1><?php echo $last_crumb ?></h1>

<form class="dcf-form" id="add-datetime-form" action="" method="POST">
    <input
        type="hidden"
        name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>"
        value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>"
    >
    <input
        type="hidden"
        name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>"
        value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>"
    >

    <?php echo $savvy->render($context, 'EventFormDateTime.tpl.php'); ?>

    <button class="dcf-btn dcf-btn-primary" type="submit">Submit</button>
</form>
<br>

<?php
$page->addScript(
    $base_frontend_url .
    'templates/default/html/js/manager-add-date-time.min.js?v='.
    UNL\UCBCN\Frontend\Controller::$version
);
