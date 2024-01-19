<?php
require_once 'vendor/autoload.php';

use Controller\AccessController;
use Route\AuthRoute;
use Route\UserRoute;
use Route\PostRoute;
use Route\TestRoute;

$access = new AccessController();

// Start session
session_start();

// Get URL
$url = isset($_GET['url']) ? $_GET['url'] : '/';

$access->accessFilter($url);
$routes = [
    new AuthRoute(),
    new UserRoute(),
    new PostRoute(),
    new TestRoute(),
];

$routeMatched = false;

foreach ($routes as $route) {
    if ($route->routing($url)) {
        $routeMatched = true;
        break;
    }
}

if (!$routeMatched) {
    header("HTTP/1.0 404 Not Found");
    require_once "view/404.php";
}

