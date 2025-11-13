<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");
include '../db.php';

$response = ["success" => false, "data" => []];

// Validate user_id from query param
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id']) || intval($_GET['user_id']) <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid or missing User ID"]);
    exit();
}

$user_id = intval($_GET['user_id']);

try {
    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT id, address FROM addresses WHERE user_id = ? ORDER BY id DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $addresses = [];

    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row;
    }

    $response["success"] = true;
    $response["data"] = $addresses;

    $stmt->close();
} catch (Exception $e) {
    $response["success"] = false;
    $response["message"] = "Database error: " . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>
