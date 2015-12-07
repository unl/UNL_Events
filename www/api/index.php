<?php

namespace UNL\UCBCN\API;
global $_API_USER;

$config_file = __DIR__ . '/../../config.sample.php';

if (file_exists(__DIR__ . '/../../config.inc.php')) {
    $config_file = __DIR__ . '/../../config.inc.php';
}
require_once $config_file;
require_once __DIR__ . '/../../vendor/composer/autoload.php';

use RegExpRouter as RegExpRouter;

$auth = new Auth;
$token = NULL;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$token = $_POST['api_token'];
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$token = $_GET['api_token'];
} 

$user = $auth->authenticateViaToken($token);
if (!$user) {
	http_response_code(403);
	echo 'Unauthorized';
	exit;
} else {
	$_API_USER = $user;
}

$routes = include __DIR__ . '/../../data/api_routes.php';
$router = new RegExpRouter\Router(array('baseURL' => Controller::$url));
$router->setRoutes($routes);

$controller_options = $router->route($_SERVER['REQUEST_URI'], $_GET);
$controller = new Controller($controller_options);
