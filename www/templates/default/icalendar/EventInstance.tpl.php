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
    $out[] = 'UID:'.$context->eventdatetime->id . '-' . $context->recurringdate->id .'@'.$_SERVER['SERVER_NAME'];
}
$out[] = 'DTSTAMP:'.gmdate("Ymd\THis\Z" , strtotime($context->event->datecreated));
$contactName = !empty($context->event->listingcontactname) ? $context->event->listingcontactname : 'unknown';
$contactEmail = !empty($context->event->listingcontactemail) ? 'MAILTO:' . $context->event->listingcontactemail : '';
$organizer = icalFormatString('CN=' . $contactName) . ":" . $contactEmail;
$out[] = 'ORGANIZER;' . $organizer;
$out[] = 'SUMMARY:' . icalFormatString($context->event->title);
$out[] = 'DESCRIPTION:' . icalFormatString($context->event->description);
if (isset($context->eventdatetime->location_id) && $context->eventdatetime->location_id) {
    $l = $context->eventdatetime->getLocation();
    $loc =  'LOCATION:'.$l->name;
    if (isset($context->eventdatetime->room)) {
        $loc .=  ' Room '.$context->eventdatetime->room;
    }
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

