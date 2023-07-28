<?php

namespace UNL\UCBCN\API;
global $_API_USER;

ini_set('html_errors', 0);

$config_file = __DIR__ . '/../../config.sample.php';

if (file_exists(__DIR__ . '/../../config.inc.php')) {
    $config_file = __DIR__ . '/../../config.inc.php';
}
require_once $config_file;
require_once __DIR__ . '/../../vendor/composer/autoload.php';

use RegExpRouter as RegExpRouter;

$auth = new \UNL\UCBCN\Manager\Auth;
$token = NULL;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$token = array_key_exists('api_token', $_POST) ? $_POST['api_token'] : '';
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$token = array_key_exists('api_token', $_GET) ? $_GET['api_token'] : '';
} 

$user = $auth->authenticateViaToken($token);
if (!$user || empty($token)) {
	http_response_code(403);
	echo 'Unauthorized';
	exit;
} else {
	$_API_USER = $user;
}

$routes = include_once __DIR__ . '/../../data/api_routes.php';
$router = new RegExpRouter\Router(array('baseURL' => Controller::$url));
$router->setRoutes($routes);

$controller_options = $router->route($_SERVER['REQUEST_URI'], $_GET);
$controller = new Controller($controller_options);

http_response_code(200);
echo $controller->output;
exit;
