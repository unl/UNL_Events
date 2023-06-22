<?php

namespace UNL\UCBCN\APIv2;

ini_set('html_errors', 0);

$config_file = __DIR__ . '/../../config.sample.php';

if (file_exists(__DIR__ . '/../../config.inc.php')) {
    $config_file = __DIR__ . '/../../config.inc.php';
}
require_once $config_file;
require_once __DIR__ . '/../../vendor/composer/autoload.php';

use RegExpRouter as RegExpRouter;

$routes = include __DIR__ . '/../../data/api_v2_routes.php';
$router = new RegExpRouter\Router(array('baseURL' => Controller::$url));
$router->setRoutes($routes);

$controller_options = $router->route($_SERVER['REQUEST_URI'], $_GET);
$controller = new Controller($controller_options);

http_response_code($controller->output['status']);
echo json_encode($controller->output);
exit;
