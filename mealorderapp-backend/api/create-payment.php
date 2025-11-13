<?php
include '../db.php'; 
header('Content-Type: application/json');

// Read JSON body
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!$data || empty($data['user_id']) || empty($data['items']) || empty($data['total_amount']) || empty($data['payment_method'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$user_id = intval($data['user_id']);
$items = $data['items']; // array of { dish_id, quantity, price }
$total_amount = floatval($data['total_amount']);
$payment_method = mysqli_real_escape_string($conn, $data['payment_method']);

// ✅ Step 1: Insert into orders table
$sql_order = "INSERT INTO orders (user_id, total_amount, order_date) VALUES ($user_id, $total_amount, NOW())";
if (!mysqli_query($conn, $sql_order)) {
    echo json_encode(["success" => false, "message" => "Order insert failed"]);
    exit;
}

// ✅ Step 2: Get the last inserted order ID
$order_id = mysqli_insert_id($conn);

// ✅ Step 3: Insert order items
foreach ($items as $item) {
    $dish_id = intval($item['dish_id']);
    $quantity = intval($item['quantity']);
    $price = floatval($item['price']);

    mysqli_query($conn, "INSERT INTO order_items (order_id, dish_id, quantity, price) 
                         VALUES ($order_id, $dish_id, $quantity, $price)");
}

// ✅ Step 4: Determine payment status
$payment_status = ($payment_method === 'COD') ? 'Pending' : 'Paid';

// ✅ Step 5: Insert payment record
$sql_payment = "INSERT INTO payments (order_id, user_id, total_amount, payment_method, payment_status, payment_date) 
                VALUES ($order_id, $user_id, $total_amount, '$payment_method', '$payment_status', NOW())";

if (!mysqli_query($conn, $sql_payment)) {
    echo json_encode(["success" => false, "message" => "Payment insert failed"]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Order placed and payment recorded successfully",
    "order_id" => $order_id
]);
?>
