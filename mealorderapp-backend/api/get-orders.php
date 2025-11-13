<?php
header('Content-Type: application/json');
// Database connection
include '../db.php'; 
if (!isset($_GET['mobile']) || empty($_GET['mobile'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Mobile number required',
        'orders' => []
    ]);
    exit;
}

$mobile = mysqli_real_escape_string($conn, $_GET['mobile']);

// ✅ Fetch user_id using mobile
$userQuery = mysqli_query($conn, "SELECT id FROM users WHERE mobile='$mobile' LIMIT 1");
if (!$userQuery || mysqli_num_rows($userQuery) === 0) {
    echo json_encode([
        'success' => true,
        'message' => 'No user found with this mobile',
        'orders' => []
    ]);
    exit;
}

$userRow = mysqli_fetch_assoc($userQuery);
$user_id = $userRow['id'];

// ✅ Fetch orders for this user_id
$sql = "SELECT id, total_amount, status, address, created_at 
        FROM orders 
        WHERE user_id='$user_id' 
        ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

$orders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = [
            'id' => $row['id'],
            'items' => '', // We'll fill this later
            'total_amount' => $row['total_amount'],
            'status' => $row['status'],
            'address' => $row['address'],
            'order_date' => $row['created_at']
        ];
    }

    // ✅ Fetch items for each order
    foreach ($orders as $key => $order) {
        $oid = $order['id'];
        $itemsQuery = mysqli_query($conn, "SELECT item_name, quantity FROM order_items WHERE order_id='$oid'");
        $items = [];
        while ($item = mysqli_fetch_assoc($itemsQuery)) {
            $items[] = $item['item_name'] . " (x" . $item['quantity'] . ")";
        }
        $orders[$key]['items'] = implode(", ", $items);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Orders fetched successfully',
        'orders' => $orders
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database query failed',
        'orders' => []
    ]);
}
?>
