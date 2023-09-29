<?php
/**
 * icalendar output for a single event instance.
 */

$timezoneDateTime = new \UNL\UCBCN\TimezoneDateTime($context->eventdatetime->timezone);

$out = array();
$out[] = 'BEGIN:VEVENT';
if (isset($context->eventdatetime->starttime)) {
    if (strpos($context->eventdatetime->starttime,'00:00:00')) {
        $out[] = 'DTSTART;VALUE=DATE:' . $timezoneDateTime->format($context->getStartTime(),'Ymd');
    } else {
        $out[] = 'DTSTART:'. $timezoneDateTime->formatUTC($context->getStartTime(),'Ymd\THis\Z');
    }
}
if ($context->eventdatetime->recurringtype == 'none') {
    $out[] = 'UID:'.$context->eventdatetime->id.'@'.$_SERVER['SERVER_NAME'];
} else {
    $recurringDateID = isset($context->recurringdate->id) ? $context->recurringdate->id : '';
    $out[] = 'UID:'.$context->eventdatetime->id . '-' . $recurringDateID .'@'.$_SERVER['SERVER_NAME'];
}
$out[] = 'DTSTAMP:'.gmdate("Ymd\THis\Z" , strtotime($context->event->datecreated));
$contactName = !empty($context->event->listingcontactname) ? $context->event->listingcontactname : 'unknown';
$contactEmail = !empty($context->event->listingcontactemail) ? 'MAILTO:' . $context->event->listingcontactemail : '';
$organizer = icalFormatString('CN=' . $contactName) . ":" . $contactEmail;
$out[] = 'ORGANIZER;' . $organizer;
$out[] = 'SUMMARY:' . icalFormatString($context->event->displayTitle($context));
$out[] = 'STATUS:' . icalFormatString($context->event->icalStatus($context));
$out[] = 'DESCRIPTION:' . icalFormatString($context->event->description);
$location = $context->eventdatetime->getLocation();
$webcast = $context->eventdatetime->getWebcast();
$has_location = isset($context->eventdatetime->location_id) && $location !== false;
$has_webcast = isset($context->eventdatetime->webcast_id) && $webcast !== false;
if ($has_location && $has_webcast) {
    $loc = 'LOCATION:' . $location->name;
    if (isset($context->eventdatetime->room)) {
        $loc .= ' Room ' . $context->eventdatetime->room;
    } elseif (isset($location->room)) {
        $loc .= ' Room ' . $location->room;
    }
    $loc .= ', and online at ' . $webcast->title . ' (' . $webcast->url . ')';
    $out[] = $loc;
} elseif ($has_location) {
    $loc = 'LOCATION:' . $location->name;
    if (isset($context->eventdatetime->room)) {
        $loc .= ' Room ' . $context->eventdatetime->room;
    } elseif (isset($location->room)) {
        $loc .= ' Room ' . $location->room;
    }
    $out[] = $loc;
} elseif ($has_webcast) {
    $loc = 'LOCATION:' . $webcast->title . ' (' . $webcast->url . ')';
    $out[] = $loc;
}
$out[] = 'URL:'.$context->getURL();
if (isset($context->eventdatetime->endtime)
    && $timezoneDateTime->getTimestamp($context->getEndTime()) > $timezoneDateTime->getTimestamp($context->getStartTime())) {
    if (strpos($context->eventdatetime->endtime,'00:00:00')) {
        $out[] = 'DTEND;VALUE=DATE:'. $timezoneDateTime->format($context->getEndTime(),'Ymd');
    } else {
        $out[] = 'DTEND:' . $timezoneDateTime->formatUTC($context->getEndTime(),'Ymd\THis\Z');
    }
} elseif (isset($context->eventdatetime->starttime)) {
    if (strpos($context->eventdatetime->starttime,'00:00:00')) {
        // All-day event
        $allDayEndDateTime = $timezoneDateTime->getDateTimeAddInterval($context->getStartTime(), 'P1D');
        $out[] = 'DTEND;VALUE=DATE:' . $allDayEndDateTime->format('Ymd');
    } else {
        // Event with unknown end-time
        $out[] = 'DTEND:'. $timezoneDateTime->formatUTC($context->getEndTime(),'Ymd\THis\Z');
    }
}
$out[] = 'END:VEVENT';
echo implode("\n",$out)."\n";

