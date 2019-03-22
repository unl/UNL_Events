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
