<?php
include '../db.php';

header('Content-Type: application/json');

$mobile = $_GET['mobile'] ?? '';

if ($mobile === '') {
    echo json_encode([
        "success" => false,
        "message" => "Mobile number is required"
    ]);
    exit;
}

// Fetch notifications for this user OR global (mobile NULL)
$sql = "SELECT * FROM notifications 
        WHERE mobile = ? OR mobile IS NULL 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $notifications
]);

$stmt->close();
$conn->close();
?>
