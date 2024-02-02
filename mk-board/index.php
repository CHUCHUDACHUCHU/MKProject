<?php
require_once 'vendor/autoload.php';

use Controller\AccessController;
use Route\AuthRoute;
use Route\UserRoute;
use Route\PostRoute;
use Route\CommentRoute;
use Route\FileRoute;
use Route\LogRoute;
use Route\EmailRoute;
use Route\TestRoute;


// Start session
session_start();

// Get URL
$url = isset($_GET['url']) ? $_GET['url'] : '/';

$access = new AccessController();
$access->accessFilter($url);
$routes = [
    new AuthRoute(),
    new UserRoute(),
    new PostRoute(),
    new CommentRoute(),
    new FileRoute(),
    new LogRoute(),
    new EmailRoute(),
    new TestRoute()
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

