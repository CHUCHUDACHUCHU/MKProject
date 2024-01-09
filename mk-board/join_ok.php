<?php
include('./connect.php');
//if($con) echo "OK\n";
//else echo "Fail\n";

$userName = $_POST['userName'];
$userEmail = $_POST['userEmail'];
$userPw = $_POST['userPw'];
$userDepart = $_POST['userDepart'];
$userPhone = $_POST['userPhone'];

$salt = '$5$QOPrAVIK'."$userEmail".'$';
$hashPw = crypt($userPw, $salt);

$sql = " INSERT INTO userTB
        (userName, userEmail, userPw, userDepart, userPhone)
        VALUES('$userName', '$userEmail', '$hashPw', '$userDepart', '$userPhone')
";

$rst = mysqli_query($con, $sql);

if($rst){
    echo"
        <script>
            alert(\"회원가입이 완료되었습니다.\");
            location.href = \"./index.php\";
        </script>
    ";
}else{
    echo " 
	    <script>
            alert(\"회원가입이 실패하였습니다.\");
            history.back();
        </script>
	";
    exit;
}

?>