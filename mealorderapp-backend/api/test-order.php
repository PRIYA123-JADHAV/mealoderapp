<?php
include '../db.php';

// Dummy values (you can adjust as needed)
$user_id = 1;
$total_amount = 250;
$payment_method = "Cash on Delivery";
$status = "pending";

// Insert into orders table
$order_sql = "INSERT INTO orders (user_id, total_amount, payment_method, status)
              VALUES ('$user_id', '$total_amount', '$payment_method', '$status')";

if ($conn->query($order_sql)) {
    $order_id = $conn->insert_id;

    // Dummy ordered items
    $items = [
        ["name" => "Paneer Tikka", "quantity" => 2, "price" => 100],
        ["name" => "Butter Naan", "quantity" => 1, "price" => 50]
    ];

    foreach ($items as $item) {
        $item_name = $item['name'];
        $item_qty = $item['quantity'];
        $item_price = $item['price'];

        $item_sql = "INSERT INTO order_items (order_id, dish_name, quantity, price)
                     VALUES ('$order_id', '$item_name', '$item_qty', '$item_price')";
        $conn->query($item_sql);
    }

    echo "✅ Test order inserted successfully!";
} else {
    echo "❌ Failed to insert order: " . $conn->error;
}
?>
