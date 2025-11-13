<?php
// =======================
// Allow Frontend Access
// =======================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit;
}

// =======================
// Get Input
// =======================
$rawInput = file_get_contents("php://input");
parse_str($rawInput, $postData);
$mobile = $postData["mobile"] ?? $_POST["mobile"] ?? "";

// Validate mobile
if (empty($mobile)) {
    echo json_encode(["success" => false, "message" => "Mobile number required"]);
    exit;
}

// =======================
// Generate OTP
// =======================
$otp = rand(1000, 9999);

// =======================
// Connect Database
// =======================
$conn = new mysqli("localhost", "root", "", "mealorderapp");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS otp_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mobile VARCHAR(15) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert OTP
$stmt = $conn->prepare("INSERT INTO otp_table (mobile, otp_code, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("ss", $mobile, $otp);
$stmt->execute();
$stmt->close();
$conn->close();

// =======================
// Send Response
// =======================
echo json_encode([
    "success" => true,
    "message" => "✅ OTP sent to $mobile. Your OTP is $otp",
    "otp" => $otp
]);
exit;
?>
