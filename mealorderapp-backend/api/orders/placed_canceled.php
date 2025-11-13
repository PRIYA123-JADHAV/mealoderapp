<?php
include_once("../../db.php");
header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM orders WHERE status IN ('placed', 'canceled') ORDER BY created_at DESC");

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
$conn->close();
?>
