<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle OPTIONS preflight
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit;
}

// Read JSON input OR form-data
$rawInput = file_get_contents("php://input");
$decoded = json_decode($rawInput, true);

$mobile = $decoded["mobile"] ?? $_POST["mobile"] ?? "";
$name   = $decoded["name"] ?? $_POST["name"] ?? "";
$otp    = $decoded["otp"] ?? $_POST["otp"] ?? "";

if (empty($mobile) || empty($otp)) {
    echo json_encode(["success" => false, "message" => "Mobile and OTP are required"]);
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "mealorderapp");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Step 1: Check OTP
$stmt = $conn->prepare("SELECT otp_code FROM otp_table WHERE mobile=? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($row["otp_code"] == $otp) {

        // Step 2: Check if user exists
        $checkUser = $conn->prepare("SELECT id, name, email, address FROM users WHERE mobile=?");
        $checkUser->bind_param("s", $mobile);
        $checkUser->execute();
        $checkResult = $checkUser->get_result();

        if ($checkResult->num_rows === 0) {
            // New user → create record
            $insert = $conn->prepare("INSERT INTO users (name, mobile) VALUES (?, ?)");
            $insert->bind_param("ss", $name, $mobile);
            $insert->execute();
            $insert->close();

            $userId = $conn->insert_id;
            $userData = ["name" => $name, "email" => "", "address" => ""];
            $needProfile = true;
        } else {
            // Existing user
            $userData = $checkResult->fetch_assoc();
            $userId = $userData['id'];

            // Check profile completeness
            $needProfile = empty($userData['name']) || empty($userData['email']) || empty($userData['address']);
        }

        // ✅ Always return a valid name
        $finalName = !empty($userData['name']) ? $userData['name'] : $name;

        echo json_encode([
            "success"      => true,
            "message"      => "OTP verified successfully",
            "user_id"      => $userId,
            "mobile"       => $mobile,
            "name"         => $finalName,
            "email"        => $userData['email']   ?? "",
            "address"      => $userData['address'] ?? "",
            "need_profile" => $needProfile
        ]);

    } else {
        echo json_encode(["success" => false, "message" => "Invalid OTP"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No OTP found for this number"]);
}

$stmt->close();
$conn->close();
