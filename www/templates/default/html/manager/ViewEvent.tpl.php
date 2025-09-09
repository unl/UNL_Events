<?php
use UNL\UCBCN\Event\Occurrence;

$calendar = $context->calendar;
$event = $context->event;
$event_type = $event->getFirstType();

$total_pages = ceil(count($event->getDatetimes()) / 5);
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

    <h2 id="location-date-time-header">Date, Time, and Locations</h2>
    <table class="dcf-table dcf-table-bordered dcf-mb-6" aria-labelledby="location-date-time-header">
        <thead>
            <tr>
                <th scope="col">Dates</th>
                <th scope="col">Location</th>
                <th scope="col">Virtual Location</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($event->getDatetimes() as $datetime) : ?>
                <tr>
                    <td class="dcf-txt-middle dates">
                        <?php
                            $date_format = 'n/d/y';
                            $time_format = 'g:ia';

                            // Set up default values
                            $recurring_details = '';
                            $date_details = date(
                                $date_format,
                                strtotime($datetime->starttime)
                            );
                            $time_details = date(
                                $time_format,
                                strtotime($datetime->starttime)
                            );

                            // Define recurring details
                            if ($datetime->recurringtype == 'daily' ||
                                $datetime->recurringtype == 'weekly' ||
                                $datetime->recurringtype == 'biweekly' ||
                                $datetime->recurringtype == 'annually'
                            ) {
                                $recurring_details = ucwords($datetime->recurringtype) . ':';
                            } elseif ($datetime->recurringtype == 'monthly') {
                                if ($datetime->rectypemonth == 'lastday') {
                                    $recurring_details = 'Last day of every month:';
                                } elseif ($datetime->rectypemonth == 'date') {
                                    $recurring_details = date('jS', strtotime($datetime->starttime)) . ' of every month:';
                                } else {
                                    $recurring_details = ucwords($datetime->rectypemonth) . date(' l', strtotime($datetime->starttime)). ' of every month:';
                                }
                            }

                            // Define date range if the recurs until is set
                            if (
                                isset($datetime->recurs_until) &&
                                $datetime->recurs_until > $datetime->starttime
                            ) {
                                $date_details .= ' to ' . date(
                                    $date_format,
                                    strtotime($datetime->recurs_until)
                                );
                            }

                            // Defines time details depending on time mode
                            if ($datetime->isAllDay()) {
                                $time_details = 'All day';
                            } elseif ($datetime->timemode === Occurrence::TIME_MODE_TBD) {
                                $time_details = 'Time <abbr title="To Be Determined">TBD</abbr>';
                            } elseif ($datetime->timemode === Occurrence::TIME_MODE_START_TIME_ONLY ) {
                                $time_details = 'Starting at ' . $time_details;
                            } elseif ($datetime->timemode === Occurrence::TIME_MODE_END_TIME_ONLY) {
                                $time_details = 'Ending at ' . date(
                                    $time_format,
                                    strtotime($datetime->endtime)
                                );
                            } else {
                                // If we get here then check if there is an endtime
                                // and it is after start time
                                if (
                                    isset($datetime->endtime) &&
                                    $datetime->endtime > $datetime->starttime
                                ) {
                                    $time_details .= ' to '. date(
                                        ' g:ia',
                                        strtotime($datetime->endtime)
                                    );
                                }
                            }
                        ?>
                        <div>
                            <?php if (!empty($recurring_details)): ?>
                                <span class="dcf-txt-nowrap"><?php echo $recurring_details; ?></span>
                            <?php endif; ?>
                            <span class="dcf-txt-nowrap"><?php echo $date_details; ?></span>
                            <span class="dcf-txt-nowrap"><?php echo $time_details; ?></span>
                        </div>
                    </td>
                    <?php $location = $datetime->getLocation(); ?>
                    <?php if (isset($datetime->location_id) && $location !== false): ?>
                        <td
                            class="dcf-txt-middle location with-controls"
                            data-id="<?php echo $location->id; ?>"
                        >
                            <div class="dcf-popup dcf-w-100%" data-hover="true" data-point="true" hidden>
                                <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-w-100%" type="button">
                                    
                                    <?php echo $location->name; ?>
                                </button>
                                <div
                                    class="dcf-popup-content unl-cream unl-bg-blue dcf-p-3 dcf-rounded"
                                    style="width: 100%; min-width: 25ch;"
                                >
                                    <dl>
                                        <?php if(isset($location->name) && !empty($location->name)): ?>
                                            <dt>Name</dt>
                                            <dd><?php echo $location->name; ?></dd>
                                        <?php endif; ?>

                                        <?php
                                            if(isset($location->streetaddress1) && !empty($location->streetaddress1)):
                                        ?>
                                            <dt>Street Address 1</dt>
                                            <dd><?php echo $location->streetaddress1; ?></dd>
                                        <?php endif; ?>

                                        <?php
                                            if(isset($location->streetaddress2) && !empty($location->streetaddress2)):
                                        ?>
                                            <dt>Street Address 2</dt>
                                            <dd><?php echo $location->streetaddress2; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($location->city) && !empty($location->city)): ?>
                                            <dt>City</dt>
                                            <dd><?php echo $location->city; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($location->state) && !empty($location->state)): ?>
                                            <dt>State</dt>
                                            <dd><?php echo $location->state; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($location->zip) && !empty($location->zip)): ?>
                                            <dt>Zip</dt>
                                            <dd><?php echo $location->zip; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($location->room) && !empty($location->room)): ?>
                                            <dt>Room</dt>
                                            <dd><?php echo $location->room; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($datetime->room) && !empty($datetime->room)): ?>
                                            <dt>Room</dt>
                                            <dd><?php echo $datetime->room; ?></dd>
                                        <?php elseif(isset($location->room) && !empty($location->room)): ?>
                                            <dt>Room</dt>
                                            <dd><?php echo $location->room; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($datetime->directions) && !empty($datetime->directions)): ?>
                                            <dt>Directions</dt>
                                            <dd><?php echo $datetime->directions; ?></dd>
                                        <?php elseif(isset($location->directions) && !empty($location->directions)): ?>
                                            <dt>Directions</dt>
                                            <dd><?php echo $location->directions; ?></dd>
                                        <?php endif; ?>

                                        <?php
                                            if(isset($datetime->location_additionalpublicinfo) &&
                                                !empty($datetime->location_additionalpublicinfo)
                                            ):
                                        ?>
                                            <dt>Additional Public Info</dt>
                                            <dd><?php echo $datetime->location_additionalpublicinfo; ?></dd>
                                        <?php
                                            elseif(isset($location->additionalpublicinfo) &&
                                                !empty($location->additionalpublicinfo)
                                            ):
                                        ?>
                                            <dt>Additional Public Info</dt>
                                            <dd><?php echo $location->additionalpublicinfo; ?></dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>
                        </td>
                    <?php else: ?>
                        <td class="dcf-txt-middle location with-controls no-location" data-id="">
                            <?php echo "None"; ?>
                        </td>
                    <?php endif;?>

                    <?php $getWebcast = $datetime->getWebcast(); ?>
                    <?php if (isset($datetime->webcast_id) && $getWebcast !== false): ?>
                        <td
                            class="dcf-txt-middle v_location with-controls"
                            data-id="<?php echo $getWebcast->id; ?>"
                        >
                            <div class="dcf-popup dcf-w-100%" data-hover="true" data-point="true" hidden>
                                <button class="dcf-btn dcf-btn-tertiary dcf-btn-popup dcf-w-100%" type="button">
                                    <?php echo $getWebcast->title; ?>
                                </button>
                                <div
                                    class="dcf-popup-content unl-cream unl-bg-blue dcf-p-3 dcf-rounded"
                                    style="min-width: 25ch;"
                                >
                                    <dl>
                                        <?php if(isset($getWebcast->title) && !empty($getWebcast->title)): ?>
                                            <dt>Name</dt>
                                            <dd><?php echo $getWebcast->title; ?></dd>
                                        <?php endif; ?>

                                        <?php if(isset($getWebcast->url) && !empty($getWebcast->url)): ?>
                                            <dt>URL</dt>
                                            <dd><?php echo $getWebcast->url; ?></dd>
                                        <?php endif; ?>

                                        <?php
                                            if(isset($datetime->webcast_additionalpublicinfo) &&
                                                !empty($datetime->webcast_additionalpublicinfo)
                                            ):
                                        ?>
                                            <dt>Additional Public Info</dt>
                                            <dd><?php echo $datetime->webcast_additionalpublicinfo; ?></dd>
                                        <?php
                                            elseif(isset($getWebcast->additionalinfo) &&
                                                !empty($getWebcast->additionalinfo)
                                            ):
                                        ?>
                                            <dt>Additional Public Info</dt>
                                            <dd><?php echo $getWebcast->additionalinfo; ?></dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>
                        </td>
                    <?php else: ?>
                        <td class="dcf-txt-middle v_location with-controls no-webcast" data-id="">
                            <?php echo "None"; ?>
                        </td>
                    <?php endif;?>
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
