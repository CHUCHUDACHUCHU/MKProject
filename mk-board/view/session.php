<?php
session_start();
if (!isset($_SESSION['userIdx'])) {
    session_unset();
    //쿠키삭제
    setcookie(session_name(), '', time() - 3600, '/');
    //여기서 세션이 없다면은 다시 로그인페이지(/auth/login)으로 리다이렉트!
    echo '<script>alert("세션정보가 손상되었습니다. 로그인 하시오.");</script>';
    echo '<script>window.location.href = "/mk-board/auth/login";</script>';
    exit;
}
?>