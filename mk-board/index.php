<?php
require_once "bootstrap.php";

use Route\AuthRoute;
use Route\UserRoute;
use Route\PostRoute;

// URL 요청!!!
$url = isset($_GET['url']) ? $_GET['url'] : '/';

// 루트 경로로 접근 시 게시글 목록으로 리다이렉트
if ($url == '/' || $url == '') {
    header('Location: auth/login');
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

//session_start();
//require_once "bootstrap.php";
//
//use Route\AuthRoute;
//use Route\UserRoute;
//use Route\PostRoute;
//
//if (isset($_SESSION['userIdx'])) {
//    // URL 요청!!!
//    $url = isset($_GET['url']) ? $_GET['url'] : '/';
//    echo "<script>alert('$url')</script>";
//
//// 루트 경로로 접근 시 게시글 목록으로 리다이렉트
//    if ($url == 'auth/login') {
//        require_once "view/main.php";
//    } else {
//        $routes = array();
//        $routes[] = new AuthRoute();
//        $routes[] = new UserRoute();
//        $routes[] = new PostRoute();
//
//        $ok = false;
//        foreach ($routes as $route) {
//            // routing 함수를 돌며 false 리턴하는 게 있다면 404 페이지 출력
//            $ok = $route->routing($url);
//            if ($ok) {
//                break;
//            }
//        }
//
//        // 404 페이지 출력
//        if (!$ok) {
//            header("HTTP/1.0 404 Not Found");
//            require_once "view/404.php";
//        }
//    }
//} else {
//    // URL 요청!!!
//    $url = isset($_GET['url']) ? $_GET['url'] : '/';
//    if ($url == '/' || $url == '') {
//        header('Location: auth/login');
//    } else if() {
//        // 만약 세션정보는 들어있지 않은데 요청이 들어와서 확인해보니
//        // 실제 로그인 요청이라면? /mk-board/auth/login으로 post방식으로 userName과 userPw가 들어왔을 경우에는
//        // 실제로 AuthRoute ->
//    } else if() {
//        require_once "view/authLogin.php";
//        exit;
//    }
//}