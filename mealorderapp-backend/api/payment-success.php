<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (
    !$data || 
    empty($data['order_id']) || 
    empty($data['payment_status']) || 
    empty($data['payment_method'])
) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$order_id = intval($data['order_id']);
$razorpay_payment_id = !empty($data['razorpay_payment_id']) ? mysqli_real_escape_string($conn, $data['razorpay_payment_id']) : null;
$payment_status = mysqli_real_escape_string($conn, $data['payment_status']);
$payment_method = mysqli_real_escape_string($conn, $data['payment_method']);

// First check if payment exists
$check = $conn->prepare("SELECT id FROM payments WHERE order_id=?");
$check->bind_param("i", $order_id);
$check->execute();
$result = $check->get_result();
$exists = $result->num_rows > 0;
$check->close();

if ($exists) {
    // Update payment
    $stmt = $conn->prepare("
        UPDATE payments 
        SET payment_status=?, payment_method=?, razorpay_payment_id=?, payment_date=NOW() 
        WHERE order_id=?
    ");
    $stmt->bind_param("sssi", $payment_status, $payment_method, $razorpay_payment_id, $order_id);
} else {
    // Insert payment (fallback)
    $stmt = $conn->prepare("
        INSERT INTO payments (order_id, user_id, total_amount, payment_method, payment_status, payment_date, razorpay_payment_id)
        SELECT id, user_id, total_amount, ?, ?, NOW(), ? FROM orders WHERE id=?
    ");
    $stmt->bind_param("sssi", $payment_method, $payment_status, $razorpay_payment_id, $order_id);
}

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "DB Error: " . $stmt->error]);
    exit;
}
$stmt->close();

// Update order
$order_status = ($payment_status === 'Paid') ? 'paid' : 'pending';
$stmt2 = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt2->bind_param("si", $order_status, $order_id);
$stmt2->execute();
$stmt2->close();

echo json_encode(["success" => true, "message" => "Payment updated successfully"]);
$conn->close();
?>
