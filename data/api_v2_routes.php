<?php
/*
 * Map of regular expressions which map to models the controller will construct
 */
$routes = array();

// Optional calendar short name, which prefixes all routes
$calendar = '(?P<calendar_id>([0-9]+|[a-zA-Z-_0-9]+))';
$user = '(?P<user_uid>([a-zA-Z0-9-_]+))';
$subscription = '(?P<subscription_id>([0-9]+))';
$location = '(?P<location_id>([0-9]+|standard))';
$webcast = '(?P<webcast_id>([0-9]+))';
$event = '(?P<event_id>([0-9]+))';
$event_datetime = '(?P<event_datetime_id>([0-9]+))';
$recurrence_id = '(?P<recurrence_id>([0-9]+))';
$calendar_slash_required = '(' . $calendar . '\/)?';
$calendar_slash_optional = '(' . $calendar . '(\/)?)?';

$routes['/^(\/)?$/'] = 'UNL\UCBCN\APIv2\APIHome';
$routes['/^audiences(\/)?$/'] = 'UNL\UCBCN\APIv2\APIAudiences';
$routes['/^eventtypes(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEventTypes';
$routes['/^location(\/)?$/'] = 'UNL\UCBCN\APIv2\APILocation';
$routes['/^location\/' . $location . '(\/)?$/'] = 'UNL\UCBCN\APIv2\APILocation';
$routes['/^virtual-location(\/)?$/'] = 'UNL\UCBCN\APIv2\APIWebcast';
$routes['/^virtual-location\/' . $webcast . '(\/)?$/'] = 'UNL\UCBCN\APIv2\APIWebcast';
$routes['/^calendar(\/)?$/'] = 'UNL\UCBCN\APIv2\APICalendar';
$routes['/^calendar\/' . $calendar . '(\/)?$/'] = 'UNL\UCBCN\APIv2\APICalendar';
$routes['/^calendar\/' . $calendar . '\/event(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvent';
$routes['/^calendar\/' . $calendar . '\/event\/' . $event . '(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvent';
$routes['/^calendar\/' . $calendar . '\/event\/' . $event . '\/status(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvent';
$routes['/^calendar\/' . $calendar . '\/event\/datetime\/' . $event_datetime . '(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvent';
$routes['/^calendar\/' . $calendar . '\/event\/datetime\/recurrence\/' . $recurrence_id . '(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvent';
$routes['/^calendar\/' . $calendar . '\/events(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvents';
$routes['/^calendar\/' . $calendar . '\/events\/search(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvents';
$routes['/^calendar\/' . $calendar . '\/events\/pending(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvents';
$routes['/^calendar\/' . $calendar . '\/events\/archived(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvents';
$routes['/^calendar\/' . $calendar . '\/events\/location\/' . $location . '(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvents';
$routes['/^calendar\/' . $calendar . '\/events\/virtual-location\/' . $webcast . '(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEvents';
$routes['/^calendars\/search(\/)?$/'] = 'UNL\UCBCN\APIv2\APIAllCalendars'; // This is here for legacy purposes
$routes['/^all-calendars\/search(\/)?$/'] = 'UNL\UCBCN\APIv2\APIAllCalendars';
$routes['/^all-calendars\/events(\/)?$/'] = 'UNL\UCBCN\APIv2\APIAllCalendars';
$routes['/^me(\/)?$/'] = 'UNL\UCBCN\APIv2\APIMe';
$routes['/^me\/calendars(\/)?$/'] = 'UNL\UCBCN\APIv2\APIMe';
$routes['/^me\/locations(\/)?$/'] = 'UNL\UCBCN\APIv2\APIMe';
$routes['/^me\/virtual-locations(\/)?$/'] = 'UNL\UCBCN\APIv2\APIMe';

return $routes;
