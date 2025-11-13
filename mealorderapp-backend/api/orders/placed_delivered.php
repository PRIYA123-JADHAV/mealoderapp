<?php
header("Content-Type: application/json");
include_once("../../db.php");

$query = "SELECT * FROM orders WHERE status IN ('placed','processing','shipped','delivered') ORDER BY created_at DESC";
$result = $conn->query($query);

$orders = [];
while($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
echo json_encode($orders);
?>
