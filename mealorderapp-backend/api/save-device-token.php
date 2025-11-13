<?php
// mealorderapp-backend/api/save-device-token.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

include '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || empty($data['user_id']) || empty($data['device_token'])) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$user_id = intval($data['user_id']);
$token = $data['device_token'];
$platform = isset($data['platform']) ? $data['platform'] : null;

$stmt = $conn->prepare("INSERT INTO user_devices (user_id, device_token, platform) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE platform = VALUES(platform), created_at = NOW()");
$stmt->bind_param("iss", $user_id, $token, $platform);
$ok = $stmt->execute();
$stmt->close();

echo json_encode(["success" => (bool)$ok]);
$conn->close();
