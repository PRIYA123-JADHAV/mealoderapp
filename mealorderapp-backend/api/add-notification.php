<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

include '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || empty($data['title']) || empty($data['message'])) {
    echo json_encode(["success" => false, "message" => "Missing title or message"]);
    exit;
}

$mobile = isset($data['mobile']) && $data['mobile'] !== '' ? $data['mobile'] : null;
$title = $data['title'];
$message = $data['message'];
$type = isset($data['type']) ? $data['type'] : 'system';
$related_id = isset($data['related_id']) ? intval($data['related_id']) : null;

if ($mobile === null) {
    $stmt = $conn->prepare("INSERT INTO notifications (mobile, title, message, type, related_id, is_read, created_at) VALUES (NULL, ?, ?, ?, ?, 0, NOW())");
    $stmt->bind_param("sssi", $title, $message, $type, $related_id);
} else {
    $stmt = $conn->prepare("INSERT INTO notifications (mobile, title, message, type, related_id, is_read, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())");
    $stmt->bind_param("ssssi", $mobile, $title, $message, $type, $related_id);
}

if (!$stmt) {
    echo json_encode(["success" => false, "message" => $conn->error]);
    exit;
}

$ok = $stmt->execute();
$insert_id = $stmt->insert_id;
$stmt->close();

if ($ok) {
    echo json_encode(["success" => true, "id" => $insert_id]);
} else {
    echo json_encode(["success" => false, "message" => "DB insert failed"]);
}

$conn->close();
