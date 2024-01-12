<?php
require_once "bootstrap.php";

use Route\AuthRoute;
use Route\UserRoute;
use Route\PostRoute;

// 세션 시작
session_start();

// URL 요청!!!
$url = isset($_GET['url']) ? $_GET['url'] : '/';

if($url == 'auth/login') {
    if(isset($_SESSION['userIdx'])) {
        echo '<script>location.href="/mk-board/post/list"</script>';
    }
} else if ($url == '/' || $url == '') {
    echo '<script>location.href="/mk-board/auth/login"</script>';
}

if (!isset($_SESSION['userIdx']) && $url !== 'auth/login') {
    echo '<script>alert("로그인이 필요합니다.");</script>';
    echo '<script>location.href="/mk-board/auth/login"</script>';
} else {
    $routes = array();
    $routes[] = new AuthRoute();
    $routes[] = new UserRoute();
    $routes[] = new PostRoute();

    $ok = false;
    foreach ($routes as $route) {
        // routing 함수를 돌며 false 리턴하는 게 있다면 404 페이지 출력
        $ok = $route->routing($url);
        if ($ok) {
            break;
        }
    }

    // 404 페이지 출력
    if (!$ok) {
        header("HTTP/1.0 404 Not Found");
        require_once "view/404.php";
    }
}