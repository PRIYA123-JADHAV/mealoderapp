<?php
include '../db.php';

header('Content-Type: application/json');

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$address = $data['address'] ?? '';
$mobile = $data['mobile'] ?? '';

// Validation
if(empty($name) || empty($email) || empty($address) || empty($mobile)){
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit;
}

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE mobile = ?");
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    // Update existing user profile
    $row = $result->fetch_assoc();
    $userId = $row['id'];

    $update = $conn->prepare("UPDATE users SET name=?, email=?, address=? WHERE id=?");
    $update->bind_param("sssi", $name, $email, $address, $userId);

    if($update->execute()){
        echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database update failed"]);
    }
} else {
    // Insert new user (fallback case)
    $insert = $conn->prepare("INSERT INTO users (name, email, address, mobile) VALUES (?,?,?,?)");
    $insert->bind_param("ssss", $name, $email, $address, $mobile);

    if($insert->execute()){
        echo json_encode(["success" => true, "message" => "Profile created successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database insert failed"]);
    }
}
