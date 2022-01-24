<?php
/**
 * This file parses the configuration and connection details for the catalog database.
 * 
 * @package UNL_UCBCN
 * @author bbieber
 */

function autoload($class)
{
    $class = str_replace(array('_', '\\'), '/', $class);
    include $class . '.php';
}

spl_autoload_register('autoload');

set_include_path(
    __DIR__ . '/src' . PATH_SEPARATOR
    . __DIR__ . '/vendor/php' . PATH_SEPARATOR
    . __DIR__ . '/vendor/unl_submodules/RegExpRouter/src'
);

require __DIR__ . '/vendor/composer/autoload.php';

ini_set('display_errors', true);
error_reporting(E_ALL);

UNL\UCBCN::$main_calendar_id = 1;
UNL\UCBCN\Frontend\Controller::$url = '/';
UNL\UCBCN\Frontend\Controller::$manager_url = '/manager/';
UNL\UCBCN\Manager\Controller::$url = "/manager/";
UNL\UCBCN\ActiveRecord\Database::setDbSettings(
    array(
    'host'     => 'localhost',
    'user'     => 'events',
    'password' => 'password',
    'dbname'   => 'events',
));

UNL\UCBCN::$defaultTimezone = 'America/Chicago';
UNL\UCBCN::setTimezoneOptions(
    array(
        'Eastern' => 'America/New_York',
        'Central' => 'America/Chicago',
        'Mountain' => 'America/Denver',
        'Arizona' => 'America/Phoenix',
        'Pacific' => 'America/Los_Angeles'
    )
);

// CAS Auth Configuration
UNL\UCBCN\Manager\Auth::$eventsAuthSessionName = 'DEV_EVENTS_AUTH_SESSION_NAME';
UNL\UCBCN\Manager\Auth::$certPath = '/etc/pki/tls/cert.pem';
UNL\UCBCN\Manager\Auth::$directory_url = 'https://directory.unl.edu/';

// Site Notice
$siteNotice = new stdClass();
$siteNotice->display = false;
$siteNotice->noticePath = 'dcf-notice';
$siteNotice->containerID = 'dcf-main';
$siteNotice->type = 'dcf-notice-info';
$siteNotice->title = 'Maintenance Notice';
$siteNotice->message = 'We will be performing site maintenance on February 7th from 4:30 to 5:00 pm CST.  This site may not be available during this time.';
