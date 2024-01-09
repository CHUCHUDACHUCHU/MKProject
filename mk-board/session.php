<?php
session_start();
if (!isset($_SESSION['sName'])) {
    session_unset();
    //쿠키삭제
    setcookie(session_name(), '', time() - 3600, '/');
    echo "
    <script>
        alert(\"로그인해주세요.\");
        location.href = \"./index.php\";
    </script>
    ";
}
?>