<?php
session_start();
header('Content-Type: application/json');
include(__DIR__ . '/../config/db.php');

if (!isset($_SESSION['admin'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if ($payment_id <= 0 || !in_array($status, ['Paid', 'Pending'])) {
    echo json_encode(["success" => false, "message" => "Missing or invalid parameters"]);
    exit;
}

// ✅ Update payment status
$sql = "UPDATE payments SET payment_status = ?, payment_date = NOW() WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $payment_id);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "DB Error: " . $stmt->error]);
    $stmt->close();
    exit;
}

// ✅ If Paid, update order status
if ($status === 'Paid') {
    $order_sql = "UPDATE orders o 
                  JOIN payments p ON o.id = p.order_id 
                  SET o.status = 'paid' 
                  WHERE p.id = ?";
    $stmt2 = $conn->prepare($order_sql);
    $stmt2->bind_param("i", $payment_id);
    $stmt2->execute();
    $stmt2->close();
}

$stmt->close();
echo json_encode(["success" => true, "message" => "Payment updated successfully"]);
?>
