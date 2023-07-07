<?php
$calendar = $context->calendar;
$event = $context->event;
$event_type = $event->getFirstType();

$total_pages = ceil(count($event->getDatetimes()) / 5);

function ordinal($number) {
    $mod = $number % 100;
    if ($mod >= 11 && $mod <= 13) {
        return $number . 'th';
    } else if ($mod % 10 == 1) {
        return $number . 'st';
    } else if ($mod % 10 == 2) {
        return $number . 'nd';
    } else if ($mod % 10 == 3) {
        return $number . 'rd';
    } else {
        return $number . 'th';
    }
}

?>
<?php
$crumbs = new stdClass;
$crumbs->crumbs = array(
    "Events Manager" => "/manager",
    $context->calendar->name => $context->calendar->getManageURL(),
    'View "' . $event->title . '"' => NULL
);
echo $savvy->render($crumbs, 'BreadcrumbBar.tpl.php');
?>

<h1>Event Preview</h1>
<p>This is a preview of an event to help you decide whether or not to accept a recommendation.</p>

<div>
    <h2>Event Details</h2>
    <dl>
        <dt>Title</dt>
        <dd><?php echo $event->title; ?></dd>
        <dt>Subtitle</dt>
        <dd><?php echo ($event->subtitle)?$event->subtitle:'(empty)'; ?></dd>
        <dt>Description</dt>
        <dd><?php echo ($event->description)?$event->description:'(empty)'; ?></dd>
        <dt>Type</dt>
        <dd><?php echo ($event_type)?$event_type->name:'(empty)'; ?></dd>
    </dl>

    <h2 id="location-date-time-header">Location, Date &amp; Time</h2>
    <table class="dcf-table dcf-table-bordered dcf-mb-6" aria-labelledby="location-date-time-header">
        <thead>
            <tr>
                <th scope="col">Dates</th>
                <th scope="col">Location</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($event->getDatetimes() as $datetime) : ?>
                <tr>
                    <td>
                        <?php
                            if ($datetime->recurringtype == 'none') {
                                echo date('n/d/y @ g:ia', strtotime($datetime->starttime));
                            } else if ($datetime->recurringtype == 'daily' || $datetime->recurringtype == 'weekly' || $datetime->recurringtype == 'biweekly' ||
                                $datetime->recurringtype == 'annually') {
                                echo ucwords($datetime->recurringtype) . ' @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                    ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                    ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                            } else if ($datetime->recurringtype == 'monthly') {
                                if ($datetime->rectypemonth == 'lastday') {
                                    echo 'Last day of each month @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                        ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                        ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                                } else if ($datetime->rectypemonth == 'date') {
                                    echo ordinal(date('d', strtotime($datetime->starttime))) .
                                        ' of each month @ ' . date('g:ia', strtotime($datetime->starttime)) .
                                        ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                        ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                                } else {
                                    echo ucwords($datetime->rectypemonth) . date(' l', strtotime($datetime->starttime)) . ' of every month' .
                                        ' from ' . date('n/d/y', strtotime($datetime->starttime)) .
                                        ' to ' . date('n/d/y', strtotime($datetime->recurs_until));
                                }
                            }
                        ?>
                    </td>
                    <td>
                        <?php $location = $datetime->getLocation(); ?>
                        <?php if ($location !== false): ?>
                            <?php echo $location->name; ?>
                        <?php else: ?>
                            <?php echo "No Physical Location" ?>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Contact Info</h2>
    <?php if (!empty($event->listingcontactname) || !empty($event->listingcontactphone) || !empty($event->listingcontactemail) || !empty($event->webpageurl)): ?>
        <dl>
            <dt>Name</dt>
            <dd><?php echo $event->listingcontactname; ?></dd>
            <dt>Phone</dt>
            <dd><?php echo $event->listingcontactphone; ?></dd>
            <dt>Email</dt>
            <dd><?php echo $event->listingcontactemail; ?></dd>
            <dt>Event Website</dt>
            <dd><?php echo $event->webpageurl; ?></dd>
        </dl>
    <?php else: ?>
        <p>No contact information provided</p>
    <?php endif; ?>

    <h2>Image</h2>
    <?php if ($image = $context->getImageURL()): ?>
        <img src="<?php echo $context->getImageURL() ?>" alt="Image for event <?php echo $event->id ?>" />
    <?php else: ?>
        <p>No image set</p>
    <?php endif; ?>
</div>
