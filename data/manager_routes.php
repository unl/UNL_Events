<?php
/*
 * Map of regular expressions which map to models the controller will construct
 */
$routes = array();

// Optional calendar short name, which prefixes all routes
$calendar                = '(?P<calendar_shortname>([a-zA-Z-_]([0-9]+)?)+)';
$calendar_slash_required = '(' . $calendar . '\/)?';
$calendar_slash_optional = '(' . $calendar . '(\/)?)?';

$routes['/^(\/)?$/'] = 'UNL\UCBCN\Manager\CalendarList';
$routes['/^'.$calendar_slash_optional.'$/'] = 'UNL\UCBCN\Manager\Calendar';
$routes['/^'.$calendar_slash_required.'create(\/)?$/'] = 'UNL\UCBCN\Manager\CreateEvent';
$routes['/^calendar\/new(\/)?$/'] = 'UNL\UCBCN\Manager\CreateCalendar';
$routes['/^'.$calendar_slash_required.'edit(\/)?$/'] = 'UNL\UCBCN\Manager\CreateCalendar';
$routes['/^'.$calendar_slash_required.'subscriptions\/new(\/)?$/'] = 'UNL\UCBCN\Manager\CreateSubscription';
$routes['/^'.$calendar_slash_required.'subscriptions(\/)?$/'] = 'UNL\UCBCN\Manager\Subscription';

return $routes;