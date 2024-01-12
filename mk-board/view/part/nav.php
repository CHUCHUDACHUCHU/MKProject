<!-- Navigation Bar -->
<nav class="navbar navbar-expand navbar-dark bg-primary">
    <a class="navbar-brand" href="/mk-board/post/list">MK게시판</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <div class="container-fluid">
            <div class="navbar-nav mx-auto"> <!-- mx-auto: 가운데 정렬 클래스 -->
                <a class="nav-link" href="/mk-board/post/list" id="homeLink">Home</a>
                <a class="nav-link" href="/mk-board/user/mypage" id="mypageLink">MyPage</a>
                <a class="nav-link" href="/mk-board/user/mypage" id="mypageLink">👤 <?= $_SESSION['userName'] ?>
                    <span style="font-weight: bold; color: black; font-size: 15px"> 님</span>
                </a>
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