<?php
header("Content-Type: application/json");
include '../db.php';

if (!empty($_GET['order_id'])) {
    $order_id = $conn->real_escape_string($_GET['order_id']);
    $result = $conn->query("SELECT * FROM orders WHERE id='$order_id'");

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        echo json_encode(["success"=>true, "data"=>$order]);
    } else {
        echo json_encode(["success"=>false, "message"=>"Order not found"]);
    }
} else {
    echo json_encode(["success"=>false, "message"=>"Order ID required"]);
}
?>
