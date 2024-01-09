<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="../assets/js/join.js"></script>
</head>
<body>
<?php include('./header.php') ?>
<main class="wrap" id="join_wrap">
    <h1>Sign Up</h1>
    <section>
        <form action="../join_ok.php" name="joinForm" method="POST" class="form" id="login_form" onsubmit="return sendIt()">
            <p>
                <input type="text" name="userName" id="user_name" placeholder="Name">
            </p>
            <p>
                <span id = "result" style="display: block; font-size: 10px"></span>
                <input type="text" name="userEmail" id="user_email" placeholder="Email">
                <input type="button" id="check_email_btn" value="check" onclick="checkEmail()">
            </p>
            <p>
                <span id = "password-check-message" style="display: block; margin-left: 5px; font-size: 10px"></span>
                <input type="password" name="userPw" id="user_pw" placeholder="Password" onblur="checkPassword()">
            </p>
            <p>
                <span id = "password-match-message" style="display: block; margin-left: 5px; font-size: 10px"></span>
                <input type="password" name="userPwCheck" id="userpw_ch" placeholder="Password Check" onblur="checkPasswordMatch()">
            </p>
            <p>
                <input type="text" name="userDepart" id="user_depart" placeholder="Department 나중에... 드롭박스로!">
            </p>
            <p>
                <input type="text" name="userPhone" id="user_phone" placeholder="Phone Number 000-0000-0000">
            </p>
            <p>
                <input type="submit" value="Sign Up" class="form_btn">
            </p>

            <p class="pre_btn">Are you join? <a href="../index.php">Login.</a></p>
        </form>
    </section>
</main>
<footer></footer>
</body>
</html>