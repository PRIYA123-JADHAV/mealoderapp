<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// include DB connection
include '../db.php'; // make sure path is correct

$response = ["success" => false, "message" => "Something went wrong!"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';

    if ($user_id && $name && $email && $address) {
        // prepare statement
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, address=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $address, $user_id);

        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Profile updated successfully!";
        } else {
            $response["message"] = "Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response["message"] = "Missing required fields!";
    }
}

echo json_encode($response);
