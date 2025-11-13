<?php
include(__DIR__ . '/../config/db.php');

header('Content-Type: application/json');

$sql = "SELECT * FROM orders WHERE status = 'Placed' ORDER BY order_time DESC";
$result = $conn->query($sql);

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $orders
]);
?>
