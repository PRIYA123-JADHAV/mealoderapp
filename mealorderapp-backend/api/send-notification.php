<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

include '../db.php';

$mobile  = trim($_POST['mobile'] ?? '');
$title   = trim($_POST['title'] ?? '');
$message = trim($_POST['message'] ?? '');
$type    = trim($_POST['type'] ?? 'system');

if ($title === '' || $message === '') {
    echo json_encode(["success" => false, "message" => "Title and message are required"]);
    exit;
}

// Case 1: Broadcast (no mobile or 0)
if ($mobile === '' || $mobile === '0') {
    $stmt = $conn->prepare("
        INSERT INTO notifications (mobile, title, message, type, related_id, is_read, created_at)
        VALUES (NULL, ?, ?, ?, NULL, 0, NOW())
    ");
    $stmt->bind_param("sss", $title, $message, $type);
    $success = $stmt->execute();
    $stmt->close();
} 
else {
    // Case 2: Multiple mobiles
    $mobiles = array_map('trim', explode(',', $mobile));
    $success = true;

    $stmt = $conn->prepare("
        INSERT INTO notifications (mobile, title, message, type, related_id, is_read, created_at)
        VALUES (?, ?, ?, ?, NULL, 0, NOW())
    ");

    foreach ($mobiles as $m) {
        if ($m !== '') {
            $stmt->bind_param("ssss", $m, $title, $message, $type);
            if (!$stmt->execute()) {
                $success = false;
                break;
            }
        }
    }
    $stmt->close();
}

if ($success) {
    echo json_encode(["success" => true, "message" => "Notification(s) sent successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to send some notifications"]);
}

$conn->close();
?>
