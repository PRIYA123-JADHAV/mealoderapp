<?php
session_start();
header('Content-Type: application/json');
include(__DIR__ . '/../config/db.php'); // DB connection

if (!isset($_SESSION['admin'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
$status     = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($payment_id <= 0 || !in_array($status, ['Paid', 'Pending'])) {
    echo json_encode(["success" => false, "message" => "Missing or invalid parameters"]);
    exit();
}

// ✅ Step 1: Update payment status
$sql = "UPDATE payments SET payment_status = ?, payment_date = NOW() WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $payment_id);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "DB Error: " . $stmt->error]);
    $stmt->close();
    exit();
}
$stmt->close();

// ✅ Step 2: If payment is marked as Paid, update orders table too
if ($status === 'Paid') {
    $sql2 = "UPDATE orders o
             JOIN payments p ON o.id = p.order_id
             SET o.status = 'paid'
             WHERE p.id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $payment_id);
    $stmt2->execute();
    $stmt2->close();
}

echo json_encode(["success" => true, "message" => "Payment and order updated successfully"]);
?>
