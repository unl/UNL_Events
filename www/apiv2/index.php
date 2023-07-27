<?php

namespace UNL\UCBCN\APIv2;

// We do not want any HTML errors
ini_set('html_errors', 0);

// Includes the main files we need
$config_file = __DIR__ . '/../../config.sample.php';

if (file_exists(__DIR__ . '/../../config.inc.php')) {
    $config_file = __DIR__ . '/../../config.inc.php';
}
require_once $config_file;
require_once __DIR__ . '/../../vendor/composer/autoload.php';

// Set up the routes
use RegExpRouter as RegExpRouter;
$routes = include_once __DIR__ . '/../../data/api_v2_routes.php';
$router = new RegExpRouter\Router(array('baseURL' => Controller::$url));
$router->setRoutes($routes);

header('Content-type:application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authentication, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Max-Age: 1728000');

// We typically do not allow options method but for the JS fetch preflight we need it
if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    exit;
}

// Set up the controller
$controller_options = $router->route($_SERVER['REQUEST_URI'], $_GET);
$controller = new Controller($controller_options);

// Output the status and the data
http_response_code($controller->output['status']);
echo json_encode($controller->output, JSON_UNESCAPED_SLASHES);
exit;
