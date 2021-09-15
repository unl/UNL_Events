<?php
    $crumbs = new stdClass;
    $crumbs->crumbs = array(
        "Events Manager" => "/manager",
        $context->calendar->name => $context->calendar->getManageURL(),
        'Recommend ' . $context->event->title => NULL
    );
    echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>
<h1><?php echo 'Recommend ' . $context->event->title; ?></h1>
<form class="dcf-form" action="" method="POST">
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenNameKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenName() ?>" />
    <input type="hidden" name="<?php echo $controller->getCSRFHelper()->getTokenValueKey() ?>" value="<?php echo $controller->getCSRFHelper()->getTokenValue() ?>">
    <table class="recommend-list dcf-mb-5">
        <thead>
            <th>&nbsp;</th>
            <th class="center">Pending</th>
            <th class="center">Approved</th>
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
                    <div class="dcf-d-flex dcf-jc-center">
                        <div class="dcf-input-radio">
                            <input id="event-<?php echo $calendar->id ?>-is-pending" name="calendar_<?php echo $calendar->id ?>" type="radio" value="pending" title="<?php echo $calendar->name ?>: Pending">
                            <label for="event-<?php echo $calendar->id ?>-is-pending"><span class="dcf-sr-only">Pending</span></label>
                        </div>
                    </div>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
                </td>

                <td class="center">
                <?php if ($arr['status'] == 'posted' || $arr['status'] == 'archived') { ?>
                    <img src="<?php echo $base_frontend_url ?>templates/default/html/images/checkmark-16.png" alt="Event is Upcoming">
                <?php } else if ($arr['can_posted']) { ?>
                    <div class="dcf-d-flex dcf-jc-center">
                        <div class="dcf-input-radio">
                            <input id="event-<?php echo $calendar->id ?>-is-upcoming" name="calendar_<?php echo $calendar->id ?>" type="radio" value="posted" title="<?php echo $calendar->name ?>: Upcoming">
                            <label for="event-<?php echo $calendar->id ?>-is-upcoming"><span class="dcf-sr-only">Upcoming</span></label>
                        </div>
                    </div>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <button class="dcf-btn dcf-btn-primary" type="submit">Submit</button>
</form>
