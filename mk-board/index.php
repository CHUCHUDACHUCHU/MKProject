<?php
require_once 'vendor/autoload.php';
use Controller\BaseController;
use Route\AuthRoute;
use Route\UserRoute;
use Route\PostRoute;
use Route\TestRoute;

$BaseController = new BaseController();
// 세션 시작
session_start();

// URL 요청!!!
$url = isset($_GET['url']) ? $_GET['url'] : '/';

if($url == 'auth/login') {
    if(isset($_SESSION['userIdx'])) {
        $BaseController->redirect('/mk-board/post/list', '');
    }
} else if ($url == '/' || $url == '') {
    $BaseController->redirect('/mk-board/auth/login', '');
}

if (!isset($_SESSION['userIdx']) && $url !== 'auth/login') {
    $BaseController->redirect('/mk-board/auth/login', '로그인이 필요합니다.');
} else if ($_SESSION['userStatus'] === '대기' && $url !== 'user/my-page') {
    // 비밀번호를 변경하지 않은 상태이면서 변경 페이지로 이동하지 않았다면 변경 페이지로 리다이렉션
//    echo '<script>alert("비밀번호 변경이 필요합니다!");</script>';
//    echo '<script>location.href="/mk-board/user/my-page"</script>';
    $BaseController->redirect('/mk-board/user/my-page', '비밀번호 변경이 필요합니다!');
} else {
    $routes = array();
    $routes[] = new AuthRoute();
    $routes[] = new UserRoute();
    $routes[] = new PostRoute();
    $routes[] = new TestRoute();

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