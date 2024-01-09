<!-- header.php -->

<?php
// 로그인 여부에 따라 다른 내용을 출력할 변수 설정
if (isset($_SESSION['sName'])) {
    $logoLink = 'main.php';
    $navItems = '<ul>
            <li><a href="/main.php" id="homeLink">Home</a></li>
            <li><a href="/myinfo.php" id="myPageLink">MyPage</a></li>
            <li><a id="logoutButton" style="cursor: pointer">Logout</a></li>
        </ul>';
    $sessionTime = '<span id="sessionTime" style="color: white"></span>';
} else {
    $logoLink = 'index.php';
    $navItems = '
        <ul>
            <li><a href="index.php">Login</a></li>
        </ul>';
    $sessionTime = "";
}
?>

<header>
    <a href="<?php echo $logoLink; ?>" id="logo">MK게시판</a>
    <nav>
        <?php echo $navItems; ?>
    </nav>
    <?php echo $sessionTime; ?>
</header>
