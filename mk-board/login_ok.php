<?php
session_set_cookie_params(0);
session_start();
include('./connect.php');

$userEmail = $_POST['userEmail'];
$userPw = $_POST['userPw'];

$salt = '$5$QOPrAVIK'."$userEmail".'$';
$hashPw = crypt($userPw, $salt);

$sql = "select * from userTB where userEmail = '$userEmail';";
$rst = mysqli_query($con, $sql);
$id = mysqli_num_rows($rst);

if(!$id){
    echo "
        <script>
            alert(\"이메일과 비밀번호가 일치하지 않습니다.\");
            history.back();
        </script>
    ";
    exit;
} else{
    $user = mysqli_fetch_array($rst);
    $password = $user['userPw'];

    if($hashPw != $password){
        echo "
            <script>
                alert(\"이메일과 비밀번호가 일치하지 않습니다.\");
                history.back();
            </script>
        ";
        exit;
    } else{
        $_SESSION['sIdx'] = $user['userIdx'];
        $_SESSION['sEmail'] = $user['userEmail'];
        $_SESSION['sName'] = $user['userName'];
        $_SESSION['sDepart'] = $user['userDepart'];
        $_SESSION['sPhone'] = $user['userPhone'];
        $_SESSION['sCreatedAt'] = $user['createdAt'];
        $_SESSION['sUpdatedAt'] = $user['updatedAt'];

        mysqli_close($con);
        echo "
            <script>
                alert('확인되었습니다. 환영합니다! " .$_SESSION['sName'] ."님! ')
                location.href = \"./main.php\";
            </script>
        ";
    };
};

?>