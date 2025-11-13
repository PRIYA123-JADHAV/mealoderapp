<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include '../db.php';
header('Content-Type: application/json');

// 🔹 Read POST data
$order_id            = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$user_id             = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$amount              = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$payment_method      = isset($_POST['payment_method']) ? mysqli_real_escape_string($conn, $_POST['payment_method']) : '';
$razorpay_payment_id = isset($_POST['razorpay_payment_id']) ? mysqli_real_escape_string($conn, $_POST['razorpay_payment_id']) : '';

if ($order_id <= 0 || $user_id <= 0 || $amount <= 0 || empty($payment_method)) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

// 🔹 Check if payment already exists
$check = $conn->prepare("SELECT id FROM payments WHERE order_id = ?");
$check->bind_param("i", $order_id);
$check->execute();
$res = $check->get_result();
$exists = $res->num_rows > 0;
$check->close();

if ($exists) {
    // 🔹 Update existing payment
    $stmt = $conn->prepare("
        UPDATE payments 
        SET total_amount = ?, payment_method = ?, razorpay_payment_id = ?, payment_status = 'Paid', payment_date = NOW()
        WHERE order_id = ?
    ");
    $stmt->bind_param("dssi", $amount, $payment_method, $razorpay_payment_id, $order_id);
} else {
    // 🔹 Insert new payment
    $stmt = $conn->prepare("
        INSERT INTO payments (order_id, user_id, total_amount, payment_method, payment_status, razorpay_payment_id, payment_date)
        VALUES (?, ?, ?, ?, 'Paid', ?, NOW())
    ");
    $stmt->bind_param("iidss", $order_id, $user_id, $amount, $payment_method, $razorpay_payment_id);
}

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "DB Error: " . $stmt->error]);
    exit;
}
$stmt->close();

// 🔹 Update order status
$order_status = 'paid';
$stmt2 = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt2->bind_param("si", $order_status, $order_id);
$stmt2->execute();
$stmt2->close();

echo json_encode(["success" => true, "message" => "Payment saved successfully"]);
$conn->close();
?>
