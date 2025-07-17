<?php
use UNL\Templates\Templates;
use UNL\UCBCN\Util;
use UNL\UCBCN\Manager\Auth;

$page = Templates::factory('AppLocal', Templates::VERSION_6_0);

if (file_exists(\UNL\UCBCN\Util::getWWWRoot() . '/wdn/templates_6.0')) {
    $page->setLocalIncludePath(\UNL\UCBCN\Util::getWWWRoot());
}

$title = '';
$site_title = 'UNL Events';
if (!$context->getCalendar()) {
    $title .= 'UNL Events';
    $cal_url = "/";
} else {
    $title .= 'UNL';
    if ($context->getCalendar()->id != UNL\UCBCN\Frontend\Controller::$default_calendar_id) {
        $title .= ' | '.$context->getCalendar()->name.' ';
    }
    $title .= ' | Manager | Events';
    $site_title = $context->getCalendar()->name . ' Events Manager';
    $cal_url = $context->getCalendar()->getFrontendURL();
}
$view_class = str_replace('\\', '_', strtolower($context->options['model']));

//Document titles
$page->doctitle = '<title>' . $title . '</title>';
$page->titlegraphic = '<a class="dcf-txt-h5" href="' . $cal_url . '">' . $site_title . '</a>';
$page->affiliation = '';

//css
$page->addStyleSheet($base_frontend_url.'templates/default/html/css/events.css?v='.UNL\UCBCN\Frontend\Controller::$version);
$page->addStyleSheet($base_frontend_url.'templates/default/html/css/manager.css?v='.UNL\UCBCN\Frontend\Controller::$version);

// no menu items, so hide mobile menu
$page->addStyleDeclaration("#dcf-mobile-toggle-menu {display: none!important}");

//javascript
$page->addScriptDeclaration('window.UNL.idm.pushConfig("logoutRoute", "' . Util::getBaseURL() . '/manager/logout");', '', true);
$page->addScriptDeclaration('window.UNL.autoLoader.config.globalOptOutSelector=".events-autoload-ignore";', '', true);
$auth = new Auth();
if ($auth->isAuthenticated()) {
    $userId = $auth->getCASUserId();
    $page->addScriptDeclaration('window.UNL.idm.pushConfig("serverUser", "' . $userId . '");', '', true);
}
$page->addScriptDeclaration('var frontend_url = "'.$base_frontend_url.'";');
$page->addScriptDeclaration('var manager_url = "'.$base_manager_url.'";');
$page->addScriptDeclaration("WDN.initializePlugin('notice');");
$page->addScriptDeclaration("
require(['jquery'], function ($) {
    $('#breadcrumbs > ul > li > a').last().parent().addClass('last-link');
});");
$page->addScript($base_frontend_url .'templates/default/html/js/manager.min.js?v='.UNL\UCBCN\Frontend\Controller::$version);

//other
$savvy->addGlobal('page', $page);

//Render output
$template = null;
if ($context->output->getRawObject() instanceof Exception) {
    $template = 'Exception.tpl.php';
}

$page->maincontentarea = '
<div class="dcf-bleed view-' . $view_class . ' events-manager">
    <div class="dcf-wrapper">';
if ($_SERVER['SERVER_NAME'] == 'events-dev.unl.edu') {
    $page->maincontentarea .= '<div class="dcf-notice" hidden>
    <h2>UNL Events Test</h2>
    <div>This is the test server for UNL Events. Events created and published here will not affect the main UNL calendar or your site\'s sub-calendar. Please send all feedback to <a href="mailto:dxg@listserv.unl.edu">the dev team</a> at dxg@listserv.unl.edu.</div>
</div>';
}

$page->maincontentarea .= '
        <section class="dcf-d-grid dcf-grid-cols-12 dcf-col-gap-vw dcf-pb-8">
            <div class="dcf-col-span-12 dcf-col-span-9@md">
';

if (($notice = $context->getNotice()) != NULL) {
    $class = '';
    switch ($notice['level']) {
        case 'success':
            $class = 'dcf-notice-success';
            break;
        case 'failure':
            $class = 'dcf-notice-danger';
            break;
        case 'alert':
            $class = 'dcf-notice-warning';
            break;
        default:
            $class = 'dcf-notice-info';
    }
    $page->maincontentarea .= '<div id="noticeContainer"><div id="notice" class="dcf-notice ' . $class . '" hidden data-no-close-button>
    <h2>' . $notice['header'] . '</h2>
    <div>' . html_entity_decode($notice['messageHTML']) . '</div>
</div></div>';
} else {
    $page->maincontentarea .= '<div id="noticeContainer"><div id="notice" class="dcf-notice" hidden data-no-close-button style="display: none!important">
    <h2>Message Header</h2>
    <div>Message Content</div>
</div></div>';
}
$page->maincontentarea .= $savvy->render($context->output, $template) . '
            <br>
            </div>
            <div class="dcf-col-span-12 dcf-col-span-3@sm">
                <nav class="calendars-list">
                    ' . $savvy->render($context, 'navigation.tpl.php') . '
                </nav>
            </div>
        </section>
    </div>
</div>
';

$page->contactinfo = $savvy->render($context, 'html/localfooter.tpl.php');

if (isset($siteNotice) && $siteNotice->display) {
    $page->displayDCFNoticeMessage($siteNotice->title, $siteNotice->message, $siteNotice->type, $siteNotice->noticePath, $siteNotice->containerID);
}

//echo everything
echo $page;
