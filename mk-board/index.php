<?php
require_once "bootstrap.php";

use Route\PostRoute;

// URL 요청
$url = isset($_GET['url']) ? $_GET['url'] : '/';

// 루트 경로로 접근 시 게시글 목록으로 리다이렉트
if ($url == '/' || $url == '') {
    header('Location: post/list');
} else {
    $routes = array();
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