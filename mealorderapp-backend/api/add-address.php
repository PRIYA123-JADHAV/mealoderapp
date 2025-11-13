<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['user_id']) || empty($data['address'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$user_id = intval($data['user_id']);
$address = $data['address'];

$stmt = $conn->prepare("INSERT INTO addresses (user_id, address, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $user_id, $address);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Address added successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Insert failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
