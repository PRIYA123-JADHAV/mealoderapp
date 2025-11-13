<?php
include '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

// Debug log
file_put_contents("debug_order.log", print_r($data, true), FILE_APPEND);

if (!$data || empty($data['name']) || empty($data['mobile']) || empty($data['address']) || empty($data['payment_method']) || empty($data['total_amount']) || empty($data['items'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$name = $data['name'];
$mobile = $data['mobile'];
$address = $data['address'];
$payment_method = strtolower($data['payment_method']); 
$total_amount = floatval($data['total_amount']);
$items = is_array($data['items']) ? json_encode($data['items']) : $data['items']; 

// ✅ Always start with "placed"
$status = 'placed'; 
$payment_status = ($payment_method === 'cod') ? 'Pending' : 'Paid';

// Step 1: Find or create user
$user_id = null;
$stmt = $conn->prepare("SELECT id FROM users WHERE mobile = ?");
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['id'];
} else {
    $stmt = $conn->prepare("INSERT INTO users (name, mobile, address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $mobile, $address);
    $stmt->execute();
    $user_id = $stmt->insert_id;
}
$stmt->close();

// Step 2: Insert order
$stmt = $conn->prepare("INSERT INTO orders (user_id, name, mobile, address, total_amount, payment_method, items, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("isssdsss", $user_id, $name, $mobile, $address, $total_amount, $payment_method, $items, $status);
if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Order insert failed: " . $stmt->error]);
    exit;
}
$order_id = $stmt->insert_id;
$stmt->close();

// Step 3: Insert payment
$stmt2 = $conn->prepare("INSERT INTO payments (order_id, user_id, total_amount, payment_method, payment_status, payment_date) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt2->bind_param("iidss", $order_id, $user_id, $total_amount, $payment_method, $payment_status);
$stmt2->execute();
$stmt2->close();

echo json_encode(["success" => true, "message" => "Order placed and payment recorded successfully", "order_id" => $order_id]);
$conn->close();
?>
