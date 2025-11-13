<?php
include(__DIR__ . '/../config/db.php');
header('Content-Type: application/json');

$sql = "SELECT p.id AS payment_id, p.order_id, p.user_id, u.name AS customer_name,
        p.total_amount, p.payment_method, p.payment_status, p.payment_date
        FROM payments p
        LEFT JOIN users u ON p.user_id = u.id
        ORDER BY p.payment_date DESC";

$result = $conn->query($sql);
$payments = [];
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}

echo json_encode(["success" => true, "data" => $payments]);
?>
