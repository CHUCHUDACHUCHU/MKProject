<?php
use Model\User;
$user = new User();
$nowUser = $user->getUserById($_SESSION['userIdx']);
if($nowUser['userStatus'] == "관리자") {
    $navbarItems = '<a class="nav-link" href="/mk-board/post/list" id="homeNav">Home</a>
                <a class="nav-link" href="/mk-board/post/manage" id="managePostNav">게시글 관리</a>
                <a class="nav-link" href="/mk-board/user/manage" id="manageUserNav">회원 관리</a>
                <a class="nav-link" href="/mk-board/user/my-page" id="myPageNav">MyPage</a>
                <a class="nav-link">👤 
                    <span style="font-weight: bold; color: black; font-size: 15px">'. $nowUser["userName"]. '님</span>
                </a>';
} else {
    $navbarItems = '<a class="nav-link" href="/mk-board/post/list" id="homeNav">Home</a>
                <a class="nav-link" href="/mk-board/user/my-page" id="myPageNav">MyPage</a>
                <a class="nav-link">👤 
                    <span style="font-weight: bold; color: black; font-size: 15px">'. $nowUser["userName"]. '님</span>
                </a>';
}

?>
<nav class="navbar navbar-expand navbar-dark bg-primary">
    <a class="navbar-brand" href="/mk-board/post/list">MK게시판</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <div class="container-fluid">
            <div class="navbar-nav mx-auto"> <!-- mx-auto: 가운데 정렬 클래스 -->
                <?= $navbarItems ?>
            </div>
        </div>
    </div>
    <div class="navbar-text ml-auto" style="font-size: 15px; color: black; font-weight: bold">
        <span id="sessionTime"></span>
        <a href="/mk-board/auth/logout">
            <img src="/mk-board/assets/img/logout.png" alt="Logout" width="50" height="50">
        </a>
    </div>
</nav>