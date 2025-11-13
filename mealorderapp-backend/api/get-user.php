<?php
include '../db.php';
header('Content-Type: application/json');

$mobile = $_GET['mobile'] ?? '';

if(empty($mobile)){
    echo json_encode(["success" => false, "message" => "Mobile required"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, address, mobile FROM users WHERE mobile=?");
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $user = $result->fetch_assoc();
    echo json_encode(["success" => true, "user" => $user]);
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}
