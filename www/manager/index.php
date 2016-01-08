<?php

namespace UNL\UCBCN\Manager;

$config_file = __DIR__ . '/../../config.sample.php';

if (file_exists(__DIR__ . '/../../config.inc.php')) {
    $config_file = __DIR__ . '/../../config.inc.php';
}
require_once $config_file;

require_once __DIR__ . '/../../vendor/composer/autoload.php';

use RegExpRouter as RegExpRouter;

$auth = new Auth;
$auth->authenticate();

$routes = include __DIR__ . '/../../data/manager_routes.php';
$router = new RegExpRouter\Router(array('baseURL' => Controller::$url));
$router->setRoutes($routes);

$controller_options = $router->route($_SERVER['REQUEST_URI'], $_GET);

$controller = new Controller($controller_options);

$savvy = new OutputController($controller);
$savvy->addGlobal('controller', $controller);
$savvy->addGlobal('base_manager_url', Controller::$url);
$savvy->addGlobal('base_frontend_url', \UNL\UCBCN\Frontend\Controller::$url);
$savvy->addGlobal('user', $auth->getCurrentUser());
echo $savvy->render($controller);
