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
    $title .= ' | Manger | Events';
    $site_title = $context->getCalendar()->name . ' Events';
}
$view_class = str_replace('\\', '_', strtolower($context->options['model']));

//Document titles
$page->doctitle     = '<title>' . $title . '</title>';
$page->titlegraphic = $site_title;
$page->pagetitle    = '';

//css
$page->addStyleSheet($base_frontend_url.'templates/default/html/css/events.css');

//javascript
$page->head .= '<script>var frontend_url = "'.$base_frontend_url.'";</script>' . PHP_EOL;
$page->head .= '<script>var manager_url = "'.$base_manager_url.'";</script>' . PHP_EOL;
$page->addScript($base_frontend_url.'templates/default/html/js/events.min.js');

//other
$page->leftRandomPromo = '';
$page->breadcrumbs = '
<ul>
    <li><a href="http://www.unl.edu/">UNL</a></li>
    <li><a href="' . $base_frontend_url .'">UNL Events</a></li>
    <li>Manage Events</li>
</ul>';
//$page->navlinks = $savvy->render(null, 'Navigation.tpl.php');
$savvy->addGlobal('page', $page);

//Render output
$template = null;
if ($context->output->getRawObject() instanceof Exception) {
    $template = 'Exception.tpl.php';
}

$page->maincontentarea = '
<div class="wdn-band view-' . $view_class . ' band-results">
    <div class="wdn-inner-wrapper">
        <section class="wdn-grid-set">
            <div class="wdn-col-one-fourth">
                <h3>My Calendars</h3>
                ' . $savvy->render($context, 'navigation.tpl.php') . '
            </div>
            <div class="wdn-col-three-fourths">
                ' . $savvy->render($context->output, $template) . '
           </div>
        </section>
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