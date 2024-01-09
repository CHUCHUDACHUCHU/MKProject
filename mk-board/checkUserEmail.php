<?php

require_once('./vendor/autoload.php');  // autoload 경로에 따라 변경

$userController = new UserController();
$userController->checkUserEmail($_GET['userEmail']);
?>