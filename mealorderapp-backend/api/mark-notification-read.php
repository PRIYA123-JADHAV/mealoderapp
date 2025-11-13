<?php
// mealorderapp-backend/api/mark-notification-read.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

include '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

// Normalize mobile number function
function normalizeMobile($mobile) {
    $mobile = preg_replace('/\D/', '', $mobile); // only digits
    if (strlen($mobile) === 12 && substr($mobile, 0, 2) === "91") {
        $mobile = substr($mobile, 2); // remove country code
    }
    return $mobile;
}

// Mark single notification by ID
if (isset($data['id'])) {
    $id = intval($data['id']);
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    $stmt->close();
    echo json_encode(["success" => (bool)$ok]);
    exit;
}

// Mark all notifications for a mobile number
if (isset($data['mobile']) && isset($data['mark_all']) && $data['mark_all']) {
    $mobile = normalizeMobile($data['mobile']);
    $stmt = $conn->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE (mobile = ? OR mobile IS NULL OR mobile = '')
    ");
    $stmt->bind_param("s", $mobile);
    $ok = $stmt->execute();
    $stmt->close();
    echo json_encode(["success" => (bool)$ok]);
    exit;
}

echo json_encode(["success" => false, "message" => "Missing parameters"]);
$conn->close();
?>
