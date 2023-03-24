<?php
/*
 * Map of regular expressions which map to models the controller will construct
 */
$routes         = array();

/* Commonly used regular expressions */

// Optional calendar short name, which prefixes all routes
$calendar                = '(?P<calendar_shortname>([a-zA-Z-_0-9]+)+)';
$calendar_slash_required = '(' . $calendar . '\/)?';
$calendar_slash_optional = '(' . $calendar . '(\/)?)?';

// Date specific regular expressions
$year     = '(?P<y>[\d]{4})';
$month    = '(?P<m>([0-1])?[0-9])';
$day      = '(?P<d>([0-3])?[0-9])';
$week     = 'W(?P<w>[0-5][0-9])';
$from   = '(?P<from>[\d]{4}-([0-1])?[0-9]-([0-3])?[0-9])';
$to   = '(?P<to>[\d]{4}-([0-1])?[0-9]-([0-3])?[0-9])';

// Used for determining an output format, e.g. .xml, .html
$format   = '(\.(?P<format>[\w]+))?';

// Used for adding optional trailing slash
$optional_trailing_slash = '(\/)?';

$routes['/^images\/(?P<id>[\d]+)$/']                                                                       = 'UNL\UCBCN\Frontend\Image';
$routes['/^'.'audience'.$optional_trailing_slash.'$/']                                                     = 'UNL\UCBCN\Frontend\Audience';
$routes['/^'.'eventtype'.$optional_trailing_slash.'$/']                                                    = 'UNL\UCBCN\Frontend\EventType';
$routes['/^'.$calendar_slash_required.'upcoming'.'(\/)?'.$format.'$/']                                     = 'UNL\UCBCN\Frontend\Upcoming';
$routes['/^'.$calendar_slash_required.'featured'.'(\/)?'.$format.'$/']                                     = 'UNL\UCBCN\Frontend\Featured';
$routes['/^'.$calendar_slash_required.'range'.'(\/)?'.$format.'$/']                                        = 'UNL\UCBCN\Frontend\Range';
$routes['/^'.$calendar_slash_required.'range'.'(\/)?'.$from.'(\/)?'.$format.'$/']                          = 'UNL\UCBCN\Frontend\Range';
$routes['/^'.$calendar_slash_required.'range'.'(\/)?'.$from.'\/'.$to.'(\/)?'.$format.'$/']                 = 'UNL\UCBCN\Frontend\Range';
$routes['/^'.$calendar_slash_required.'range'.'(\/)?'.$from.'(\/)?'.$format.'$/']                          = 'UNL\UCBCN\Frontend\Range';
$routes['/^'.$calendar_slash_required.'week'.'(\/)?'.$format.'$/']                                         = 'UNL\UCBCN\Frontend\Week';
$routes['/^'.$calendar_slash_required.'search'.$optional_trailing_slash.'$/']                              = 'UNL\UCBCN\Frontend\Search';
$routes['/^'.$calendar_slash_required.$year.'(\/)?'.$format.'$/']                                          = 'UNL\UCBCN\Frontend\Year';
$routes['/^'.$calendar_slash_required.$year.'\/'.$month.'(\/)?'.$format.'$/']                              = 'UNL\UCBCN\Frontend\Month';
$routes['/^'.$calendar_slash_required.$year.'\/'.$month.'\/widget(\/)?'.$format.'$/']                      = 'UNL\UCBCN\Frontend\MonthWidget';
$routes['/^'.$calendar_slash_required.$year.'\/'.$week.'(\/)?'.$format.'$/']                               = 'UNL\UCBCN\Frontend\Week';
$routes['/^'.$calendar_slash_required.$year.'\/'.$month.'\/'.$day.'(\/)?'.$format.'$/']                    = 'UNL\UCBCN\Frontend\Day';
$routes['/^'.$calendar_slash_required.$year.'\/'.$month.'\/'.$day.'\/(?P<id>[\d]+)'.'(\/)?'.$format.'$/']  = 'UNL\UCBCN\Frontend\EventInstance';
$routes['/^'.$calendar_slash_optional.'$/']                                                                = 'UNL\UCBCN\Frontend\Upcoming';

return $routes;
