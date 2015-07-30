<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        'Recommend ' . $context->event->title => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1 class="wdn-brand">
<?php echo 'Recommend ' . $context->event->title; ?>
</h1>
<form action="" method="POST">
    <table class="recommend-list">
        <thead>
            <th>
                &nbsp;
            </th>
            <th class="center">
                Pending
            </th>
            <th class="center">
                Approved
            </th>
        </thead>
        <tbody>
        <?php foreach($context->getRecommendableCalendars() as $id => $arr): ?>
            <tr>
                <?php $calendar = $arr['calendar']; ?>
                <td style="word-wrap: break-word;">
                    <?php echo $calendar->name; ?>
                </td>
                <td class="center">
                <?php if ($arr['status'] == 'pending') { ?>
                    <img src="<?php echo $base_frontend_url ?>templates/default/html/images/checkmark-16.png" alt="Event is Pending">
                <?php } else if ($arr['can_pending']) { ?>
                    <input type="radio" title="<?php echo $calendar->name ?>: Pending" name="calendar_<?php echo $calendar->id ?>" value="pending">
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
                </td>

                <td class="center">
                <?php if ($arr['status'] == 'posted' || $arr['status'] == 'archived') { ?>
                    <img src="<?php echo $base_frontend_url ?>templates/default/html/images/checkmark-16.png" alt="Event is Upcoming">
                <?php } else if ($arr['can_posted']) { ?>
                    <input type="radio" title="<?php echo $calendar->name ?>: Upcoming" name="calendar_<?php echo $calendar->id ?>" value="posted">
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <button class="wdn-button wdn-button-brand" type="submit">
        Submit
    </button>
</form>
