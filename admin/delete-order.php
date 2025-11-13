<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include(__DIR__ . '/../config/db.php');

if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']);

    $sql = "DELETE FROM orders WHERE id = $order_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: view-orders.php?msg=deleted");
        exit();
    } else {
        echo "Error deleting order: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
