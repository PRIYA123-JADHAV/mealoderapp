<?php
include(__DIR__ . '/../config/db.php');


header('Content-Type: application/json');

// ✅ Accept POST form data instead of JSON
$order_id = $_POST['order_id'] ?? null;
$status   = $_POST['status'] ?? null;

if (!$order_id || !$status) {
    echo json_encode(["success" => false, "message" => "Missing order_id or status"]);
    exit;
}

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Order status updated"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update status"]);
}
?>
