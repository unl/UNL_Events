<?php

const DISPLAY_TIMEZONE_NOTICE = 'displayTimeZoneNotice';

use UNL\Templates\Templates;
use UNL\UCBCN\Util;

$page = Templates::factory('AppLocal', Templates::VERSION_5_3);

if (file_exists(\UNL\UCBCN\Util::getWWWRoot() . '/wdn/templates_5.3')) {
    $page->setLocalIncludePath(\UNL\UCBCN\Util::getWWWRoot());
}

//Document titles
$title = '';
$site_title = 'UNL Events';
if (!$context->getCalendar()) {
    $title .= 'Page Not Found - UNL Events';
} else {
    $title .= 'UNL';
    if ($context->getCalendar()->id != UNL\UCBCN\Frontend\Controller::$default_calendar_id) {
        $title .= ' | '.$context->getCalendar()->name.' ';
    }
    $title .= ' | Events';
    $site_title = $context->getCalendar()->name . ' Events';
}

$page->doctitle = '<title>' . $title . '</title>';
$page->titlegraphic = '<a class="dcf-txt-h5" href="' . $context->getCalendarURL() . '">' . $site_title . '</a>';
$page->affiliation = '';

//css
$page->addStyleSheet($frontend->getURL().'templates/default/html/css/events.css?v='.UNL\UCBCN\Frontend\Controller::$version);

// no menu items, so hide mobile menu
$page->addStyleDeclaration("#dcf-mobile-toggle-menu {display: none!important}");

//javascript
$page->addScriptDeclaration('WDN.setPluginParam("idm", "logout", "' . Util::getBaseURL() . '/manager/logout");');
$page->addScriptDeclaration('var frontend_url = "'.$frontend->getURL().'";');
$page->addScript($frontend->getURL().'templates/default/html/js/events.min.js?v='.UNL\UCBCN\Frontend\Controller::$version);

//other head
if ($context->getCalendar()) {
    $page->head .= '<link rel="alternate" type="application/rss+xml" title="' . $context->getCalendar()->name . ' Events" href="' . $frontend->getCalendarURL() . '.rss" />' . PHP_EOL;
}

if ($context->getRaw('output') instanceof UNL\UCBCN\Frontend\RoutableInterface) {
    $page->head .= '<link rel = "canonical" href = "' . $context->output->getURL() . '" />' . PHP_EOL;
}

$page->head .= '<link rel="home" href="' . $context->getCalendarURL() . '" />' . PHP_EOL;

if ($context->getRaw('output') instanceof UNL\UCBCN\Frontend\MetaTagInterface) {
    $page->head .= $context->getRaw('output')->getMetaTags() . PHP_EOL;
}

//Render output
$savvy->addGlobal('page', $page);
$view_class = str_replace('\\', '_', strtolower($context->options['model']));

$page->maincontentarea = ''; // Clear
if ($context->getCalendar()) {
    $timezoneDisplay = \UNL\UCBCN::getTimezoneDisplay($context->getCalendar()->defaulttimezone);
    $calendarTimezone = array_search($context->getCalendar()->defaulttimezone, \UNL\UCBCN::getTimezoneOptions());
    $timezoneMessage = 'All events are in ' . $calendarTimezone . ' time unless specified.';

    // Need to for datetime display
    $savvy->addGlobal('timezoneDisplay', $timezoneDisplay);

    $page->maincontentarea = '
            <div class="dcf-bleed view-' . $view_class . ' band-nav">
                <div class="dcf-wrapper">';

    // Display timezone notice when calendar timezone is not app default and DISPLAY_TIMEZONE_NOTICE cookie not set or has changed
    if ($context->getCalendar()->defaulttimezone != UNL\UCBCN::$defaultTimezone && (empty($_COOKIE[DISPLAY_TIMEZONE_NOTICE]) || $_COOKIE[DISPLAY_TIMEZONE_NOTICE] != $context->getCalendar()->defaulttimezone)) {
        setcookie(DISPLAY_TIMEZONE_NOTICE, $context->getCalendar()->defaulttimezone);
        $page->addScriptDeclaration("WDN.initializePlugin('notice');");
        $page->maincontentarea .= '<div id="timezone-notice" class="dcf-notice dcf-notice-info" hidden><h2>Timezone Display</h2><div>' . $timezoneMessage . '</div></div>';
    }

    $page->maincontentarea .= '
                    <div class="dcf-grid">
                        <div class="dcf-col-100%">
                            <div class="events-nav">
                                <div class="submit-search">
                                    <a id="frontend_login" href="' . UNL\UCBCN\Frontend\Controller::$manager_url . $context->getCalendar()->shortname . '">Manage Events</a>
                                    <form class="dcf-form" id="event_search" method="get" action="' . $frontend->getCalendarURL() . 'search/" role="search">
                                        <label for="searchinput">Search Events</label>
                                        <div class="dcf-input-group">
                                            <input
                                                type="text"
                                                name="q"
                                                id="searchinput"
                                                placeholder="e.g., Monday, tomorrow"
                                                value="' .
                                                    (
                                                        (isset($context->options['q']
                                                    ) && (
                                                        strpos($context->output->getURL(), '/audience') === false &&
                                                        strpos($context->output->getURL(), '/eventtype') === false)
                                                    )?$context->options['q']:'') .
                                                '"/>

                                            <button class="dcf-btn dcf-btn-primary">
                                                <svg class="dcf-h-5 dcf-w-5 dcf-fill-current" aria-hidden="true" focusable="false" height="16" width="16" viewBox="0 0 48 48">
          <path d="M18 36a17.9 17.9 0 0 0 11.27-4l15.31 15.41a2 2 0 0 0 2.84-2.82L32.08 29.18A18 18 0 1 0 18 36zm0-32A14 14 0 1 1 4 18 14 14 0 0 1 18 4z"></path>
        </svg>
                                                <span class="dcf-sr-only">Search</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <ul id="frontend_view_selector" class="' . $view_class . '">
                                    <li id="todayview"><a href="' . $frontend->getCurrentDayURL() . '">Today</a></li>
                                    <li id="weekview"><a href="' .  $frontend->getCurrentWeekURL() . '">Week</a></li>
                                    <li id="monthview"><a href="' .  $frontend->getCurrentMonthURL() . '">Month</a></li>
                                    <li id="yearview"><a href="' .  $frontend->getCurrentYearURL() . '">Year</a></li>
                                    <li id="upcomingview"><a href="' .  $frontend->getUpcomingURL() . '">Upcoming</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <small class="events-timezone-msg">' . $timezoneMessage . '</small>
                </div>
            </div>';
}

$template = null;
if ($context->output->getRawObject() instanceof Exception) {
    $template = 'Exception.tpl.php';
}
$page->maincontentarea .= '
    <div class="dcf-bleed view-' . $view_class . ' band-results dcf-pb-8">
        <div class="dcf-wrapper">
            ' . $savvy->render($context->output, $template) . '
        </div>
    </div>';

$page->contactinfo = $savvy->render($context, 'localfooter.tpl.php');

if (isset($siteNotice) && $siteNotice->display) {
    $page->displayDCFNoticeMessage($siteNotice->title, $siteNotice->message, $siteNotice->type, $siteNotice->noticePath, $siteNotice->containerID);
}

//echo everything
echo $page;
