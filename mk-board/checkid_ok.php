<?php
include('./connect.php');

//$userEmail = $_GET['userEmail'];
//$sql = "SELECT * FROM userTB WHERE userEmail = ?";
//$stmt = mysqli_prepare($con, $sql);
//
//if ($stmt) {
//    mysqli_stmt_bind_param($stmt, 's', $userEmail);
//    mysqli_stmt_execute($stmt);
//
//    mysqli_stmt_store_result($stmt);
//
//    $response = new stdClass();
//    $response->result = (mysqli_stmt_num_rows($stmt) > 0) ? "X" : "O";
//
//    echo json_encode($response);
//
//    mysqli_stmt_close($stmt);
//} else {
//    $response = new stdClass();
//    $response->result = "Error in query preparing statement";
//
//    echo json_encode($response);
//}
//
//mysqli_close($con);

try {
    $userEmail = $_GET['userEmail'];
    $stmt = $conn->prepare("SELECT * FROM userTB WHERE userEmail = :userEmail");

    $stmt->bindValue(':userEmail', $userEmail);
    $stmt->execute();
    $rst = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = new stdClass();
    $response->result = count($rst) > 0 ? "X" : "O";

    echo json_encode($response);
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>