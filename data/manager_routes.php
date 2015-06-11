<?php
/*
 * Map of regular expressions which map to models the controller will construct
 */
$routes = array();

// Optional calendar short name, which prefixes all routes
$calendar = '(?P<calendar_shortname>([a-zA-Z-_0-9]+)+)';
$user = '(?P<user_uid>([a-zA-Z0-9-_]+))';
$subscription = '(?P<subscription_id>([0-9]+))';
$event = '(?P<event_id>([0-9]+))';
$event_datetime	= '(?P<event_datetime_id>([0-9]+))';
$recurrence_id = '(?P<recurrence_id>([0-9]+))';
$calendar_slash_required = '(' . $calendar . '\/)?';
$calendar_slash_optional = '(' . $calendar . '(\/)?)?';

$routes['/^(\/)?$/'] = 'UNL\UCBCN\Manager\CalendarList';
$routes['/^account(\/)?$/'] = 'UNL\UCBCN\Manager\EditAccount';
$routes['/^'.$calendar_slash_optional.'$/'] = 'UNL\UCBCN\Manager\Calendar';
$routes['/^'.$calendar_slash_required.'create(\/)?$/'] = 'UNL\UCBCN\Manager\CreateEvent';
$routes['/^'.$calendar_slash_required.'event\/' . $event . '\/edit(\/)?$/'] = 'UNL\UCBCN\Manager\EditEvent';
$routes['/^'.$calendar_slash_required.'event\/' . $event . '\/datetime\/add(\/)?$/'] = 'UNL\UCBCN\Manager\AddDateTime';
$routes['/^'.$calendar_slash_required.'event\/' . $event . '\/datetime\/' . $event_datetime . '\/edit(\/)?$/'] = 'UNL\UCBCN\Manager\AddDateTime';
$routes['/^'.$calendar_slash_required.'event\/' . $event . '\/datetime\/' . $event_datetime . '\/edit\/recurrence\/' . $recurrence_id . '(\/)?$/'] = 'UNL\UCBCN\Manager\AddDateTime';
$routes['/^'.$calendar_slash_required.'event\/' . $event . '\/datetime\/' . $event_datetime . '\/delete\/recurrence\/' . $recurrence_id . '(\/)?$/'] = 'UNL\UCBCN\Manager\DeleteRecurrence';
$routes['/^'.$calendar_slash_required.'event\/' . $event . '\/datetime\/' . $event_datetime . '\/delete(\/)?$/'] = 'UNL\UCBCN\Manager\DeleteDateTime';
$routes['/^'.$calendar_slash_required.'event\/' . $event . '\/delete(\/)?$/'] = 'UNL\UCBCN\Manager\DeleteEvent';
$routes['/^calendar\/new(\/)?$/'] = 'UNL\UCBCN\Manager\CreateCalendar';
$routes['/^'.$calendar_slash_required.'edit(\/)?$/'] = 'UNL\UCBCN\Manager\CreateCalendar';
$routes['/^'.$calendar_slash_required.'subscriptions\/new(\/)?$/'] = 'UNL\UCBCN\Manager\CreateSubscription';
$routes['/^'.$calendar_slash_required.'subscriptions\/' . $subscription . '\/edit(\/)?$/'] = 'UNL\UCBCN\Manager\CreateSubscription';
$routes['/^'.$calendar_slash_required.'subscriptions\/' . $subscription . '\/delete(\/)?$/'] = 'UNL\UCBCN\Manager\DeleteSubscription';
$routes['/^'.$calendar_slash_required.'subscriptions(\/)?$/'] = 'UNL\UCBCN\Manager\Subscription';
$routes['/^'.$calendar_slash_required.'users(\/)?$/'] = 'UNL\UCBCN\Manager\Users';
$routes['/^'.$calendar_slash_required.'users\/new(\/)?$/'] = 'UNL\UCBCN\Manager\AddUser';
$routes['/^'.$calendar_slash_required.'users\/' . $user . '\/edit(\/)?$/'] = 'UNL\UCBCN\Manager\AddUser';
$routes['/^'.$calendar_slash_required.'users\/' . $user . '\/delete(\/)?$/'] = 'UNL\UCBCN\Manager\DeleteUser';
$routes['/^'.$calendar_slash_required.'event\/' . $event . '\/recommend(\/)?$/'] = 'UNL\UCBCN\Manager\Recommend';
$routes['/^'.$calendar_slash_required.'search(\/)?$/'] = 'UNL\UCBCN\Manager\Search';

return $routes;