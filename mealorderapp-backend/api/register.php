<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $address = $_POST['address'];
    $mobile  = $_POST['mobile'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, address, mobile, created_at) 
                            VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $address, $mobile);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User registered successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
