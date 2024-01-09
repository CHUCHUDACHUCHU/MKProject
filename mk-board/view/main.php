<?php
include('./session.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['sName'];?> Main Page!</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src='../assets/js/common.js'></script>
</head>
<body>
<?php include('./header.php')?>
<div>
    <h1> 메인페이지!!! </h1>
</div>
<footer></footer>
</body>
</html>