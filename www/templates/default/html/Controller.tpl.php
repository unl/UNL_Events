<?php
UNL_Templates::$options['version'] = 4.0;
$page = UNL_Templates::factory('Fixed');

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
$view_class = str_replace('\\', '_', strtolower($context->options['model']));

//Document titles
$page->doctitle     = '<title>' . $title . '</title>';
$page->titlegraphic = $site_title;
$page->pagetitle    = '';

//css
$page->addStyleSheet($frontend->getURL().'templates/default/html/css/events.css');

//javascript
$page->head .= '<script>var frontend_url = "'.$frontend->getURL().'";</script>' . PHP_EOL;
$page->addScript($frontend->getURL().'templates/default/html/js/events.min.js');

//other head
if ($context->getCalendar()) {
    $page->head .= '<link rel="alternate" type="application/rss+xml" title="' . $context->getCalendar()->name . ' Events" href="' . $frontend->getCalendarURL() . '.rss" />' . PHP_EOL;
}

if ($context->getRaw('output') instanceof UNL\UCBCN\Frontend\RoutableInterface) {
    $page->head .= '<link rel = "canonical" href = "' . $context->output->getURL() . '" />' . PHP_EOL;
}

$page->head .= '<link rel="home" href="' . $context->getCalendarURL() . '" />' . PHP_EOL;

//other
$page->leftRandomPromo = '';
$page->breadcrumbs = '
<ul>
    <li><a href="http://www.unl.edu/">UNL</a></li>
    <li><a href="' . $frontend->getURL() .'">UNL Events</a></li>
    <li>Events</li>
</ul>';
//$page->navlinks = $savvy->render(null, 'Navigation.tpl.php');
$savvy->addGlobal('page', $page);

//Render output
if ($context->getCalendar()) {
    $page->maincontentarea = '
            <div class="wdn-band view-' . $view_class . ' band-nav">
                <div class="wdn-inner-wrapper">
                    <div class="wdn-grid-set">
                        <div class="wdn-col-full">
                            <div class="events-nav">
                                <div class="submit-search">
                                    <a id="frontend_login" class="eventicon-plus-circled" href="' . UNL\UCBCN\Frontend\Controller::$manager_url . '">Submit an Event</a>
                                    <form id="event_search" method="get" action="' . $frontend->getCalendarURL() . 'search/" role="search">
                                        <label for="searchinput">Search Events</label>
                                        <div class="wdn-input-group">
                                            <input type="text" name="q" id="searchinput" title="Search Query" placeholder="e.g., Monday, tomorrow" value="' . ((isset($context->options['q']))?$context->options['q']:'') . '" />
                                            <span class="wdn-input-group-btn">
                                                <button type="submit" class="wdn-icon-search" title="Search"></button>
                                            </span>
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
                </div>
            </div>';
}

$template = null;
if ($context->output->getRawObject() instanceof Exception) {
    $template = 'Exception.tpl.php';
}
$page->maincontentarea .= ' 
    <div class="wdn-band view-' . $view_class . ' band-results">
        <div class="wdn-inner-wrapper">
            ' . $savvy->render($context->output, $template) . '
        </div>
    </div>';

$page->contactinfo = '
<p>University of Nebraska&ndash;Lincoln<br />
1400 R Street<br />
Lincoln, NE 68588<br />
402-472-7211</p>';
$page->footercontent = $page->footercontent = '© '.date('Y').' University of Nebraska–Lincoln · Lincoln, NE 68588 · 402-472-7211<br />
    The University of Nebraska–Lincoln is an <a href="http://www.unl.edu/equity/">equal opportunity</a> educator and employer.';

//echo everything
echo $page;