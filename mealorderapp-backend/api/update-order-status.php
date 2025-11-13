<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") exit;

include '../db.php';

$order_id = $_POST['order_id'] ?? '';
$status   = $_POST['status'] ?? '';

if (!$order_id || !$status) {
    echo json_encode(["success" => false, "message" => "Order ID and status are required"]);
    exit;
}

// 1️⃣ Get user ID for this order
$stmt = $conn->prepare("SELECT user_id FROM orders WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Order not found"]);
    exit;
}
$user = $res->fetch_assoc();
$user_id = $user['user_id'];
$stmt->close();

// 2️⃣ Update order status
$update = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$update->bind_param("si", $status, $order_id);
$update->execute();
$update->close();

// 3️⃣ Insert notification for this user
$title = "Order Status Updated";
$message = "Your order #$order_id status is now: $status";
$type = "order";

$notif = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
$notif->bind_param("isss", $user_id, $title, $message, $type);
$notif->execute();
$notif->close();

echo json_encode(["success" => true, "message" => "Order status updated and notification sent"]);
?>
