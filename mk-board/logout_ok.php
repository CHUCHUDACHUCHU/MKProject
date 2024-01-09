<?php
session_start();
session_unset();

// JSON 형식의 응답을 출력
echo json_encode(['success' => true]);
?>