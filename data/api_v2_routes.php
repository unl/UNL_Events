<?php
/*
 * Map of regular expressions which map to models the controller will construct
 */
$routes = array();

// Optional calendar short name, which prefixes all routes
$calendar = '(?P<calendar_shortname>([a-zA-Z-_0-9]+)+)';
$user = '(?P<user_uid>([a-zA-Z0-9-_]+))';
$subscription = '(?P<subscription_id>([0-9]+))';
$location = '(?P<location_id>([0-9]+|standard))';
$webcast = '(?P<webcast_id>([0-9]+))';
$event = '(?P<event_id>([0-9]+))';
$event_datetime	= '(?P<event_datetime_id>([0-9]+))';
$recurrence_id = '(?P<recurrence_id>([0-9]+))';
$calendar_slash_required = '(' . $calendar . '\/)?';
$calendar_slash_optional = '(' . $calendar . '(\/)?)?';

$routes['/^(\/)?$/'] = 'UNL\UCBCN\APIv2\APIHome';
$routes['/^audience(\/)?$/'] = 'UNL\UCBCN\APIv2\APIAudiences';
$routes['/^eventtype(\/)?$/'] = 'UNL\UCBCN\APIv2\APIEventTypes';
$routes['/^location(\/)?$/'] = 'UNL\UCBCN\APIv2\APILocation';
$routes['/^location\/' . $location . '(\/)?$/'] = 'UNL\UCBCN\APIv2\APILocation';
$routes['/^virtual-location(\/)?$/'] = 'UNL\UCBCN\APIv2\APIWebcast';
$routes['/^virtual-location\/' . $webcast . '(\/)?$/'] = 'UNL\UCBCN\APIv2\APIWebcast';

return $routes;