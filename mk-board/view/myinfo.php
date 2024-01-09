<?php
include('./session.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['sName'];?> Main Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src='../assets/js/common.js'></script>
</head>
<body>
<?php include('./header.php')?>
<main class="wrap" id="myinfo_wrap">
    <section>
        <h1>파일전송 웹 어플리케이션 마이페이지</h1>
        <div id="content_wrap">
            <div id="img_content">
                <img src="../assets/img/logo.png"></img>
                <h2><?php echo $_SESSION['sName']; ?>'s!!</h2>
                <h3>E-mail : <?php echo $_SESSION['sEmail'];?></h3>
                <h3>등록날짜 : <?php echo $_SESSION['sCreatedAt']; ?></h3>
            </div>
            <div id="flex_content">
                <div class="content">
                    <p class="content_data">
                    <h2>My data0!!</h2>
                    <p>data data data</p>
                    </p>
                </div>
                <div class="content">
                    <p class="content_data">
                    <h2>My data1!</h2>
                    </p>
                    <p>data data data</p>
                </div>
                <div class="content">
                    <p class="content_data">
                    <h2>My data2!</h2>
                    </p>
                    <p>data data data</p>
                </div>
            </div>
        </div>
    </section>
</main>
<footer></footer>
</body>
</html>