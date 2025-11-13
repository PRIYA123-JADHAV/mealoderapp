<?php
header("Content-Type: application/json");
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $order_id = $_POST['order_id'] ?? null;
    $status = $_POST['status'] ?? null;

    // Allowed statuses
    $valid_status = ['placed','processing','shipped','delivered','canceled'];

    if (!$order_id || !$status || !in_array($status, $valid_status)) {
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Order status updated to $status"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update status"]);
    }
}
?>
