<?php
header("Content-Type: application/json");

include '../db.php';
$result = $conn->query("SELECT * FROM deals WHERE valid_until >= CURDATE()");
$deals = [];

while ($row = $result->fetch_assoc()) {
    $deals[] = $row;
}

echo json_encode(["success"=>true, "data"=>$deals]);
?>
