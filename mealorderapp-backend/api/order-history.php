<?php
header("Content-Type: application/json");
include '../db.php';

if (!empty($_GET['mobile'])) {
    $mobile = $conn->real_escape_string($_GET['mobile']);

    $res = $conn->query("SELECT id FROM users WHERE mobile='$mobile'");
    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $user_id = $user['id'];

        $result = $conn->query("SELECT * FROM orders WHERE user_id='$user_id' ORDER BY created_at DESC");
        $orders = [];

        while ($row = $result->fetch_assoc()) {
            $row['items'] = [];
            $items = $conn->query("SELECT * FROM order_items WHERE order_id='{$row['id']}'");
            while ($i = $items->fetch_assoc()) {
                $row['items'][] = $i;
            }
            $orders[] = $row;
        }

        echo json_encode(["success"=>true, "data"=>$orders]);
    } else {
        echo json_encode(["success"=>false, "message"=>"User not found"]);
    }
} else {
    echo json_encode(["success"=>false, "message"=>"Mobile number required"]);
}
?>
